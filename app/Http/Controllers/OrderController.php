<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoiceMail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['products', 'client', 'transporter']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('statut', $request->status);
        }

        $orders = $query->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // List only products with stock available
        $products = Product::where('quantite', '>', 0)->get();

        return view('orders.create', compact('products'));
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
            'delivery_address' => 'required|string|max:500',
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
                'client_id' => auth()->id(), // Automatically set to authenticated user
                'delivery_address' => $validated['delivery_address'],
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
        $order = Order::with([
            'products' => function ($q) {
                $q->withPivot('quantite');
            },
            'client',
            'transporter',
            'collectionPoint',
        ])->findOrFail($id);

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
     * Display the authenticated user's orders with tracking information.
     */
    public function myOrders()
    {
        $orders = Order::with(['products', 'transporter', 'collectionPoint', 'livraison'])
            ->where('client_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.my-orders', compact('orders'));
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

    /**
     * Download order invoice as PDF
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['products', 'client'])->findOrFail($id);
        
        // Calculate total
        $total = $order->products->sum(function($product) {
            return $product->prix * $product->pivot->quantite;
        });
        
        $pdf = Pdf::loadView('invoices.order', compact('order', 'total'));
        
        return $pdf->download('invoice-' . $order->id . '.pdf');
    }

    /**
     * Email order invoice as PDF
     */
    public function emailInvoice($id)
    {
        $order = Order::with(['products', 'client'])->findOrFail($id);
        
        if (!$order->client || !$order->client->email) {
            return redirect()->back()->with('error', 'Client email not found.');
        }
        
        // Calculate total
        $total = $order->products->sum(function($product) {
            return $product->prix * $product->pivot->quantite;
        });
        
        try {
            Mail::to($order->client->email)->send(new OrderInvoiceMail($order, $total));
            return redirect()->back()->with('success', 'Invoice sent successfully to ' . $order->client->email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    /**
     * Export orders to Excel/CSV
     */
    public function export()
    {
        $orders = Order::with(['products', 'client'])->get();
        
        // Create CSV content
        $csv = "Order ID,Date,Client,Status,Total Amount\n";
        
        foreach ($orders as $order) {
            $total = $order->products->sum(function($product) {
                return $product->prix * $product->pivot->quantite;
            });
            
            $csv .= implode(',', [
                $order->id,
                $order->date,
                $order->client ? $order->client->name : 'N/A',
                $order->statut,
                number_format($total, 2)
            ]) . "\n";
        }
        
        $fileName = 'orders-export-' . date('Y-m-d') . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
