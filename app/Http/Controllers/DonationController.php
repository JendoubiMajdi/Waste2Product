<?php

namespace App\Http\Controllers;

use App\Models\Don;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    /**
     * Display a listing of donations
     */
    public function index()
    {
        $donations = Don::with('user')
            ->where('status', 'approved')
            ->latest()
            ->paginate(12);
        
        return view('donations.index', compact('donations'));
    }

    /**
     * Show the form for creating a new donation
     */
    public function create()
    {
        return view('donations.create');
    }

    /**
     * Store a newly created donation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:money,food,clothes,other',
            'amount' => 'nullable|numeric|min:0',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending'; // Pending admin approval

        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }

        Don::create($validated);

        return redirect()->route('donations.index')
            ->with('success', 'Your donation has been submitted and is pending approval.');
    }

    /**
     * Display the specified donation
     */
    public function show(Don $donation)
    {
        $donation->load('user');
        return view('donations.show', compact('donation'));
    }

    /**
     * Show user's own donations
     */
    public function myDonations()
    {
        $donations = Don::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('donations.my-donations', compact('donations'));
    }
}
