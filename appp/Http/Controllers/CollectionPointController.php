<?php

namespace App\Http\Controllers;

use App\Models\CollectionPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionPointController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        if ($user->role !== 'collector') {
            abort(403, 'Only collectors can manage their collection points.');
        }
        $points = CollectionPoint::where('user_id', $user->id)->get();
        // Example statistics: total wastes per collection point
        foreach ($points as $point) {
            $point->waste_count = $point->wastes()->count();
            $point->waste_total = $point->wastes()->sum('quantite');
        }
        return view('collection_points.dashboard', compact('points'));
    }
    public function index()
    {
        $collectionPoints = CollectionPoint::where('status', 'active')->paginate(9);
        return view('collection_points.index', compact('collectionPoints'));
    }

    public function show($id)
    {
        $point = CollectionPoint::findOrFail($id);
        return view('collection_points.show', compact('point'));
    }

    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            abort(403, 'Only collectors can create collection points.');
        }
        return view('collection_points.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'collector') {
            abort(403, 'Only collectors can create collection points.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'working_hours' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $validated['user_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }
        CollectionPoint::create($validated);
        return redirect()->route('collection_points.index')->with('success', 'Collection point submitted for admin approval.');
    }

    public function edit($id)
    {
        $point = CollectionPoint::findOrFail($id);
        return view('collection_points.edit', compact('point'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'working_hours' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $point = CollectionPoint::findOrFail($id);
        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }
        $point->update($validated);
        return redirect()->route('collection_points.index')->with('success', 'Collection point updated successfully.');
    }

    public function destroy($id)
    {
        $point = CollectionPoint::findOrFail($id);
        $point->delete();
        return redirect()->route('collection_points.index');
    }
}
