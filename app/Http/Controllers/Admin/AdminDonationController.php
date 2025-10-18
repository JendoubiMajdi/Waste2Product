<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use Illuminate\Http\Request;

class AdminDonationController extends Controller
{
    /**
     * Display all donations for admin management
     */
    public function index(Request $request)
    {
        $type = $request->input('type');
        $status = $request->input('status');

        $donations = Don::with('user')
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.donations.index', compact('donations', 'type', 'status'));
    }

    /**
     * Approve a donation
     */
    public function approve(Don $donation)
    {
        $donation->update(['status' => 'approved']);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation approved successfully.');
    }

    /**
     * Reject a donation
     */
    public function reject(Don $donation, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $donation->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation rejected.');
    }

    /**
     * Delete a donation
     */
    public function destroy(Don $donation)
    {
        $donation->delete();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation deleted successfully.');
    }
}
