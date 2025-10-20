@extends('layouts.app')

@section('title', 'Messages')

@push('head')
<style>
  .messages-container {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
    height: calc(100vh - 200px);
    display: flex;
    gap: 20px;
  }

  .conversations-sidebar {
    width: 350px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .sidebar-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    padding: 20px;
  }

  .sidebar-header h2 {
    margin: 0;
    font-size: 20px;
  }

  .conversations-list {
    flex: 1;
    overflow-y: auto;
  }

  .conversation-item {
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
  }

  .conversation-item:hover {
    background: #f9fafb;
  }

  .conversation-item.active {
    background: #ecfdf5;
    border-left: 4px solid #00927E;
  }

  .conversation-item.unread {
    background: rgba(0, 146, 126, 0.05);
  }

  .conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
    flex-shrink: 0;
  }

  .conversation-info {
    flex: 1;
    min-width: 0;
  }

  .conversation-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
    margin-bottom: 4px;
  }

  .conversation-preview {
    color: #6b7280;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .conversation-item.unread .conversation-name {
    font-weight: 700;
  }

  .conversation-time {
    color: #9ca3af;
    font-size: 12px;
  }

  .unread-badge {
    background: #ef4444;
    color: white;
    border-radius: 12px;
    padding: 2px 8px;
    font-size: 11px;
    font-weight: 600;
  }

  .empty-messages {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #9ca3af;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  }

  .empty-messages i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
  }

  .empty-messages h3 {
    color: #4b5563;
    margin-bottom: 8px;
  }
</style>
@endpush

@section('content')
<div class="messages-container">
  
  <!-- Conversations Sidebar -->
  <div class="conversations-sidebar">
    <div class="sidebar-header">
      <h2><i class="bi bi-chat-dots-fill"></i> Messages</h2>
    </div>

    <div class="conversations-list">
      @forelse($conversations as $conversation)
        @php
          $otherUser = $conversation->getOtherUser(Auth::id());
          $unreadCount = $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->count();
        @endphp
        <a href="{{ route('messages.show', $otherUser->id) }}" 
           class="conversation-item {{ $unreadCount > 0 ? 'unread' : '' }}">
          <div class="conversation-avatar">
            {{ strtoupper(substr($otherUser->name, 0, 1)) }}
          </div>
          <div class="conversation-info">
            <div class="conversation-name">{{ $otherUser->name }}</div>
            <div class="conversation-preview">
              @if($conversation->latestMessage)
                {{ Str::limit($conversation->latestMessage->message, 40) }}
              @else
                No messages yet
              @endif
            </div>
          </div>
          <div class="d-flex flex-column align-items-end gap-1">
            @if($conversation->latestMessage)
            <span class="conversation-time">
              {{ $conversation->latestMessage->created_at->diffForHumans(null, true) }}
            </span>
            @endif
            @if($unreadCount > 0)
            <span class="unread-badge">{{ $unreadCount }}</span>
            @endif
          </div>
        </a>
      @empty
        <div class="text-center py-5 text-muted">
          <i class="bi bi-inbox" style="font-size: 48px; color: #d1d5db;"></i>
          <p class="mt-3 mb-0">No conversations yet</p>
          <small>Start chatting with your friends!</small>
        </div>
      @endforelse
    </div>
  </div>

  <!-- Empty State -->
  <div class="empty-messages">
    <i class="bi bi-chat-square-text"></i>
    <h3>Select a conversation</h3>
    <p>Choose a conversation from the sidebar to start messaging</p>
  </div>

</div>
@endsection
