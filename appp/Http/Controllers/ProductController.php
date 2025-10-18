<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
