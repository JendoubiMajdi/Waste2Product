<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::visibleTo(Auth::id())
            ->with(['user', 'sharedPost.user'])
            ->latest()
            ->paginate(15);

        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'image' => 'nullable|image|max:5120',
            'visibility' => 'required|in:public,friends',
        ]);

        $data = $request->only(['title', 'content', 'visibility']);
        $data['user_id'] = Auth::id();
        $data['post_type'] = 'regular';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $data['image'] = base64_encode(file_get_contents($image->getRealPath()));
        }

        Post::create($data);

        return back()->with('success', 'Post created successfully.');
    }

    public function shareToFeed(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'nullable|string|max:1000',
            'visibility' => 'required|in:public,friends',
        ]);

        $originalPost = Post::findOrFail($request->post_id);

        // Check if can view the original post
        if ($originalPost->visibility === 'friends' && !Auth::user()->isFriendsWith($originalPost->user_id) && $originalPost->user_id != Auth::id()) {
            return back()->with('error', 'You cannot share this post.');
        }

        Post::create([
            'user_id' => Auth::id(),
            'shared_post_id' => $request->post_id,
            'content' => $request->content ?? '',
            'visibility' => $request->visibility,
            'post_type' => 'shared',
        ]);

        // Increment share count
        $originalPost->increment('share_count');

        return back()->with('success', 'Post shared to your feed.');
    }

    public function destroy($id)
    {
        $post = Post::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $post->delete();

        return back()->with('success', 'Post deleted successfully.');
    }
}
