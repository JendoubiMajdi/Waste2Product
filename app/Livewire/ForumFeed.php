<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ForumFeed extends Component
{
    use WithPagination, WithFileUploads;

    public $newContent;
    public $newMediaType = 'text';
    public $newMedia;
    public $newComment = []; // Array for per-post comments
    public $replyTo = []; // Array for comment replies
    public $showReportModal = false;
    public $reportPostId = null;
    public $reportReason = '';
    public $customReason = '';
    public $showInviteModal = false;
    public $inviteUserId = null;

    public function render()
    {
        if (Auth::user()->isBanned()) {
            session()->flash('error', 'You are banned until ' . Auth::user()->banned_until);
        }

        $posts = Post::with(['user', 'comments', 'reactions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Add friendship status to posts
        foreach ($posts as $post) {
            $post->is_friend = Auth::user()->friends()->where('friend_id', $post->user_id)->exists();
        }
 
        $invitations = Auth::user()->receivedInvitations()->where('status', 'pending')->get();

        return view('livewire.forum-feed', compact('posts', 'invitations'));
    }

    public function createPost()
    {
        if (Auth::user()->isBanned()) {
            session()->flash('error', 'You are banned and cannot create posts.');
            return;
        }

        // Validate based on post type
        $rules = [
            'newContent' => 'required|string|min:1|max:1000',
            'newMediaType' => 'required|in:text,image,video,link',
        ];

        // Only validate media for image/video posts
        if (in_array($this->newMediaType, ['image', 'video'])) {
            $rules['newMedia'] = 'required|file|max:10240'; // 10MB max
        }

        $this->validate($rules);

        $mediaUrl = null;
        if (in_array($this->newMediaType, ['image', 'video'])) {
            // Store in storage/app/public/media
            $mediaUrl = $this->newMedia->store('media', 'public');
            
            // Also copy to public/storage/media for immediate access
            $this->ensurePublicAccess($mediaUrl);
        } elseif ($this->newMediaType === 'link') {
            $mediaUrl = $this->newContent; // For links, content becomes the URL
        }

        try {
            Auth::user()->posts()->create([
                'content' => $this->newContent,
                'media_type' => $this->newMediaType,
                'media_url' => $mediaUrl,
            ]);

            $this->reset(['newContent', 'newMediaType', 'newMedia']);
            
            session()->flash('success', 'Post created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create post. Please try again.');
            \Log::error('Post creation failed: ' . $e->getMessage());
        }
    }

    public function like($postId)
    {
        if (Auth::user()->isBanned()) return;
        $post = Post::find($postId);
        $existing = $post->reactions()->where('user_id', Auth::id())->first();
        if ($existing) {
            $existing->update(['type' => 'like']);
        } else {
            $post->reactions()->create([
                'user_id' => Auth::id(),
                'type' => 'like',
            ]);
            
            // Create notification for post owner (if not self-like)
            if ($post->user_id !== Auth::id()) {
                $this->createNotification($post->user_id, 'post_like', 'liked your post', $postId);
            }
        }
    }

    public function dislike($postId)
    {
        if (Auth::user()->isBanned()) return;
        $post = Post::find($postId);
        $existing = $post->reactions()->where('user_id', Auth::id())->first();
        if ($existing) {
            $existing->update(['type' => 'dislike']);
        } else {
            $post->reactions()->create(['user_id' => Auth::id(), 'type' => 'dislike']);
        }
    }

    public function share($postId)
    {
        if (Auth::user()->isBanned()) return;
        $post = Post::find($postId);
        $post->share_count++;
        $post->save();
    }


    public function comment($postId)
    {
        if (Auth::user()->isBanned()) return;
        
        $this->validate(['newComment.' . $postId => 'required|string']);
        
        $post = Post::find($postId);
        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->newComment[$postId],
        ]);
        
        // Create notification for post owner (if not self-comment)
        if ($post->user_id !== Auth::id()) {
            $this->createNotification($post->user_id, 'post_comment', 'commented on your post', $postId);
        }
        
        // Clear the specific comment field
        $this->newComment[$postId] = '';
    }

    public function replyToComment($commentId)
    {
        if (Auth::user()->isBanned()) return;
        
        $this->validate(['replyTo.' . $commentId => 'required|string']);
        
        $comment = Comment::find($commentId);
        
        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $comment->post_id,
            'content' => $this->replyTo[$commentId],
            'parent_id' => $commentId,
        ]);
        
        // Clear the reply field
        $this->replyTo[$commentId] = '';
    }

    public function likeComment($commentId)
    {
        if (Auth::user()->isBanned()) return;
        
        $comment = Comment::find($commentId);
        $existing = $comment->likes()->where('user_id', Auth::id())->first();
        
        if ($existing) {
            $existing->delete(); // Unlike if already liked
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'post_id' => null, // Explicitly set to null for comment likes
                'comment_id' => $commentId,
                'type' => 'like'
            ]);
        }
    }

    public function openReportModal($postId)
    {
        $this->reportPostId = $postId;
        $this->showReportModal = true;
        $this->reportReason = '';
        $this->customReason = '';
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->reportPostId = null;
        $this->reportReason = '';
        $this->customReason = '';
    }

    public function submitReport()
    {
        if (Auth::user()->isBanned()) return;
        
        $this->validate([
            'reportReason' => 'required|string',
            'customReason' => 'required_if:reportReason,other|string|max:500'
        ]);
        
        $reason = $this->reportReason === 'other' ? $this->customReason : $this->reportReason;
        
        Post::find($this->reportPostId)->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $reason,
        ]);
        
        $this->closeReportModal();
        session()->flash('success', 'Report submitted successfully. Thank you for helping keep our community safe.');
    }

    public function acceptInvite($inviteId)
    {
        $invite = Invitation::find($inviteId);
        if ($invite->receiver_id !== Auth::id()) return;
        $invite->status = 'accepted';
        $invite->save();
        Auth::user()->friends()->attach($invite->sender_id);
        $invite->sender->friends()->attach(Auth::id());
    }

    private function ensurePublicAccess($mediaUrl)
    {
        $storagePath = storage_path('app/public/' . $mediaUrl);
        $publicPath = public_path('storage/' . $mediaUrl);
        
        // Create public/storage/media directory if it doesn't exist
        $publicDir = dirname($publicPath);
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        
        // Copy file to public location if it doesn't exist there
        if (file_exists($storagePath) && !file_exists($publicPath)) {
            copy($storagePath, $publicPath);
        }
    }

    public function refuseInvite($inviteId)
    {
        $invite = Invitation::find($inviteId);
        $invite->status = 'refused';
        $invite->save();
    }

    public function openInviteModal($userId)
    {
        // Check if already friends or invite already sent
        $alreadyFriends = Auth::user()->friends()->where('friend_id', $userId)->exists();
        $inviteExists = Invitation::where('sender_id', Auth::id())
            ->where('receiver_id', $userId)
            ->where('status', 'pending')
            ->exists();
            
        if ($alreadyFriends) {
            session()->flash('error', 'You are already friends with this user.');
            return;
        }
        
        if ($inviteExists) {
            session()->flash('error', 'Friend invitation already sent to this user.');
            return;
        }
        
        $this->inviteUserId = $userId;
        $this->showInviteModal = true;
    }

    public function closeInviteModal()
    {
        $this->showInviteModal = false;
        $this->inviteUserId = null;
    }

    public function sendFriendInvite()
    {
        if (!$this->inviteUserId) return;
        
        Invitation::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->inviteUserId,
            'status' => 'pending'
        ]);
        
        // Create notification for the receiver
        $this->createNotification($this->inviteUserId, 'friend_invite', 'sent you a friend invitation', null);
        
        $this->closeInviteModal();
        session()->flash('success', 'Friend invitation sent successfully!');
    }

    private function createNotification($userId, $type, $message, $relatedId = null)
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => Auth::user()->name . ' ' . $message,
            'related_id' => $relatedId,
            'is_read' => false
        ]);
    }

    // Add method to send invite (call from view with user search)
    public function sendInvite($receiverId)
    {
        Invitation::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
        ]);
    }
}