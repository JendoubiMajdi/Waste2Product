<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteController extends Controller
{
    public function index()
    {
        $wastes = Waste::where('user_id', Auth::id())->get();
        return view('wastes.index', compact('wastes'));
    }

    public function create()
    {
        $collectionPoints = \App\Models\CollectionPoint::where('status', 'active')->get();
        return view('wastes.create', compact('collectionPoints'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'quantite' => 'required|numeric|min:10',
            'dateDepot' => 'required|date|after:today',
            'localisation' => 'required|string',
            'collection_point_id' => 'required|exists:collection_points,id',
        ]);

        Waste::create([
            'type' => $validated['type'],
            'quantite' => $validated['quantite'],
            'dateDepot' => $validated['dateDepot'],
            'localisation' => $validated['localisation'],
            'user_id' => Auth::id(),
            'collection_point_id' => $validated['collection_point_id'],
        ]);
        return redirect()->route('wastes.index')->with('success', 'Waste created successfully.');
    }

    public function show(Waste $waste)
    {
        if ($waste->user_id !== Auth::id()) {
            abort(403);
        }
        return view('wastes.show', compact('waste'));
    }

    public function edit($id)
    {
        $waste = Waste::findOrFail($id);
        $collectionPoints = \App\Models\CollectionPoint::where('status', 'active')->get();
        return view('wastes.edit', compact('waste', 'collectionPoints'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'quantite' => 'required|numeric|min:10',
            'dateDepot' => 'required|date|after:today',
            'localisation' => 'required|string',
            'collection_point_id' => 'required|exists:collection_points,id',
        ]);
        $waste = Waste::findOrFail($id);
        $waste->update($validated);
        return redirect()->route('wastes.index')->with('success', 'Waste updated successfully.');
    }

    public function destroy($id)
    {
        $waste = Waste::findOrFail($id);
        $waste->delete();
        return redirect()->route('wastes.index')->with('success', 'Waste deleted successfully.');
    }
}
