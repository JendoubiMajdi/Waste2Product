<?php

namespace App\Http\Controllers;

use App\Models\Livraison;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Livraison::create($validated);

        return redirect()->route('livraisons.index')->with('success', 'Livraison créée avec succès.');
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
        $livraison->update($validated);

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
     * Upload delivery proof (photo/signature)
     */
    public function uploadProof(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        // Check if user is the assigned transporter
        if (Auth::id() !== $livraison->livreur_id) {
            return redirect()->back()->with('error', 'You are not authorized to upload proof for this delivery.');
        }

        $validated = $request->validate([
            'delivery_proof_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'delivery_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        // Upload photo
        if ($request->hasFile('delivery_proof_photo')) {
            $photoPath = $request->file('delivery_proof_photo')->store('delivery-proofs', 'public');
            $livraison->delivery_proof_photo = $photoPath;
        }

        // Upload signature
        if ($request->hasFile('delivery_signature')) {
            $signaturePath = $request->file('delivery_signature')->store('delivery-signatures', 'public');
            $livraison->delivery_signature = $signaturePath;
        }

        // Save notes
        if ($request->filled('delivery_notes')) {
            $livraison->delivery_notes = $validated['delivery_notes'];
        }

        $livraison->proof_uploaded_at = now();
        $livraison->save();

        return redirect()->back()->with('success', 'Delivery proof uploaded successfully.');
    }

    /**
     * Client confirms receipt of delivery
     */
    public function confirmReceipt(Request $request, $id)
    {
        $livraison = Livraison::findOrFail($id);

        // Check if user is the client who ordered
        if (Auth::id() !== $livraison->idClient) {
            return redirect()->back()->with('error', 'You are not authorized to confirm this delivery.');
        }

        $validated = $request->validate([
            'client_confirmation_notes' => 'nullable|string|max:500',
        ]);

        $livraison->client_confirmed = true;
        $livraison->client_confirmed_at = now();
        $livraison->client_confirmation_notes = $validated['client_confirmation_notes'] ?? null;
        $livraison->statut = 'delivered';
        $livraison->save();

        // Update order status
        $order = Order::find($livraison->idOrder);
        if ($order) {
            $order->statut = 'delivered';
            $order->save();
        }

        return redirect()->back()->with('success', 'Thank you for confirming the delivery receipt!');
    }

    /**
     * Mark delivery as delivered (Admin/Transporter)
     */
    public function markAsDelivered($id)
    {
        $livraison = Livraison::findOrFail($id);

        // Only admin or assigned transporter can mark as delivered
        if (Auth::user()->role !== 'admin' && Auth::id() !== $livraison->livreur_id) {
            return redirect()->back()->with('error', 'You are not authorized to mark this delivery as delivered.');
        }

        $livraison->statut = 'delivered';
        $livraison->save();

        // Update order status
        $order = Order::find($livraison->idOrder);
        if ($order) {
            $order->statut = 'delivered';
            $order->save();
        }

        return redirect()->back()->with('success', 'Delivery marked as delivered successfully!');
    }
}

