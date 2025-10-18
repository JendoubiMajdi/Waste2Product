<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Livraison;

class LivraisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $livraisons = Livraison::all();
        return view('livraisons.index', compact('livraisons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('livraisons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idOrder' => 'required|integer',
            'idClient' => 'required|integer',
            'adresseLivraison' => 'required|string|min:1',
            'dateLivraison' => 'required|date|after:today',
            'statut' => 'required|string',
        ]);
        
        // Create the delivery
        $livraison = Livraison::create($validated);
        
        // Update the order with transporter and status
        $order = \App\Models\Order::find($validated['idOrder']);
        if ($order) {
            // Calculate estimated delivery time (example: 2-3 days from acceptance)
            $estimatedTime = \Carbon\Carbon::parse($validated['dateLivraison'])->setTime(14, 0); // 2 PM on delivery date
            
            $order->update([
                'statut' => 'in_delivery',
                'transporter_id' => auth()->id(), // Assign current transporter
                'estimated_delivery_time' => $estimatedTime,
            ]);
        }
        
        return redirect()->route('orders.index')->with('success', 'Delivery accepted successfully. You are now assigned as the transporter.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $livraison = Livraison::findOrFail($id);
        return view('livraisons.show', compact('livraison'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $livraison = Livraison::findOrFail($id);
        return view('livraisons.edit', compact('livraison'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $livraison = Livraison::findOrFail($id);
        $validated = $request->validate([
            'idOrder' => 'required|integer',
            'idClient' => 'required|integer',
            'adresseLivraison' => 'required|string',
            'dateLivraison' => 'required|date',
            'statut' => 'required|string',
        ]);
        
        // Update the delivery
        $livraison->update($validated);
        
        // Update the order status if delivery is marked as delivered
        if ($validated['statut'] === 'delivered') {
            $order = \App\Models\Order::find($validated['idOrder']);
            if ($order) {
                $order->update(['statut' => 'delivered']);
            }
        }
        
        return redirect()->route('livraisons.index')->with('success', 'Livraison mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $livraison = Livraison::findOrFail($id);
        $livraison->delete();
        return redirect()->route('livraisons.index')->with('success', 'Livraison supprimée avec succès.');
    }
}
