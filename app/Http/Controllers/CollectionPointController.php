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
        if ($user->role !== 'collector' && $user->role !== 'admin') {
            abort(403, 'Only collectors and admins can manage collection points.');
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
        if (! Auth::check() || (Auth::user()->role !== 'collector' && Auth::user()->role !== 'admin')) {
            abort(403, 'Only collectors and admins can create collection points.');
        }

        return view('collection_points.create');
    }

    public function store(Request $request)
    {
        if (! Auth::check() || (Auth::user()->role !== 'collector' && Auth::user()->role !== 'admin')) {
            abort(403, 'Only collectors and admins can create collection points.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Combine opening and closing times into working_hours string
        if ($request->filled('opening_time') && $request->filled('closing_time')) {
            $validated['working_hours'] = $request->opening_time.'-'.$request->closing_time;
        } else {
            $validated['working_hours'] = null;
        }

        // Remove the separate time fields
        unset($validated['opening_time'], $validated['closing_time']);

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
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Combine opening and closing times into working_hours string
        if ($request->filled('opening_time') && $request->filled('closing_time')) {
            $validated['working_hours'] = $request->opening_time.'-'.$request->closing_time;
        } else {
            $validated['working_hours'] = null;
        }

        // Remove the separate time fields
        unset($validated['opening_time'], $validated['closing_time']);

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

    /**
     * Show the map with all collection points
     */
    public function showMap(Request $request)
    {
        $collectionPoints = CollectionPoint::where('status', 'active')
            ->get()
            ->map(function ($point) {
                return [
                    'id' => $point->id,
                    'name' => $point->name,
                    'address' => $point->address,
                    'latitude' => (float) $point->latitude,
                    'longitude' => (float) $point->longitude,
                    'working_hours' => $point->formatted_working_hours,
                    'contact_phone' => $point->contact_phone,
                    'status' => $point->status,
                ];
            });

        return view('collection_points.map', compact('collectionPoints'));
    }

    /**
     * Find the nearest collection point based on user's location
     */
    public function findNearest(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        // Get all active collection points with distance calculation
        $collectionPoints = CollectionPoint::where('status', 'active')
            ->get()
            ->map(function ($point) use ($userLat, $userLng) {
                $distance = $this->calculateDistance(
                    $userLat,
                    $userLng,
                    $point->latitude,
                    $point->longitude
                );

                return [
                    'id' => $point->id,
                    'name' => $point->name,
                    'address' => $point->address,
                    'latitude' => (float) $point->latitude,
                    'longitude' => (float) $point->longitude,
                    'working_hours' => $point->formatted_working_hours,
                    'contact_phone' => $point->contact_phone,
                    'distance' => round($distance, 2),
                ];
            })
            ->sortBy('distance');

        return response()->json([
            'success' => true,
            'nearest' => $collectionPoints->first(),
            'all_points' => $collectionPoints->values(),
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
