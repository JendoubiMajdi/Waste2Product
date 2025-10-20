@extends('layouts.app')

@section('title', 'Notifications')

@push('head')
<style>
  .notifications-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  .notifications-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 146, 126, 0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .notification-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 12px;
    transition: all 0.3s ease;
    display: flex;
    align-items: start;
    gap: 16px;
    position: relative;
  }

  .notification-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }

  .notification-card.unread {
    background: rgba(0, 146, 126, 0.05);
    border-left: 4px solid #00927E;
  }

  .notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .notification-icon.friend-request {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
  }

  .notification-icon.friend-accepted {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
  }

  .notification-icon.message {
    background: rgba(14, 165, 233, 0.1);
    color: #0ea5e9;
  }

  .notification-icon.ban {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
  }

  .notification-icon.unban {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
  }

  .notification-content {
    flex: 1;
  }

  .notification-message {
    color: #374151;
    margin-bottom: 6px;
    line-height: 1.5;
  }

  .notification-time {
    color: #9ca3af;
    font-size: 13px;
  }

  .notification-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
  }

  .btn-notification {
    padding: 6px 16px;
    border-radius: 20px;
    border: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-accept {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
  }

  .btn-accept:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 146, 126, 0.3);
  }

  .btn-deny {
    background: #e5e7eb;
    color: #6b7280;
  }

  .btn-deny:hover {
    background: #d1d5db;
  }

  .mark-read-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    background: transparent;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .mark-read-btn:hover {
    background: #f3f4f6;
    color: #00927E;
  }

  .empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
  }

  .empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
  }

  .btn-mark-all {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-mark-all:hover {
    background: white;
    color: #00927E;
  }
</style>
@endpush

@section('content')
<div class="notifications-container">
  
  <div class="notifications-header">
    <div>
      <h1 class="mb-2"><i class="bi bi-bell-fill"></i> Notifications</h1>
      <p class="mb-0">Stay updated with your activity</p>
    </div>
    @if($notifications->where('read_at', null)->count() > 0)
    <form action="{{ route('notifications.read-all') }}" method="POST">
      @csrf
      <button type="submit" class="btn-mark-all">
        <i class="bi bi-check-all"></i> Mark all as read
      </button>
    </form>
    @endif
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Notifications List -->
  @forelse($notifications as $notification)
    @php
      $data = json_decode($notification->data, true);
      $type = $notification->type;
    @endphp
    
    <div class="notification-card {{ $notification->read_at ? '' : 'unread' }}">
      <!-- Icon based on type -->
      <div class="notification-icon {{ str_replace('_', '-', $type) }}">
        @if($type == 'friend_request')
          <i class="bi bi-person-plus-fill"></i>
        @elseif($type == 'friend_accepted')
          <i class="bi bi-check-circle-fill"></i>
        @elseif($type == 'message')
          <i class="bi bi-chat-fill"></i>
        @elseif($type == 'ban')
          <i class="bi bi-x-circle-fill"></i>
        @elseif($type == 'unban')
          <i class="bi bi-check-circle-fill"></i>
        @else
          <i class="bi bi-bell-fill"></i>
        @endif
      </div>

      <div class="notification-content">
        <div class="notification-message">
          {{ $data['message'] ?? 'New notification' }}
        </div>
        <div class="notification-time">
          {{ $notification->created_at->diffForHumans() }}
        </div>

        <!-- Action buttons for friend requests -->
        @if($type == 'friend_request' && !$notification->read_at)
          @php
            $friendship = \App\Models\Friendship::where('id', $data['friendship_id'] ?? 0)->first();
          @endphp
          @if($friendship && $friendship->status == 'pending')
          <div class="notification-actions">
            <form action="{{ route('friends.accept', $friendship->id) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn-notification btn-accept">
                <i class="bi bi-check"></i> Accept
              </button>
            </form>
            <form action="{{ route('friends.deny', $friendship->id) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn-notification btn-deny">
                <i class="bi bi-x"></i> Deny
              </button>
            </form>
          </div>
          @endif
        @endif

        <!-- View message button -->
        @if($type == 'message' && isset($data['sender_id']))
          <div class="notification-actions">
            <a href="{{ route('messages.show', $data['sender_id']) }}" class="btn-notification btn-accept">
              <i class="bi bi-chat"></i> View Message
            </a>
          </div>
        @endif
      </div>

      <!-- Mark as read button -->
      @if(!$notification->read_at)
      <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="mark-read-btn">
        @csrf
        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;" title="Mark as read">
          <i class="bi bi-check"></i>
        </button>
      </form>
      @endif
    </div>
  @empty
    <div class="empty-state">
      <i class="bi bi-bell-slash"></i>
      <h3>No notifications</h3>
      <p>You're all caught up! Check back later for updates.</p>
    </div>
  @endforelse

  <!-- Pagination -->
  @if($notifications->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $notifications->links() }}
  </div>
  @endif

</div>
@endsection
