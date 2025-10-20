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
use App\Exports\OrdersExport;

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

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['products', 'client'])->findOrFail($id);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.order', compact('order'));
        
        // Download with filename
        $filename = 'Invoice-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Email invoice to client
     */
    public function emailInvoice($id)
    {
        try {
            $order = Order::with(['products', 'client'])->findOrFail($id);

            if (!$order->client || !$order->client->email) {
                return back()->withErrors(['error' => 'Aucune adresse e-mail client trouvée pour cette commande.']);
            }

            // Generate PDF
            $pdf = Pdf::loadView('invoices.order', compact('order'));
            
            // Save PDF temporarily
            $filename = 'Invoice-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
            $tempPath = storage_path('app/temp/' . $filename);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $pdf->save($tempPath);

            // Send email with attachment
            Mail::to($order->client->email)->send(new OrderInvoiceMail($order, $tempPath));

            // Delete temporary file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return back()->with('success', 'La facture a été envoyée avec succès à ' . $order->client->email);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'envoi de l\'e-mail: ' . $e->getMessage()]);
        }
    }

    /**
     * Export orders to Excel or CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['products', 'client']);

        // Apply same filters as index method (if available)
        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }

        if ($request->filled('client_name')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client_name . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('statut', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('client_id') && $request->client_id !== 'all') {
            $query->where('client_id', $request->client_id);
        }

        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Export format (xlsx or csv)
        $format = $request->get('format', 'xlsx');
        
        $exporter = new OrdersExport($query, $format);
        return $exporter->download();
    }
}
