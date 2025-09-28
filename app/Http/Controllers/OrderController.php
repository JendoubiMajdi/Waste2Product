<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

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
        $products = Product::whereNull('order_id')->get();
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
            'client_id' => 'required|exists:users,id',
        ]);

        $order = Order::create([
            'date' => now()->toDateString(),
            'statut' => $validated['statut'],
            'client_id' => $validated['client_id'],
        ]);
        Product::whereIn('id', $validated['products'])->update(['order_id' => $order->id]);
        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with('products')->findOrFail($id);
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
