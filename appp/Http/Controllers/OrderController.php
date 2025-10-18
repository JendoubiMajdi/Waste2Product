<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['products', 'client'])->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // List only products with stock available
        $products = Product::where('quantite', '>', 0)->get();
        $clients = User::all();

        return view('orders.create', compact('products', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'statut' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'quantites' => 'required|array',
            'client_id' => 'required|exists:users,id',
        ]);

        // Build requested quantities keyed by product id
        $requested = [];
        foreach ($validated['products'] as $pid) {
            $q = $validated['quantites'][$pid] ?? null;
            if (! is_numeric($q) || (int) $q < 1) {
                return back()->withErrors(["quantites.$pid" => 'Quantité invalide'])->withInput();
            }
            $requested[$pid] = (int) $q;
        }

        // Validate stock
        $products = Product::whereIn('id', array_keys($requested))->get()->keyBy('id');
        foreach ($requested as $pid => $qty) {
            if (! isset($products[$pid])) {
                return back()->withErrors(['products' => 'Produit introuvable'])->withInput();
            }
            if ($qty > $products[$pid]->quantite) {
                return back()->withErrors(["quantites.$pid" => 'Quantité demandée dépasse le stock disponible'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $requested, $products) {
            $order = Order::create([
                'date' => now()->toDateString(),
                'statut' => $validated['statut'],
                'client_id' => $validated['client_id'],
            ]);

            // Attach items with quantities and decrement stock
            $attachData = [];
            foreach ($requested as $pid => $qty) {
                $attachData[$pid] = ['quantite' => $qty];
                // decrement stock
                $product = $products[$pid];
                $product->decrement('quantite', $qty);
            }
            $order->products()->attach($attachData);
        });

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['products' => function ($q) {
            $q->withPivot('quantite');
        }])->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::with('products')->findOrFail($id);
        $products = Product::whereNull('order_id')->orWhere('order_id', $order->id)->get();
        $clients = User::all();

        return view('orders.edit', compact('order', 'products', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'statut' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'client_id' => 'required|exists:users,id',
        ]);
        $order = Order::findOrFail($id);
        $order->update([
            'statut' => $validated['statut'],
            'client_id' => $validated['client_id'],
        ]);
        // Unassign all products from this order
        Product::where('order_id', $order->id)->update(['order_id' => null]);
        // Assign selected products
        Product::whereIn('id', $validated['products'])->update(['order_id' => $order->id]);

        return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        // Unassign products
        Product::where('order_id', $order->id)->update(['order_id' => null]);
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
