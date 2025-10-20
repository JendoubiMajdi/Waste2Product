<?php

namespace App\Http\Controllers;

use App\Models\PostReport;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason' => 'required|string|max:1000',
        ]);

        // Check if already reported by this user
        $existing = PostReport::where('post_id', $request->post_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reported this post.');
        }

        PostReport::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Post reported successfully. Our team will review it.');
    }
}
