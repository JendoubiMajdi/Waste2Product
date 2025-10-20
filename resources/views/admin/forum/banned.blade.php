@extends('admin.layouts.app')

@section('title', 'Banned Users')

@push('styles')
<style>
  .banned-header {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.2);
  }

  .banned-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    transition: all 0.3s ease;
    border-left: 4px solid #ef4444;
  }

  .banned-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .user-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
  }

  .user-avatar-lg {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 24px;
  }

  .user-info h5 {
    margin: 0 0 4px 0;
    color: #1f2937;
  }

  .user-info small {
    color: #6b7280;
  }

  .ban-details {
    background: #fef2f2;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 16px;
  }

  .detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #fee2e2;
  }

  .detail-row:last-child {
    border-bottom: none;
  }

  .detail-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 13px;
  }

  .detail-value {
    color: #1f2937;
    font-weight: 500;
  }

  .badge-permanent {
    background: #7f1d1d;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-temporary {
    background: #fb923c;
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .ban-reason {
    background: white;
    padding: 16px;
    border-radius: 8px;
    border-left: 3px solid #ef4444;
    margin-bottom: 16px;
  }

  .btn-unban {
    padding: 10px 24px;
    border-radius: 25px;
    background: #22c55e;
    color: white;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-unban:hover {
    background: #16a34a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
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

  .back-btn {
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

  .back-btn:hover {
    background: white;
    color: #ef4444;
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
  
  <div class="banned-header">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-2"><i class="bi bi-shield-x"></i> Banned Users</h1>
        <p class="mb-0">Manage banned users and their restrictions</p>
      </div>
      <a href="{{ route('admin.forum.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
    </div>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Banned Users List -->
  @forelse($bannedUsers as $userBan)
  <div class="banned-card">
    <div class="user-header">
      <div class="user-avatar-lg">
        {{ strtoupper(substr($userBan->user->name, 0, 1)) }}
      </div>
      <div class="user-info flex-grow-1">
        <h5>{{ $userBan->user->name }}</h5>
        <small>{{ $userBan->user->email }}</small>
      </div>
      @if($userBan->banned_until)
      <span class="badge-temporary">
        <i class="bi bi-clock"></i> {{ $userBan->daysRemaining() }} days left
      </span>
      @else
      <span class="badge-permanent">
        <i class="bi bi-infinity"></i> Permanent
      </span>
      @endif
    </div>

    <div class="ban-details">
      <div class="detail-row">
        <span class="detail-label">Banned By:</span>
        <span class="detail-value">{{ $userBan->bannedBy->name }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Ban Date:</span>
        <span class="detail-value">{{ $userBan->created_at->format('M d, Y h:i A') }}</span>
      </div>
      @if($userBan->banned_until)
      <div class="detail-row">
        <span class="detail-label">Expires:</span>
        <span class="detail-value">{{ $userBan->banned_until->format('M d, Y h:i A') }}</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Time Remaining:</span>
        <span class="detail-value">{{ $userBan->banned_until->diffForHumans() }}</span>
      </div>
      @endif
    </div>

    @if($userBan->reason)
    <div class="ban-reason">
      <div class="detail-label mb-2">BAN REASON:</div>
      <div>{{ $userBan->reason }}</div>
    </div>
    @endif

    <form action="{{ route('admin.forum.users.unban', $userBan->user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to unban {{ $userBan->user->name }}?')">
      @csrf
      <button type="submit" class="btn-unban">
        <i class="bi bi-person-check-fill"></i> Unban User
      </button>
    </form>
  </div>
  @empty
  <div class="empty-state">
    <i class="bi bi-check-circle"></i>
    <h3>No banned users</h3>
    <p>All users are currently in good standing.</p>
  </div>
  @endforelse

  <!-- Pagination -->
  @if($bannedUsers->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $bannedUsers->links() }}
  </div>
  @endif

</div>
@endsection
