<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;

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
            if (!is_numeric($q) || (int)$q < 1) {
                return back()->withErrors(["quantites.$pid" => 'Quantité invalide'])->withInput();
            }
            $requested[$pid] = (int)$q;
        }

        // Validate stock
        $products = Product::whereIn('id', array_keys($requested))->get()->keyBy('id');
        foreach ($requested as $pid => $qty) {
            if (!isset($products[$pid])) {
                return back()->withErrors(['products' => 'Produit introuvable'])->withInput();
            }
            if ($qty > $products[$pid]->quantite) {
                return back()->withErrors(["quantites.$pid" => "Quantité demandée ({$qty}) dépasse le stock disponible ({$products[$pid]->quantite})"])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($validated, $requested, $products) {
                // Create the order
                $order = Order::create([
                    'date' => now()->toDateString(),
                    'statut' => $validated['statut'],
                    'client_id' => $validated['client_id'],
                    'total_amount' => 0, // Will be calculated
                ]);

                $totalAmount = 0;

                // Attach items with quantities, prices, and decrease stock
                $attachData = [];
                foreach ($requested as $pid => $qty) {
                    $product = $products[$pid];
                    $unitPrice = $product->prix;
                    $subtotal = $unitPrice * $qty;
                    $totalAmount += $subtotal;

                    $attachData[$pid] = [
                        'quantite' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];

                    // Decrease stock using the model method
                    $product->decreaseStock($qty);
                }

                $order->products()->attach($attachData);

                // Update total amount
                $order->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('orders.index')->with('success', 'Order created successfully with total calculated and stock updated.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
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
        // Get all products (stock management handles availability)
        $products = Product::all();
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
            'quantites' => 'required|array',
            'client_id' => 'required|exists:users,id',
        ]);

        $order = Order::with('products')->findOrFail($id);

        // Build requested quantities
        $requested = [];
        foreach ($validated['products'] as $pid) {
            $q = $validated['quantites'][$pid] ?? null;
            if (!is_numeric($q) || (int)$q < 1) {
                return back()->withErrors(["quantites.$pid" => 'Quantité invalide'])->withInput();
            }
            $requested[$pid] = (int)$q;
        }

        // Get old order items to restore stock
        $oldItems = $order->products->keyBy('id');

        // Get new products
        $newProducts = Product::whereIn('id', array_keys($requested))->get()->keyBy('id');

        // Validate stock for new/updated items
        foreach ($requested as $pid => $qty) {
            $product = $newProducts[$pid] ?? null;
            if (!$product) {
                return back()->withErrors(['products' => 'Produit introuvable'])->withInput();
            }

            // Calculate available stock (current stock + old quantity if updating existing item)
            $oldQty = $oldItems->has($pid) ? $oldItems[$pid]->pivot->quantite : 0;
            $availableStock = $product->quantite + $oldQty;

            if ($qty > $availableStock) {
                return back()->withErrors(["quantites.$pid" => "Stock insuffisant. Disponible: {$availableStock}"])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($order, $validated, $requested, $oldItems, $newProducts) {
                // Restore stock for removed or updated items
                foreach ($oldItems as $oldProduct) {
                    $oldQty = $oldProduct->pivot->quantite;
                    $oldProduct->increaseStock($oldQty);
                }

                // Detach all old products
                $order->products()->detach();

                $totalAmount = 0;

                // Attach new products with updated quantities and prices
                $attachData = [];
                foreach ($requested as $pid => $qty) {
                    $product = $newProducts[$pid];
                    $unitPrice = $product->prix;
                    $subtotal = $unitPrice * $qty;
                    $totalAmount += $subtotal;

                    $attachData[$pid] = [
                        'quantite' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];

                    // Decrease stock
                    $product->decreaseStock($qty);
                }

                $order->products()->attach($attachData);

                // Update order
                $order->update([
                    'statut' => $validated['statut'],
                    'client_id' => $validated['client_id'],
                    'total_amount' => $totalAmount,
                ]);
            });

            return redirect()->route('orders.index')->with('success', 'Order updated successfully with stock adjusted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::with('products')->findOrFail($id);

        try {
            DB::transaction(function () use ($order) {
                // Restore stock for all products in this order
                foreach ($order->products as $product) {
                    $qty = $product->pivot->quantite;
                    $product->increaseStock($qty);
                }

                // Delete the order (cascade will delete order_items)
                $order->delete();
            });

            return redirect()->route('orders.index')->with('success', 'Order deleted successfully and stock restored.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete order: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate and download PDF invoice for an order
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['products', 'client'])->findOrFail($id);
        
        // Generate PDF
        $pdf = Pdf::loadView('invoices.order', compact('order'));
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Download the PDF
        $fileName = 'Invoice-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Email invoice to client
     */
    public function emailInvoice($id)
    {
        $order = Order::with(['products', 'client'])->findOrFail($id);
        
        if (!$order->client || !$order->client->email) {
            return back()->withErrors(['error' => 'No client email address found for this order.']);
        }
        
        try {
            // Generate PDF
            $pdf = Pdf::loadView('invoices.order', compact('order'));
            $pdf->setPaper('A4', 'portrait');
            
            // Save PDF temporarily
            $fileName = 'Invoice-' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
            $pdfPath = storage_path('app/temp/' . $fileName);
            
            // Create temp directory if it doesn't exist
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $pdf->save($pdfPath);
            
            // Send email with PDF attachment
            Mail::to($order->client->email)->send(new OrderInvoiceMail($order, $pdfPath));
            
            // Delete temporary PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            return back()->with('success', 'Invoice sent successfully to ' . $order->client->email);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send invoice: ' . $e->getMessage()]);
        }
    }
}
