@extends('layouts.app')

@section('title', 'Chat with ' . $otherUser->name)

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

  /* Chat Area */
  .chat-area {
    flex: 1;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .chat-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .chat-header-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 20px;
  }

  .chat-header-info h3 {
    margin: 0;
    font-size: 18px;
  }

  .chat-header-info small {
    opacity: 0.9;
  }

  .messages-area {
    flex: 1;
    overflow-y: auto;
    padding: 30px;
    background: #f9fafb;
  }

  .message-bubble {
    margin-bottom: 20px;
    display: flex;
    align-items: flex-end;
    gap: 10px;
  }

  .message-bubble.sent {
    flex-direction: row-reverse;
  }

  .message-content {
    max-width: 60%;
    padding: 12px 16px;
    border-radius: 16px;
    position: relative;
  }

  .message-bubble.received .message-content {
    background: white;
    color: #1f2937;
    border-bottom-left-radius: 4px;
  }

  .message-bubble.sent .message-content {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    border-bottom-right-radius: 4px;
  }

  .message-text {
    margin-bottom: 4px;
    word-wrap: break-word;
  }

  .message-time {
    font-size: 11px;
    opacity: 0.7;
  }

  .shared-post-preview {
    margin-top: 10px;
    padding: 12px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
    border-left: 3px solid rgba(0, 0, 0, 0.2);
  }

  .message-bubble.sent .shared-post-preview {
    background: rgba(255, 255, 255, 0.2);
    border-left-color: rgba(255, 255, 255, 0.5);
  }

  .shared-post-preview small {
    display: block;
    margin-bottom: 4px;
    font-weight: 600;
  }

  .chat-input-area {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    background: white;
  }

  .chat-input-form {
    display: flex;
    gap: 10px;
  }

  .chat-input {
    flex: 1;
    padding: 12px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 25px;
    outline: none;
    transition: all 0.3s ease;
  }

  .chat-input:focus {
    border-color: #00927E;
  }

  .send-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    border: none;
    color: white;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 146, 126, 0.3);
  }

  .date-separator {
    text-align: center;
    margin: 30px 0 20px;
    position: relative;
  }

  .date-separator span {
    background: #f9fafb;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
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
      @foreach($conversations as $conv)
        @php
          $user = $conv->getOtherUser(Auth::id());
          $unreadCount = $conv->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->count();
        @endphp
        <a href="{{ route('messages.show', $user->id) }}" 
           class="conversation-item {{ $user->id == $otherUser->id ? 'active' : '' }} {{ $unreadCount > 0 && $user->id != $otherUser->id ? 'unread' : '' }}">
          <div class="conversation-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
          <div class="conversation-info">
            <div class="conversation-name">{{ $user->name }}</div>
            <div class="conversation-preview">
              @if($conv->latestMessage)
                {{ Str::limit($conv->latestMessage->message, 40) }}
              @else
                No messages yet
              @endif
            </div>
          </div>
          <div class="d-flex flex-column align-items-end gap-1">
            @if($conv->latestMessage)
            <span class="conversation-time">
              {{ $conv->latestMessage->created_at->diffForHumans(null, true) }}
            </span>
            @endif
            @if($unreadCount > 0 && $user->id != $otherUser->id)
            <span class="unread-badge">{{ $unreadCount }}</span>
            @endif
          </div>
        </a>
      @endforeach
    </div>
  </div>

  <!-- Chat Area -->
  <div class="chat-area">
    <div class="chat-header">
      <div class="chat-header-avatar">
        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
      </div>
      <div class="chat-header-info flex-grow-1">
        <h3>{{ $otherUser->name }}</h3>
        <small>{{ ucfirst($otherUser->role) }}</small>
      </div>
      <a href="{{ route('profile.show', $otherUser->id) }}" class="btn btn-light btn-sm rounded-pill">
        <i class="bi bi-person"></i> View Profile
      </a>
    </div>

    <!-- Error/Success Messages -->
    @if(session('error'))
    <div class="alert alert-danger mx-3 mt-3">{{ session('error') }}</div>
    @endif
    @if(session('success'))
    <div class="alert alert-success mx-3 mt-3">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger mx-3 mt-3">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="messages-area" id="messagesArea">
      @php
        $currentDate = null;
      @endphp

      @forelse($messages as $message)
        @php
          $messageDate = $message->created_at->format('Y-m-d');
          if ($currentDate !== $messageDate) {
            $currentDate = $messageDate;
            echo '<div class="date-separator"><span>' . $message->created_at->format('F j, Y') . '</span></div>';
          }
        @endphp

        <div class="message-bubble {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
          <div class="message-content">
            <div class="message-text">{{ $message->message }}</div>
            
            @if($message->sharedPost)
            <div class="shared-post-preview">
              <small><i class="bi bi-share"></i> Shared Post</small>
              <div>{{ Str::limit($message->sharedPost->content, 100) }}</div>
              <a href="{{ route('posts.index') }}" style="color: inherit; font-size: 12px;">View post</a>
            </div>
            @endif
            
            <div class="message-time">
              {{ $message->created_at->format('g:i A') }}
              @if($message->sender_id == Auth::id() && $message->read_at)
                <i class="bi bi-check-all"></i>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="text-center text-muted py-5">
          <i class="bi bi-chat-text" style="font-size: 48px; color: #d1d5db;"></i>
          <p class="mt-3">No messages yet. Start the conversation!</p>
        </div>
      @endforelse
    </div>

    <div class="chat-input-area">
      <form action="{{ route('messages.send') }}" method="POST" class="chat-input-form" id="messageForm">
        @csrf
        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
        <input type="text" name="message" class="chat-input" placeholder="Type a message..." required>
        <button type="submit" class="send-btn">
          <i class="bi bi-send-fill"></i>
        </button>
      </form>
    </div>
  </div>

</div>

<script>
  // Auto scroll to bottom on load
  const messagesArea = document.getElementById('messagesArea');
  if (messagesArea) {
    messagesArea.scrollTop = messagesArea.scrollHeight;
  }

  // Keep form input focused
  document.querySelector('.chat-input').focus();
</script>
@endsection
