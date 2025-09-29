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
        return view('wastes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'quantite' => 'required|numeric|min:10',
            'dateDepot' => 'required|date|after:today',
            'localisation' => 'required|string',
        ]);

        Waste::create([
            'type' => $validated['type'],
            'quantite' => $validated['quantite'],
            'dateDepot' => $validated['dateDepot'],
            'localisation' => $validated['localisation'],
            'user_id' => Auth::id(),
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

    public function edit(Waste $waste)
    {
        if ($waste->user_id !== Auth::id()) {
            abort(403);
        }
        return view('wastes.edit', compact('waste'));
    }

    public function update(Request $request, Waste $waste)
    {
        if ($waste->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'quantite' => 'required|numeric|min:10',
            'dateDepot' => 'required|date|after:today',
            'localisation' => 'required|string',
        ]);

        $waste->update([
            'type' => $validated['type'],
            'quantite' => $validated['quantite'],
            'dateDepot' => $validated['dateDepot'],
            'localisation' => $validated['localisation'],
        ]);
        return redirect()->route('wastes.index')->with('success', 'Waste updated successfully.');
    }

    public function destroy(Waste $waste)
    {
        if ($waste->user_id !== Auth::id()) {
            abort(403);
        }
        $waste->delete();
        return redirect()->route('wastes.index')->with('success', 'Waste deleted successfully.');
    }
}
