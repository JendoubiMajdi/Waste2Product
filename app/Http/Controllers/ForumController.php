<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Display forum feed
     */
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate(10);

        return view('forum.index', compact('posts'));
    }

    /**
     * Show form for creating a new post
     */
    public function create()
    {
        return view('forum.create');
    }

    /**
     * Store a new post
     */
    public function storePost(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }

        Post::create($validated);

        return redirect()->route('forum.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display a single post with comments
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments.user', 'likes']);

        return view('forum.show', compact('post'));
    }

    /**
     * Store a comment on a post
     */
    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('forum.show', $post)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Like/Unlike a post
     */
    public function toggleLike(Post $post)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('post_id', $post->id)
            ->first();

        if ($like) {
            $like->delete();
            $message = 'Post unliked';
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'post_id' => $post->id,
            ]);
            $message = 'Post liked';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * Report a post
     */
    public function report(Request $request, Post $post)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return redirect()->route('forum.show', $post)
            ->with('success', 'Post reported successfully. Our team will review it.');
    }

    /**
     * Delete a post (own posts only or admin)
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $post->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Post deleted successfully.');
    }

    /**
     * Delete a comment (own comments only or admin)
     */
    public function destroyComment(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id && ! Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()->route('forum.show', $postId)
            ->with('success', 'Comment deleted successfully.');
    }
}
