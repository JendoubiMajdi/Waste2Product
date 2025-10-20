@extends('layouts.app')

@section('title', $user->name . "'s Profile")

@push('head')
<style>
  .profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  .profile-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    border-radius: 16px;
    padding: 40px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 146, 126, 0.2);
    position: relative;
  }

  .profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    border: 4px solid white;
    margin-bottom: 20px;
  }

  .profile-name {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .profile-role {
    font-size: 16px;
    opacity: 0.9;
    text-transform: capitalize;
  }

  .profile-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .btn-profile {
    padding: 10px 24px;
    border-radius: 25px;
    border: 2px solid white;
    background: transparent;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-profile:hover {
    background: white;
    color: #00927E;
    transform: translateY(-2px);
  }

  .btn-profile.solid {
    background: white;
    color: #00927E;
  }

  .btn-profile.solid:hover {
    background: rgba(255, 255, 255, 0.9);
  }

  .btn-profile.danger {
    border-color: #ef4444;
    color: #ef4444;
  }

  .btn-profile.danger:hover {
    background: #ef4444;
    color: white;
  }

  .profile-content {
    display: grid;
    gap: 30px;
  }

  .section-title {
    font-size: 24px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .post-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    transition: all 0.3s ease;
  }

  .post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .post-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
  }

  .post-author-info {
    flex: 1;
  }

  .post-author-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
  }

  .post-time {
    color: #6b7280;
    font-size: 13px;
  }

  .post-content {
    color: #374151;
    line-height: 1.6;
    margin-bottom: 16px;
  }

  .post-visibility {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 12px;
    color: #6b7280;
  }

  .shared-post-indicator {
    background: #ecfdf5;
    border-left: 3px solid #00927E;
    padding: 12px;
    border-radius: 8px;
    margin-top: 12px;
    font-size: 14px;
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
  }

  .empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
  }

  .alert-blocked {
    background: #fee2e2;
    border: 2px solid #ef4444;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    color: #991b1b;
    text-align: center;
  }

  .post-footer {
    display: flex;
    gap: 20px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
    font-size: 14px;
    color: #6b7280;
  }

  .post-stat {
    display: flex;
    align-items: center;
    gap: 6px;
  }
</style>
@endpush

@section('content')
<div class="profile-container">

  @if($isBlocked)
  <div class="alert-blocked">
    <i class="bi bi-shield-x" style="font-size: 32px;"></i>
    <h4>User Blocked</h4>
    <p class="mb-0">You have blocked this user. You cannot interact with their content.</p>
    <form action="{{ route('users.unblock', $user->id) }}" method="POST" class="mt-3">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">Unblock User</button>
    </form>
  </div>
  @endif

  <!-- Profile Header -->
  <div class="profile-header">
    <div class="profile-avatar-large">
      {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <div class="profile-name">{{ $user->name }}</div>
    <div class="profile-role">{{ $user->role }}</div>

    @if(!$isOwnProfile && !$isBlocked)
    <div class="profile-actions">
      @if($friendshipStatus === 'friends')
        <!-- Already Friends -->
        <a href="{{ route('messages.show', $user->id) }}" class="btn-profile solid">
          <i class="bi bi-chat-dots-fill"></i> Message
        </a>
        <form action="{{ route('friends.remove', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove friend?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn-profile">
            <i class="bi bi-person-x"></i> Remove Friend
          </button>
        </form>
        <form action="{{ route('users.block') }}" method="POST" class="d-inline" onsubmit="return confirm('Block this user?')">
          @csrf
          <input type="hidden" name="blocked_user_id" value="{{ $user->id }}">
          <button type="submit" class="btn-profile danger">
            <i class="bi bi-shield-x"></i> Block
          </button>
        </form>

      @elseif($friendshipStatus === 'pending_sent')
        <!-- Request Sent -->
        <button class="btn-profile" disabled>
          <i class="bi bi-clock"></i> Friend Request Sent
        </button>

      @elseif($friendshipStatus === 'pending_received')
        <!-- Accept/Deny Request -->
        <form action="{{ route('friends.accept', $pendingRequest->id) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn-profile solid">
            <i class="bi bi-check-circle"></i> Accept Request
          </button>
        </form>
        <form action="{{ route('friends.deny', $pendingRequest->id) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn-profile">
            <i class="bi bi-x-circle"></i> Deny Request
          </button>
        </form>

      @else
        <!-- Not Friends -->
        <form action="{{ route('friends.request') }}" method="POST" class="d-inline">
          @csrf
          <input type="hidden" name="friend_id" value="{{ $user->id }}">
          <button type="submit" class="btn-profile solid">
            <i class="bi bi-person-plus"></i> Add Friend
          </button>
        </form>
        <form action="{{ route('users.block') }}" method="POST" class="d-inline" onsubmit="return confirm('Block this user?')">
          @csrf
          <input type="hidden" name="blocked_user_id" value="{{ $user->id }}">
          <button type="submit" class="btn-profile danger">
            <i class="bi bi-shield-x"></i> Block
          </button>
        </form>
      @endif
    </div>
    @endif
  </div>

  <!-- Posts Section -->
  <div class="profile-content">
    <div class="section-title">
      <i class="bi bi-file-post"></i>
      Posts
    </div>

    @forelse($posts as $post)
    <div class="post-card">
      <div class="post-header">
        <div class="post-avatar">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="post-author-info">
          <div class="post-author-name">{{ $user->name }}</div>
          <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
        </div>
        <span class="post-visibility">
          <i class="bi bi-{{ $post->visibility == 'public' ? 'globe' : 'people' }}"></i>
          {{ ucfirst($post->visibility) }}
        </span>
      </div>

      <div class="post-content">
        {{ $post->content }}
      </div>

      @if($post->image)
      <div class="post-image mb-3">
        <img src="data:image/jpeg;base64,{{ $post->image }}" alt="Post image" style="max-width: 100%; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
      </div>
      @endif

      @if($post->sharedPost)
      <div class="shared-post-indicator">
        <small class="d-block text-muted mb-1">
          <i class="bi bi-share"></i> Shared from {{ $post->sharedPost->user->name }}
        </small>
        <div>{{ Str::limit($post->sharedPost->content, 150) }}</div>
        @if($post->sharedPost->image)
        <img src="data:image/jpeg;base64,{{ $post->sharedPost->image }}" alt="Shared post image" style="max-width: 100%; margin-top: 12px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        @endif
      </div>
      @endif

      <div class="post-footer">
        <span class="post-stat">
          <i class="bi bi-heart"></i> {{ $post->likes_count ?? 0 }}
        </span>
        <span class="post-stat">
          <i class="bi bi-chat"></i> {{ $post->comments_count ?? 0 }}
        </span>
        <span class="post-stat">
          <i class="bi bi-share"></i> {{ $post->share_count ?? 0 }}
        </span>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <i class="bi bi-file-post"></i>
      <h3>No posts yet</h3>
      <p>{{ $isOwnProfile ? "You haven't" : $user->name . " hasn't" }} created any posts.</p>
    </div>
    @endforelse
  </div>

</div>
@endsection
