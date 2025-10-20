<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show($userId)
    {
        $user = User::with(['posts' => function($query) use ($userId) {
            if (Auth::id() != $userId) {
                $query->visibleTo(Auth::id());
            }
            $query->latest();
        }])->findOrFail($userId);

        $isOwnProfile = Auth::check() && Auth::id() == $userId;
        $isBlocked = Auth::check() ? Auth::user()->hasBlocked($userId) : false;
        $isBlockedBy = Auth::check() ? Auth::user()->isBlockedBy($userId) : false;

        // Check friendship status
        $friendshipStatus = null;
        $pendingRequest = null;

        if (Auth::check() && !$isOwnProfile) {
            // Check if they are friends
            $friendship = Friendship::where(function ($query) use ($userId) {
                $query->where('user_id', Auth::id())->where('friend_id', $userId);
            })->orWhere(function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('friend_id', Auth::id());
            })->first();

            if ($friendship) {
                if ($friendship->status == 'accepted') {
                    $friendshipStatus = 'friends';
                } elseif ($friendship->user_id == Auth::id()) {
                    $friendshipStatus = 'pending_sent';
                } else {
                    $friendshipStatus = 'pending_received';
                    $pendingRequest = $friendship;
                }
            }
        }

        $posts = $user->posts;

        return view('profile.show', compact('user', 'posts', 'friendshipStatus', 'pendingRequest', 'isOwnProfile', 'isBlocked', 'isBlockedBy'));
    }
}
