@extends('admin.layouts.app')

@section('title', 'Report Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.post-reports') }}" class="btn btn-secondary mb-3">
                <span class="iconify" data-icon="mdi:arrow-left"></span> Back to Reports
            </a>
            <h2>Report #{{ $report->id }}</h2>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Report Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><span class="iconify" data-icon="mdi:flag"></span> Report Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($report->status === 'pending')
                            <span class="badge bg-warning text-dark ms-2">Pending Review</span>
                        @else
                            <span class="badge bg-success ms-2">Resolved</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Reported By:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                                    {{ strtoupper(substr($report->reporter->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $report->reporter->name }}</div>
                                    <small class="text-muted">{{ $report->reporter->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Post Author:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                                    {{ strtoupper(substr($report->post->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $report->post->user->name }}</div>
                                    <small class="text-muted">{{ $report->post->user->email }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Reported Date:</strong>
                        <div class="mt-1">{{ $report->created_at->format('F d, Y \a\t g:i A') }}</div>
                        <small class="text-muted">({{ $report->created_at->diffForHumans() }})</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Reason for Report:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $report->reason }}
                        </div>
                    </div>
                    
                    @if($report->admin_notes)
                    <hr>
                    <div class="mb-0">
                        <strong>Admin Notes:</strong>
                        <div class="mt-2 p-3 bg-light rounded">
                            {{ $report->admin_notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reported Post & Actions -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header" style="background-color: #00927E; color: white;">
                    <h5 class="mb-0"><span class="iconify" data-icon="mdi:file-document"></span> Reported Post</h5>
                </div>
                <div class="card-body">
                    @if($report->post->title)
                    <h6 class="fw-bold mb-2">{{ $report->post->title }}</h6>
                    @endif
                    
                    <p class="mb-3">{{ $report->post->content }}</p>
                    
                    @if($report->post->image)
                    <div class="mb-3">
                        <img src="data:image/jpeg;base64,{{ $report->post->image }}" class="img-fluid rounded" alt="Post image" style="max-height: 300px; object-fit: cover;">
                    </div>
                    @endif
                    
                    <small class="text-muted">
                        <span class="iconify" data-icon="mdi:clock-outline"></span>
                        Posted {{ $report->post->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>

            <!-- Admin Actions -->
            @if($report->status === 'pending')
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><span class="iconify" data-icon="mdi:tools"></span> Admin Actions</h5>
                </div>
                <div class="card-body">
                    <!-- Ban User Form -->
                    <div class="mb-4">
                        <h6 class="text-danger fw-bold mb-3">
                            <span class="iconify" data-icon="mdi:account-cancel"></span> Ban User
                        </h6>
                        <form action="{{ route('admin.post-reports.ban', $report->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Ban Duration (days) *</label>
                                <input type="number" name="ban_duration" class="form-control" min="1" max="365" value="7" required placeholder="Enter number of days">
                                <small class="text-muted">Min: 1 day, Max: 365 days</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Ban Reason *</label>
                                <textarea name="ban_reason" class="form-control" rows="3" required placeholder="Explain why this user is being banned..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to ban this user? They will not be able to access the platform for the specified duration.')">
                                <span class="iconify" data-icon="mdi:account-lock"></span> Ban User
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Delete Post Form -->
                    <div class="mb-4">
                        <h6 class="text-warning fw-bold mb-3">
                            <span class="iconify" data-icon="mdi:delete"></span> Delete Post
                        </h6>
                        <p class="small text-muted">This will permanently delete the post and send a warning to the author.</p>
                        <form action="{{ route('admin.post-reports.delete-post', $report->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to delete this post? This action cannot be undone.')">
                                <span class="iconify" data-icon="mdi:trash-can"></span> Delete Post
                            </button>
                        </form>
                    </div>

                    <hr>

                    <!-- Resolve Without Action -->
                    <div>
                        <h6 class="fw-bold mb-3" style="color: #00927E;">
                            <span class="iconify" data-icon="mdi:check-circle"></span> Resolve Report
                        </h6>
                        <form action="{{ route('admin.post-reports.resolve', $report->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Action *</label>
                                <select name="action" class="form-select" required>
                                    <option value="">Select action...</option>
                                    <option value="dismiss">Dismiss (No violation found)</option>
                                    <option value="warning">Send Warning to Author</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Admin Notes (optional)</label>
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Add any additional notes..."></textarea>
                            </div>
                            <button type="submit" class="btn w-100" style="background-color: #00927E; color: white;">
                                <span class="iconify" data-icon="mdi:check-circle"></span> Resolve Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-success">
                <h6 class="alert-heading">
                    <span class="iconify" data-icon="mdi:check-circle"></span> Report Resolved
                </h6>
                <p class="mb-0">This report has already been resolved by an administrator.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-md {
    font-weight: bold;
}
</style>
@endsection
