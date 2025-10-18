@extends('layouts.app')

@section('title', 'Admin - Forum Reports')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Forum Reports & Moderation</h2>
        <p>Review reported content and manage user bans</p>
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

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $reports->total() }}</h3>
                        <p class="mb-0">Total Reports</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $bannedUsersCount }}</h3>
                        <p class="mb-0">Banned Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $reportsToday }}</h3>
                        <p class="mb-0">Reports Today</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Banned Users Section -->
        @if($bannedUsers->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-x"></i> Currently Banned Users
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Banned Until</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bannedUsers as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->banned_until)
                                        {{ $user->banned_until->format('M d, Y h:i A') }}
                                    @else
                                        <span class="badge bg-danger">Permanent</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($user->ban_reason, 50) }}</td>
                                <td>
                                    @if($user->banned_until)
                                        <span class="badge bg-warning text-dark">
                                            {{ now()->diffInDays($user->banned_until) }} days left
                                        </span>
                                    @else
                                        <span class="badge bg-danger">Permanent</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.forum.unban', $user) }}" method="POST" 
                                          onsubmit="return confirm('Unban {{ $user->name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-person-check"></i> Unban
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Reports Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-flag"></i> Reported Posts ({{ $reports->total() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Report ID</th>
                                <th>Post</th>
                                <th>Author</th>
                                <th>Reported By</th>
                                <th>Reason</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td><strong>#{{ $report->id }}</strong></td>
                                <td>
                                    <div style="max-width: 300px;">
                                        <strong>{{ Str::limit($report->post->title, 40) }}</strong>
                                        <p class="mb-0 text-muted small">
                                            {{ Str::limit($report->post->content, 80) }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $report->post->user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $report->post->user->email }}</small>
                                        @if($report->post->user->banned_until)
                                        <br>
                                        <span class="badge bg-danger">Banned</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        {{ $report->reporter->name }}
                                        <br>
                                        <small class="text-muted">{{ $report->reporter->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $report->reason }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $report->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group-vertical gap-1">
                                        <a href="{{ route('forum.show', $report->post) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank">
                                            <i class="bi bi-eye"></i> View Post
                                        </a>
                                        
                                        @if(!$report->post->user->banned_until)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                onclick="banUser({{ $report->post->user->id }}, '{{ $report->post->user->name }}')">
                                            <i class="bi bi-person-slash"></i> Ban User
                                        </button>
                                        @endif
                                        
                                        <form action="{{ route('forum.posts.destroy', $report->post) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Delete this reported post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-trash"></i> Delete Post
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No reports! Community is behaving well.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>
</section>

<!-- Ban User Modal -->
<div class="modal fade" id="banModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="banForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Ban User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>You are about to ban <strong id="userName"></strong></p>
                    
                    <div class="mb-3">
                        <label for="ban_duration" class="form-label">Ban Duration</label>
                        <select name="ban_duration" id="ban_duration" class="form-select" required>
                            <option value="1">1 Day</option>
                            <option value="3">3 Days</option>
                            <option value="7" selected>7 Days</option>
                            <option value="14">14 Days</option>
                            <option value="30">30 Days</option>
                            <option value="permanent">Permanent</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ban_reason" class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="ban_reason" id="ban_reason" rows="3" 
                                  class="form-control" 
                                  placeholder="Explain why this user is being banned..."
                                  required></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Warning:</strong> Banned users cannot post, comment, or like until the ban expires.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-person-slash"></i> Ban User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function banUser(userId, userName) {
    document.getElementById('userName').textContent = userName;
    const form = document.getElementById('banForm');
    form.action = `/admin/forum/users/${userId}/ban`;
    const modal = new bootstrap.Modal(document.getElementById('banModal'));
    modal.show();
}
</script>
@endsection
