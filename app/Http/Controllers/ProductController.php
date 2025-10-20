<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Waste;
use App\Models\User;
use App\Notifications\LowStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Start with base query
        $query = Product::with(['waste.collectionPoint', 'waste.user']);

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('prix', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('prix', '<=', $request->max_price);
        }

        // Filter by status (état)
        if ($request->filled('status')) {
            $query->where('etat', $request->status);
        }

        // Filter by stock level
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'in_stock':
                    $query->where('quantite', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantite', [1, 10])
                          ->orWhereRaw('quantite <= stock_threshold AND quantite > 0');
                    break;
                case 'out_of_stock':
                    $query->where('quantite', 0);
                    break;
            }
        }

        // Get filtered products
        $products = $query->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        // Collectors can create products from any waste
        // Regular users can create products from their own wastes
        if (Auth::user()->role === 'collector') {
            $wastes = Waste::all();
        } else {
            $wastes = Waste::where('user_id', Auth::id())->get();
        }

        return view('products.create', compact('wastes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'waste_id' => 'required|exists:wastes,id',
            'nom' => 'required|string',
            'description' => 'required|string',
            'etat' => 'required|in:recyclé,non recyclé',
            'prix' => 'required|numeric|min:100',
            'quantite' => 'required|integer|min:0',
        ]);

        // Collectors can create products from any waste
        // Regular users can only create from their own wastes
        if (Auth::user()->role !== 'collector') {
            $waste = Waste::where('id', $validated['waste_id'])->where('user_id', Auth::id())->first();
            if (! $waste) {
                abort(403, 'You can only create products from your own wastes.');
            }
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        // Products are public in the marketplace - anyone can view them
        $product->load('waste.collectionPoint', 'waste.user');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->waste->user_id !== Auth::id()) {
            abort(403);
        }
        $wastes = Waste::where('user_id', Auth::id())->get();

        return view('products.edit', compact('product', 'wastes'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->waste->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'waste_id' => 'required|exists:wastes,id',
            'nom' => 'required|string',
            'description' => 'required|string',
            'etat' => 'required|in:recyclé,non recyclé',
            'prix' => 'required|numeric|min:100',
            'quantite' => 'required|integer|min:0',
        ]);

        // Ownership check for selected waste
        $waste = Waste::where('id', $validated['waste_id'])->where('user_id', Auth::id())->first();
        if (! $waste) {
            abort(403);
        }

        // Store old quantity to check if stock decreased
        $oldQuantity = $product->quantite;

        $product->update($validated);

        // Check if stock is now low and notify admins
        $newQuantity = $validated['quantite'];
        $threshold = $product->stock_threshold ?? 10;
        
        if ($newQuantity <= $threshold && $newQuantity > 0 && $oldQuantity > $threshold) {
            // Stock just dropped below threshold - notify admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new LowStockAlert($product, $newQuantity));
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->waste->user_id !== Auth::id()) {
            abort(403);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
