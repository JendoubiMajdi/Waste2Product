@extends('layouts.app')

@section('title', $post->title)

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $post->title }}</h2>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($post->title, 30) }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left"></i> Back to Forum
            </a>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Main Post -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start mb-4">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; font-weight: bold; font-size: 1.5rem;">
                            {{ $post->user->initials() }}
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $post->user->name }}</h4>
                        <small class="text-muted">
                            Posted {{ $post->created_at->diffForHumans() }} • 
                            {{ $post->created_at->format('M d, Y') }}
                        </small>
                    </div>
                    @if(Auth::check() && (Auth::id() === $post->user_id || Auth::user()->isAdmin()))
                    <form action="{{ route('forum.posts.destroy', $post) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this post?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Delete Post
                        </button>
                    </form>
                    @endif
                </div>

                <div class="post-content">
                    <p class="lead">{{ $post->content }}</p>
                </div>

                @if($post->image)
                <div class="mt-4 mb-3">
                    <img src="data:image/jpeg;base64,{{ $post->image }}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 500px; width: auto;" 
                         alt="Post image">
                </div>
                @endif

                <hr class="my-4">

                <div class="d-flex gap-3 align-items-center">
                    @auth
                    <button class="btn btn-primary like-btn" data-post-id="{{ $post->id }}">
                        <i class="bi bi-heart-fill"></i> 
                        <span class="likes-count">{{ $post->likes_count }}</span> Likes
                    </button>
                    <button class="btn btn-outline-danger report-btn" data-post-id="{{ $post->id }}">
                        <i class="bi bi-flag"></i> Report
                    </button>
                    @else
                    <span class="text-muted">
                        <i class="bi bi-heart"></i> {{ $post->likes_count }} Likes
                    </span>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        Login to Like or Comment
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-chat-dots"></i> Comments ({{ $post->comments_count }})
                </h5>
            </div>
            <div class="card-body">
                @auth
                <!-- Add Comment Form -->
                <form action="{{ route('forum.comments.store', $post) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="content" class="form-label">Your Comment</label>
                        <textarea name="content" id="content" rows="3" 
                                  class="form-control @error('content') is-invalid @enderror" 
                                  placeholder="Share your thoughts..." required>{{ old('content') }}</textarea>
                        @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-send"></i> Post Comment
                    </button>
                </form>
                @else
                <div class="alert alert-info">
                    <a href="{{ route('login') }}" class="alert-link">Login</a> to post a comment.
                </div>
                @endauth

                <hr>

                <!-- Comments List -->
                @forelse($post->comments as $comment)
                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px; font-weight: bold;">
                            {{ $comment->user->initials() }}
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $comment->user->name }}</strong>
                                <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            @if(Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->isAdmin()))
                            <form action="{{ route('forum.comments.destroy', $comment) }}" method="POST" 
                                  onsubmit="return confirm('Delete this comment?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        <p class="mb-0">{{ $comment->content }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-chat-square-dots" style="font-size: 3rem;"></i>
                    <p class="mt-2">No comments yet. Be the first to comment!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<script>
// Like button functionality
const likeBtn = document.querySelector('.like-btn');
if (likeBtn) {
    likeBtn.addEventListener('click', async function(e) {
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
                if (data.liked) {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                }
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

// Report button functionality
const reportBtn = document.querySelector('.report-btn');
if (reportBtn) {
    reportBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const reason = prompt('Please provide a reason for reporting this post:');
        
        if (!reason) return;
        
        try {
            const response = await fetch(`/forum/posts/${postId}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason })
            });
            
            const data = await response.json();
            if (data.success) {
                alert('Post reported successfully. Our team will review it.');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}
</script>
@endsection
