<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Don;
use App\Models\Post;
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

        // Get counts for stats
        $pendingCount = Don::where('status', 'pending')->count();
        $approvedCount = Don::where('status', 'approved')->count();
        $rejectedCount = Don::where('status', 'rejected')->count();
        $totalCount = Don::count();

        return view('admin.donations.index', compact(
            'donations',
            'type',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount'
        ));
    }

    /**
     * Approve a donation
     */
    public function approve(Don $donation)
    {
        // Update donation status
        $donation->update(['status' => 'approved']);

        // Create a pride post automatically from the donor user
        $this->createPridePost($donation);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation approved successfully and pride post created.');
    }

    /**
     * Create a pride post for an approved donation
     */
    private function createPridePost(Don $donation)
    {
        // Create content based on donation type
        $content = "I'm thrilled to share that I've made a donation to support our community! 🌟\n\n";
        
        if ($donation->type === 'money') {
            $content .= "💰 Amount: " . number_format($donation->amount, 2) . " TND\n";
        } elseif ($donation->type === 'food') {
            $content .= "🍎 Food Donation: " . $donation->amount . " kg of food\n";
        } elseif ($donation->type === 'clothes') {
            $content .= "👕 Clothing Donation: " . $donation->amount . " items of clothing\n";
        }
        
        $content .= "\n" . $donation->description . "\n\n";
        $content .= "Every small act of kindness makes a big difference. Together, we can build a better world! 💚\n\n";
        $content .= "#Donation #Community #MakingADifference #Waste2Product";

        Post::create([
            'user_id' => $donation->user_id,
            'don_id' => $donation->id,
            'title' => "🎉 Proud to Make a Difference!",
            'content' => $content,
            'post_type' => 'donation',
        ]);
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
