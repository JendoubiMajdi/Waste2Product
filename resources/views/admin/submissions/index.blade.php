@extends('admin.layouts.app')

@section('title', 'Challenge Submissions')

@section('content')
<div class="d-flex justify-content-between align-items-center" style="margin-bottom: 32px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Challenge Submissions</h1>
        <p style="color: #6b7280; font-size: 14px; margin: 0;">Review and approve challenge submissions</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-12">
        <div class="admin-card">
            @if($submissions->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Challenge</th>
                                <th>Proof</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #12a16b, #0a3223); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; margin-right: 12px;">
                                                {{ substr($submission->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500; color: #1a1a1a;">{{ $submission->user->name }}</div>
                                                <div style="font-size: 12px; color: #6b7280;">{{ $submission->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div style="font-weight: 500; color: #1a1a1a;">{{ $submission->challenge->title }}</div>
                                            <div style="font-size: 12px; color: #6b7280;">{{ $submission->challenge->points }} points</div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#proofModal{{ $submission->id }}">
                                            <iconify-icon icon="mdi:eye" style="margin-right: 4px;"></iconify-icon>
                                            View Proof
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($submission->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($submission->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                            
                                            @if($submission->review_notes)
                                                @php
                                                    $aiReview = json_decode($submission->review_notes, true);
                                                @endphp
                                                @if(isset($aiReview['ai_approved']))
                                                    <span class="badge bg-info" style="font-size: 10px;">
                                                        <iconify-icon icon="mdi:robot" width="12"></iconify-icon> AI
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td style="color: #6b7280; font-size: 13px;">
                                        {{ $submission->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td>
                                        @if($submission->status === 'pending')
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <iconify-icon icon="mdi:check" style="margin-right: 4px;"></iconify-icon>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.submissions.reject', $submission) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <iconify-icon icon="mdi:close" style="margin-right: 4px;"></iconify-icon>
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span style="color: #6b7280; font-size: 13px;">
                                                {{ ucfirst($submission->status) }} {{ $submission->reviewed_at ? 'on ' . $submission->reviewed_at->format('M d, Y') : '' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Proof Modal -->
                                <div class="modal fade" id="proofModal{{ $submission->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Submission Proof - {{ $submission->challenge->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Submitted by:</strong> {{ $submission->user->name }}
                                                </div>
                                                
                                                @if($submission->description)
                                                    <div class="mb-3">
                                                        <strong>Description:</strong>
                                                        <p class="mt-2">{{ $submission->description }}</p>
                                                    </div>
                                                @endif

                                                @if($submission->proof_image)
                                                    <div class="mb-3">
                                                        <strong>Proof Image:</strong>
                                                        <div class="mt-2">
                                                            <img src="data:image/jpeg;base64,{{ $submission->proof_image }}" 
                                                                 alt="Proof" 
                                                                 class="img-fluid rounded"
                                                                 style="max-height: 500px; width: auto;">
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($submission->review_notes)
                                                    @php
                                                        $aiReview = json_decode($submission->review_notes, true);
                                                    @endphp
                                                    @if(isset($aiReview['ai_reason']))
                                                        <div class="mb-3">
                                                            <div class="alert alert-info">
                                                                <strong><iconify-icon icon="mdi:robot" style="margin-right: 4px;"></iconify-icon> AI Analysis:</strong>
                                                                <p class="mb-2 mt-2">{{ $aiReview['ai_reason'] }}</p>
                                                                <div class="d-flex gap-3">
                                                                    <span class="badge {{ $aiReview['ai_approved'] ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $aiReview['ai_approved'] ? 'AI Approved' : 'AI Rejected' }}
                                                                    </span>
                                                                    <span class="badge bg-secondary">
                                                                        Confidence: {{ round($aiReview['ai_confidence'] * 100) }}%
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                @if($submission->status === 'pending')
                                                    <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">
                                                            <iconify-icon icon="mdi:check" style="margin-right: 4px;"></iconify-icon>
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.submissions.reject', $submission) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">
                                                            <iconify-icon icon="mdi:close" style="margin-right: 4px;"></iconify-icon>
                                                            Reject
                                                        </button>
                                                    </form>
                                                @endif
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $submissions->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 24px;">
                    <iconify-icon icon="mdi:file-document-outline" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;"></iconify-icon>
                    <h3 style="font-size: 18px; font-weight: 600; color: #6b7280; margin-bottom: 8px;">No Submissions Yet</h3>
                    <p style="color: #9ca3af; font-size: 14px;">Challenge submissions will appear here</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
