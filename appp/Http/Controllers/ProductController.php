<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    public function index()
    {
        // Only products from wastes owned by the user
        $products = Product::whereHas('waste', function ($q) {
            $q->where('user_id', Auth::id());
        })->with('waste')->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        // User can only attach to their own wastes
        $wastes = Waste::where('user_id', Auth::id())->get();

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

        // Ownership check: selected waste must belong to current user
        $waste = Waste::where('id', $validated['waste_id'])->where('user_id', Auth::id())->first();
        if (! $waste) {
            abort(403);
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        if ($product->waste->user_id !== Auth::id()) {
            abort(403);
        }
        $product->load('waste');

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

        $product->update($validated);

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

    /**
     * Export products to Excel or CSV
     */
    public function export(Request $request)
    {
        // Build the same query as index method
        $query = Product::with(['waste.collectionPoint', 'waste.user']);

        // Apply all filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('quantite', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('quantite', '=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('quantite', '>', 0)->where('quantite', '<=', 10);
            }
        }

        if ($request->filled('waste_type')) {
            $query->whereHas('waste', function($q) use ($request) {
                $q->where('type', 'LIKE', "%{$request->waste_type}%");
            });
        }

        if ($request->filled('creator_id')) {
            $query->whereHas('waste', function($q) use ($request) {
                $q->where('user_id', $request->creator_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        
        $allowedSorts = ['nom', 'prix', 'quantite', 'created_at', 'etat'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Get export format
        $format = $request->input('format', 'xlsx');

        // Create export and download
        $export = new ProductsExport($query, $format);
        return $export->download();
    }
}
