<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use Illuminate\Http\Request;

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

    /**
     * Upload delivery proof (photo, signature, notes)
     */
    public function uploadProof(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        // Only assigned transporter can upload proof
        if (auth()->id() !== $livraison->livreur_id && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'You are not authorized to upload proof for this delivery.');
        }

        $validated = $request->validate([
            'delivery_proof_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'delivery_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        // Handle photo upload
        if ($request->hasFile('delivery_proof_photo')) {
            $photoPath = $request->file('delivery_proof_photo')->store('delivery_proofs', 'public');
            $validated['delivery_proof_photo'] = $photoPath;
        }

        // Handle signature upload
        if ($request->hasFile('delivery_signature')) {
            $signaturePath = $request->file('delivery_signature')->store('delivery_signatures', 'public');
            $validated['delivery_signature'] = $signaturePath;
        }

        $validated['proof_uploaded_at'] = now();

        $livraison->update($validated);

        return redirect()->back()->with('success', 'Delivery proof uploaded successfully!');
    }

    /**
     * Client confirms receipt of delivery
     */
    public function confirmReceipt(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        // Only the client can confirm receipt
        if (auth()->id() !== $livraison->idClient && auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'You are not authorized to confirm this delivery.');
        }

        $validated = $request->validate([
            'client_confirmation_notes' => 'nullable|string|max:500',
        ]);

        $livraison->update([
            'client_confirmed' => true,
            'client_confirmed_at' => now(),
            'client_confirmation_notes' => $validated['client_confirmation_notes'] ?? null,
            'statut' => 'delivered',
        ]);

        // Update order status
        $order = \App\Models\Order::find($livraison->idOrder);
        if ($order) {
            $order->update(['statut' => 'delivered']);
        }

        return redirect()->back()->with('success', 'Delivery confirmed successfully!');
    }

    /**
     * Mark delivery as delivered (for admin or transporter)
     */
    public function markAsDelivered($id)
    {
        $livraison = Livraison::findOrFail($id);

        // Only admin or assigned transporter can mark as delivered
        if (auth()->user()->role !== 'admin' && auth()->id() !== $livraison->livreur_id) {
            return redirect()->back()->with('error', 'You are not authorized to mark this delivery as delivered.');
        }

        $livraison->statut = 'delivered';
        $livraison->save();

        // Update order status
        $order = \App\Models\Order::find($livraison->idOrder);
        if ($order) {
            $order->statut = 'delivered';
            $order->save();
        }

        return redirect()->back()->with('success', 'Delivery marked as delivered successfully!');
    }
}
