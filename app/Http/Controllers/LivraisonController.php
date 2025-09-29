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
}
