@extends('admin.layouts.app')

@section('title', 'Post Reports')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><span class="iconify" data-icon="mdi:flag" style="color: #dc3545;"></span> Post Reports</h2>
            <p class="text-muted">Review and manage reported posts</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Reported By</th>
                            <th>Post Author</th>
                            <th>Reason</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 100px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr class="{{ $report->status === 'pending' ? 'table-warning' : '' }}">
                            <td class="fw-bold">#{{ $report->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($report->reporter->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $report->reporter->name }}</div>
                                        <small class="text-muted">{{ $report->reporter->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($report->post->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $report->post->user->name }}</div>
                                        <small class="text-muted">{{ $report->post->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 300px;" title="{{ $report->reason }}">
                                    {{ Str::limit($report->reason, 50) }}
                                </span>
                            </td>
                            <td>
                                @if($report->status === 'pending')
                                    <span class="badge bg-warning text-dark">
                                        <span class="iconify" data-icon="mdi:clock-outline"></span> Pending
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <span class="iconify" data-icon="mdi:check-circle"></span> Resolved
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                <br>
                                <small class="text-muted">{{ $report->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.post-reports.show', $report->id) }}" class="btn btn-sm btn-primary">
                                    <span class="iconify" data-icon="mdi:eye"></span> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <span class="iconify" data-icon="mdi:inbox" style="font-size: 64px; color: #ddd;"></span>
                                <p class="text-muted mt-3 mb-0">No reports found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-sm {
    font-size: 16px;
    font-weight: bold;
}
.table-warning {
    background-color: #fff3cd !important;
}
</style>
@endsection
