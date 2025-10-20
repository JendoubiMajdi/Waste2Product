<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Services\WasteClassificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WasteController extends Controller
{
    protected $classificationService;

    public function __construct(WasteClassificationService $classificationService)
    {
        $this->classificationService = $classificationService;
    }

    public function index(Request $request)
    {
        // Start with base query
        $query = Waste::with('collectionPoint', 'user');

        // Filter by waste type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search by location
        if ($request->filled('location')) {
            $location = $request->location;
            $query->whereHas('collectionPoint', function($q) use ($location) {
                $q->where('nom', 'like', "%{$location}%")
                  ->orWhere('adresse', 'like', "%{$location}%");
            });
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('dateDepot', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('dateDepot', '<=', $request->end_date);
        }

        // Filter by minimum quantity
        if ($request->filled('min_quantity')) {
            $query->where('quantite', '>=', $request->min_quantity);
        }

        // Get filtered wastes
        $wastes = $query->get();

        return view('wastes.index', compact('wastes'));
    }

    public function create()
    {
        $collectionPoints = \App\Models\CollectionPoint::where('status', 'active')->get();
        $aiAvailable = $this->classificationService->isAvailable();
        $wasteCategories = $this->classificationService->getCategories();

        return view('wastes.create', compact('collectionPoints', 'aiAvailable', 'wasteCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'quantite' => 'required|numeric|min:10',
            'dateDepot' => 'required|date|after:today',
            'localisation' => 'required|string',
            'collection_point_id' => 'required|exists:collection_points,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        $wasteData = [
            'type' => $validated['type'],
            'quantite' => $validated['quantite'],
            'dateDepot' => $validated['dateDepot'],
            'localisation' => $validated['localisation'],
            'user_id' => Auth::id(),
            'collection_point_id' => $validated['collection_point_id'],
        ];

        // Handle image upload and AI classification
        if ($request->hasFile('image')) {
            // Convert image to base64
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $wasteData['image'] = $imageData;

            // Try to classify with AI
            $classification = $this->classificationService->classifyFromBase64($imageData);

            if ($classification['success']) {
                // Auto-fill waste type from AI if user hasn't changed it
                if ($request->input('use_ai_classification') === 'true') {
                    $wasteData['type'] = $classification['waste_type'];
                }
                $wasteData['ai_confidence'] = $classification['confidence'];

                Log::info('Waste classified by AI', [
                    'predicted_type' => $classification['waste_type'],
                    'confidence' => $classification['confidence'],
                ]);
            } else {
                Log::warning('AI classification failed', ['error' => $classification['error'] ?? 'Unknown error']);
            }
        }

        Waste::create($wasteData);

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

    /**
     * Classify waste image via AJAX
     */
    public function classifyImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if (! $request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'error' => 'No image provided',
            ], 400);
        }

        $classification = $this->classificationService->classifyFromFile($request->file('image'));

        return response()->json($classification);
    }
}
