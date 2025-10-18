@extends('layouts.app')

@section('title', 'Community Forum')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Community Forum</h2>
        <p>Share ideas, ask questions, and connect with the community</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Recent Posts</h3>
                    <a href="{{ route('forum.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Create Post
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            @forelse($posts as $post)
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; font-weight: bold;">
                                    {{ $post->user->initials() }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">
                                            <a href="{{ route('forum.show', $post) }}" class="text-decoration-none text-dark">
                                                {{ $post->title }}
                                            </a>
                                        </h5>
                                        <small class="text-muted">
                                            by <strong>{{ $post->user->name }}</strong> • 
                                            {{ $post->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                
                                <p class="card-text mb-3">{{ Str::limit($post->content, 200) }}</p>

                                @if($post->image)
                                <div class="mb-3">
                                    <img src="data:image/jpeg;base64,{{ $post->image }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 300px; object-fit: cover;" 
                                         alt="Post image">
                                </div>
                                @endif

                                <div class="d-flex gap-3 align-items-center">
                                    <button class="btn btn-sm btn-outline-primary like-btn" 
                                            data-post-id="{{ $post->id }}">
                                        <i class="bi bi-heart"></i> 
                                        <span class="likes-count">{{ $post->likes_count }}</span> Likes
                                    </button>
                                    <a href="{{ route('forum.show', $post) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-chat"></i> {{ $post->comments_count }} Comments
                                    </a>
                                    @if(Auth::check() && (Auth::id() === $post->user_id || Auth::user()->isAdmin()))
                                    <form action="{{ route('forum.posts.destroy', $post) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this post?');" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    No posts yet. Be the first to start a discussion!
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.like-btn').forEach(button => {
    button.addEventListener('click', async function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        
        try {
            const response = await fetch(`/forum/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            if (data.success) {
                this.querySelector('.likes-count').textContent = data.likes_count;
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endsection
