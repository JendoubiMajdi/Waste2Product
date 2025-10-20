<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = Auth::user()->conversations()->map(function ($conversation) {
            $conversation->load('latestMessage');
            return $conversation;
        });
        return view('messages.index', compact('conversations'));
    }

    public function show($userId)
    {
        $otherUser = User::findOrFail($userId);
        
        // Check if they're friends
        if (!Auth::user()->isFriendsWith($userId)) {
            return redirect()->route('messages.index')->with('error', 'You can only message friends.');
        }

        // Get or create conversation
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', Auth::id())->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)->where('user_two_id', Auth::id());
        })->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => Auth::id(),
                'user_two_id' => $userId,
            ]);
        }

        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Get all conversations for the sidebar
        $conversations = Auth::user()->conversations();

        return view('messages.show', compact('conversation', 'messages', 'otherUser', 'conversations'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required_without:shared_post_id|string|max:5000',
            'shared_post_id' => 'nullable|exists:posts,id',
        ]);

        $receiverId = $request->receiver_id;

        // Check if friends
        if (!Auth::user()->isFriendsWith($receiverId)) {
            return back()->with('error', 'You can only message friends.');
        }

        // Get or create conversation
        $conversation = Conversation::where(function ($query) use ($receiverId) {
            $query->where('user_one_id', Auth::id())->where('user_two_id', $receiverId);
        })->orWhere(function ($query) use ($receiverId) {
            $query->where('user_one_id', $receiverId)->where('user_two_id', Auth::id());
        })->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => Auth::id(),
                'user_two_id' => $receiverId,
            ]);
        }

        // Create message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'message' => $request->message ?? '',
            'shared_post_id' => $request->shared_post_id,
        ]);

        // Update conversation timestamp
        $conversation->touch();

        // Create notification
        Notification::create([
            'user_id' => $receiverId,
            'type' => 'message',
            'data' => json_encode([
                'sender_id' => Auth::id(),
                'sender_name' => Auth::user()->name,
                'message' => $request->shared_post_id ? 'Shared a post with you' : substr($request->message, 0, 50),
                'conversation_id' => $conversation->id,
            ]),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', 'Message sent.');
    }

    public function markAsRead($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
