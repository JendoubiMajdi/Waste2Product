@extends('layouts.app')

@section('title', 'Friends')

@push('head')
<style>
  .friends-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  .section-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 146, 126, 0.2);
  }

  .friend-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }

  .friend-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .friend-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 24px;
    flex-shrink: 0;
  }

  .friend-info {
    flex-grow: 1;
    margin-left: 15px;
  }

  .friend-name {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
  }

  .friend-role {
    color: #6b7280;
    font-size: 14px;
    text-transform: capitalize;
  }

  .btn-action {
    padding: 8px 20px;
    border-radius: 25px;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
    font-size: 14px;
  }

  .btn-primary-custom {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
  }

  .btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 146, 126, 0.3);
  }

  .btn-danger-custom {
    background: #ef4444;
    color: white;
  }

  .btn-danger-custom:hover {
    background: #dc2626;
    transform: translateY(-2px);
  }

  .btn-secondary-custom {
    background: #6b7280;
    color: white;
  }

  .btn-secondary-custom:hover {
    background: #4b5563;
    transform: translateY(-2px);
  }

  .btn-outline-custom {
    background: transparent;
    border: 2px solid #00927E;
    color: #00927E;
  }

  .btn-outline-custom:hover {
    background: #00927E;
    color: white;
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

  .tabs-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e5e7eb;
  }

  .tab-btn {
    padding: 12px 24px;
    background: transparent;
    border: none;
    color: #6b7280;
    font-weight: 500;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .tab-btn.active {
    color: #00927E;
  }

  .tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #00927E;
  }

  .tab-btn:hover {
    color: #00927E;
  }

  .badge-count {
    background: #ef4444;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    margin-left: 6px;
  }
</style>
@endpush

@section('content')
<div class="friends-container">
  
  <div class="section-header">
    <h1 class="mb-2"><i class="bi bi-people-fill"></i> Friends</h1>
    <p class="mb-0">Manage your connections and friend requests</p>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Tabs -->
  <div class="tabs-nav">
    <button class="tab-btn active" data-tab="friends">
      My Friends ({{ $friends->count() }})
    </button>
    <button class="tab-btn" data-tab="pending">
      Pending Requests
      @if($pendingRequests->count() > 0)
      <span class="badge-count">{{ $pendingRequests->count() }}</span>
      @endif
    </button>
    <button class="tab-btn" data-tab="sent">
      Sent Requests ({{ $sentRequests->count() }})
    </button>
  </div>

  <!-- My Friends Tab -->
  <div class="tab-content" id="friends-tab">
    @forelse($friends as $friend)
    <div class="friend-card d-flex align-items-center">
      <div class="friend-avatar">
        {{ strtoupper(substr($friend->name, 0, 1)) }}
      </div>
      <div class="friend-info">
        <div class="friend-name">{{ $friend->name }}</div>
        <div class="friend-role">{{ $friend->role }}</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('profile.show', $friend->id) }}" class="btn btn-action btn-outline-custom">
          <i class="bi bi-person"></i> View Profile
        </a>
        <a href="{{ route('messages.show', $friend->id) }}" class="btn btn-action btn-primary-custom">
          <i class="bi bi-chat-dots"></i> Message
        </a>
        <form action="{{ route('friends.remove', $friend->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this friend?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-action btn-danger-custom">
            <i class="bi bi-person-x"></i> Remove
          </button>
        </form>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <i class="bi bi-people"></i>
      <h3>No friends yet</h3>
      <p>Start connecting with other users to build your network!</p>
    </div>
    @endforelse
  </div>

  <!-- Pending Requests Tab -->
  <div class="tab-content" id="pending-tab" style="display: none;">
    @forelse($pendingRequests as $request)
    <div class="friend-card d-flex align-items-center">
      <div class="friend-avatar">
        {{ strtoupper(substr($request->user->name, 0, 1)) }}
      </div>
      <div class="friend-info">
        <div class="friend-name">{{ $request->user->name }}</div>
        <div class="friend-role">{{ $request->user->role }}</div>
        <small class="text-muted">Sent {{ $request->created_at->diffForHumans() }}</small>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('profile.show', $request->user->id) }}" class="btn btn-action btn-outline-custom">
          <i class="bi bi-person"></i> View Profile
        </a>
        <form action="{{ route('friends.accept', $request->id) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-action btn-primary-custom">
            <i class="bi bi-check-circle"></i> Accept
          </button>
        </form>
        <form action="{{ route('friends.deny', $request->id) }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-action btn-secondary-custom">
            <i class="bi bi-x-circle"></i> Deny
          </button>
        </form>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <h3>No pending requests</h3>
      <p>You don't have any friend requests at the moment.</p>
    </div>
    @endforelse
  </div>

  <!-- Sent Requests Tab -->
  <div class="tab-content" id="sent-tab" style="display: none;">
    @forelse($sentRequests as $request)
    <div class="friend-card d-flex align-items-center">
      <div class="friend-avatar">
        {{ strtoupper(substr($request->friend->name, 0, 1)) }}
      </div>
      <div class="friend-info">
        <div class="friend-name">{{ $request->friend->name }}</div>
        <div class="friend-role">{{ $request->friend->role }}</div>
        <small class="text-muted">Sent {{ $request->created_at->diffForHumans() }}</small>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('profile.show', $request->friend->id) }}" class="btn btn-action btn-outline-custom">
          <i class="bi bi-person"></i> View Profile
        </a>
        <span class="text-muted">Waiting for response...</span>
      </div>
    </div>
    @empty
    <div class="empty-state">
      <i class="bi bi-send"></i>
      <h3>No sent requests</h3>
      <p>You haven't sent any friend requests.</p>
    </div>
    @endforelse
  </div>

</div>

<script>
  // Tab switching
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      // Remove active class from all buttons
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      // Add active to clicked button
      this.classList.add('active');
      
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
      
      // Show selected tab content
      const tabId = this.dataset.tab + '-tab';
      document.getElementById(tabId).style.display = 'block';
    });
  });
</script>
@endsection
