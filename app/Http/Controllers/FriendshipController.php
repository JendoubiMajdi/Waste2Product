<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\BlockedUser;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    /**
     * Send friend request
     */
    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $userId = Auth::id();
        $friendId = $request->friend_id;

        if ($userId == $friendId) {
            return back()->with('error', 'You cannot add yourself as a friend.');
        }

        $existing = Friendship::where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->first();

        if ($existing) {
            return back()->with('error', $existing->status === 'accepted' ? 'Already friends.' : 'Request already sent.');
        }

        if (Auth::user()->hasBlocked($friendId) || Auth::user()->isBlockedBy($friendId)) {
            return back()->with('error', 'Cannot send friend request.');
        }

        Friendship::create([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => $friendId,
            'type' => 'friend_request',
            'data' => json_encode([
                'sender_id' => $userId,
                'sender_name' => Auth::user()->name,
                'message' => Auth::user()->name . ' sent you a friend request.',
            ]),
        ]);

        return back()->with('success', 'Friend request sent successfully.');
    }

    public function acceptRequest($friendshipId)
    {
        $friendship = Friendship::findOrFail($friendshipId);

        if ($friendship->friend_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $friendship->update(['status' => 'accepted']);

        Notification::create([
            'user_id' => $friendship->user_id,
            'type' => 'friend_accepted',
            'data' => json_encode([
                'sender_id' => Auth::id(),
                'sender_name' => Auth::user()->name,
                'message' => Auth::user()->name . ' accepted your friend request.',
            ]),
        ]);

        return back()->with('success', 'Friend request accepted.');
    }

    public function denyRequest($friendshipId)
    {
        $friendship = Friendship::findOrFail($friendshipId);

        if ($friendship->friend_id != Auth::id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $friendship->update(['status' => 'denied']);
        return back()->with('success', 'Friend request denied.');
    }

    public function removeFriend($friendId)
    {
        $userId = Auth::id();
        $friendship = Friendship::where(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $userId)->where('friend_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $userId);
        })->where('status', 'accepted')->first();

        if ($friendship) {
            $friendship->delete();
            return back()->with('success', 'Friend removed successfully.');
        }

        return back()->with('error', 'Friend not found.');
    }

    public function index()
    {
        $friends = Auth::user()->friends();
        $pendingRequests = Auth::user()->pendingFriendRequests();
        $sentRequests = Friendship::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('friend')
            ->latest()
            ->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }

    public function blockUser(Request $request)
    {
        $request->validate(['blocked_user_id' => 'required|exists:users,id']);

        $userId = Auth::id();
        $blockedUserId = $request->blocked_user_id;

        if ($userId == $blockedUserId) {
            return back()->with('error', 'You cannot block yourself.');
        }

        if (Auth::user()->hasBlocked($blockedUserId)) {
            return back()->with('error', 'User is already blocked.');
        }

        Friendship::where(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_id', $userId)->where('friend_id', $blockedUserId);
        })->orWhere(function ($query) use ($userId, $blockedUserId) {
            $query->where('user_id', $blockedUserId)->where('friend_id', $userId);
        })->delete();

        BlockedUser::create([
            'user_id' => $userId,
            'blocked_user_id' => $blockedUserId,
        ]);

        return back()->with('success', 'User blocked successfully.');
    }

    public function unblockUser($blockedUserId)
    {
        $blocked = BlockedUser::where('user_id', Auth::id())
            ->where('blocked_user_id', $blockedUserId)
            ->first();

        if ($blocked) {
            $blocked->delete();
            return back()->with('success', 'User unblocked successfully.');
        }

        return back()->with('error', 'User is not blocked.');
    }
}
