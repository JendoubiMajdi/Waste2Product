@extends('back.layout')

@section('title', 'Forum Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Forum Reports Management</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Reporter</th>
                                    <th>Reported User</th>
                                    <th>Post Content</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reports as $report)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                                </div>
                                                {{ $report->user->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                    {{ strtoupper(substr($report->post->user->name, 0, 1)) }}
                                                </div>
                                                {{ $report->post->user->name }}
                                                @if($report->post->user->isBanned())
                                                    <span class="badge bg-danger ms-2">Banned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $report->post->content }}">
                                                {{ Str::limit($report->post->content, 50) }}
                                            </div>
                                            <small class="text-muted">{{ $report->post->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $report->reason }}</span>
                                        </td>
                                        <td>
                                            @if($report->post->user->isBanned())
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-shield-exclamation"></i> Banned
                                                </span>
                                                <small class="d-block text-muted">Until: {{ $report->post->user->banned_until->format('M d, Y') }}</small>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Active
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($report->post->user->isBanned())
                                                <!-- Unban Form -->
                                                <form action="{{ route('admin.users.unban', $report->post->user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to unban this user?')">
                                                        <i class="bi bi-shield-check"></i> Unban
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Ban Form -->
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#banModal{{ $report->id }}">
                                                    <i class="bi bi-shield-exclamation"></i> Ban User
                                                </button>
                                                
                                                <!-- Ban Modal -->
                                                <div class="modal fade" id="banModal{{ $report->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Ban User: {{ $report->post->user->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.users.ban', $report->post->user) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Ban Duration (Days)</label>
                                                                        <input type="number" name="duration" class="form-control" placeholder="Enter days (0 for permanent)" required>
                                                                        <div class="form-text">Enter 0 for permanent ban</div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Ban Reason</label>
                                                                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this user is being banned..." required>{{ $report->reason }}</textarea>
                                                                    </div>
                                                                    <div class="alert alert-warning">
                                                                        <i class="bi bi-exclamation-triangle"></i>
                                                                        <strong>Warning:</strong> This user will be notified about the ban and the reason.
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="bi bi-shield-exclamation"></i> Ban User
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($reports->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-shield-check display-1 text-success"></i>
                            <h5 class="mt-3 text-muted">No Reports</h5>
                            <p class="text-muted">All clear! No reports to review at the moment.</p>
                        </div>
                    @endif
                </div>
                
                @if($reports->hasPages())
                    <div class="card-footer">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection