@extends('admin.layouts.app')

@section('title', 'Donation Approvals')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Donation Approvals</h1>
    <p style="color: #6b7280; font-size: 14px;">Review and approve donation submissions from users</p>
</div>
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 159, 67, 0.1); color: #FF9F43;">
                <span class="iconify" data-icon="mdi:clock-outline"></span>
            </div>
            <div class="stat-label">Pending</div>
            <div class="stat-value">{{ number_format($pendingCount) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(23, 174, 19, 0.1); color: #17AE13;">
                <span class="iconify" data-icon="mdi:check-circle"></span>
            </div>
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ number_format($approvedCount) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(244, 63, 94, 0.1); color: #F43F5E;">
                <span class="iconify" data-icon="mdi:close-circle"></span>
            </div>
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ number_format($rejectedCount) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(0, 146, 126, 0.1); color: var(--primary-color);">
                <span class="iconify" data-icon="mdi:hand-heart-outline"></span>
            </div>
            <div class="stat-label">Total Donations</div>
            <div class="stat-value">{{ number_format($totalCount) }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="admin-card mb-4">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.donations.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label" style="font-size: 13px; font-weight: 600;">Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="food" {{ request('type') == 'food' ? 'selected' : '' }}>Food</option>
                    <option value="clothing" {{ request('type') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                    <option value="money" {{ request('type') == 'money' ? 'selected' : '' }}>Money</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label" style="font-size: 13px; font-weight: 600;">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <span class="iconify me-1" data-icon="mingcute:filter-line"></span> Filter
                </button>
                <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
                    <span class="iconify me-1" data-icon="mingcute:close-line"></span> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Donations Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">
            <span class="iconify me-2" data-icon="mdi:format-list-checks"></span> 
            Donations List ({{ $donations->total() }})
        </h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f9fafb;">
                    <tr>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">ID</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Donor</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Type</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Quantity</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Description</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Status</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Date</th>
                        <th style="padding: 16px; font-size: 13px; font-weight: 600; color: #374151;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $donation)
                    <tr>
                        <td style="padding: 16px;"><strong>#{{ $donation->id }}</strong></td>
                        <td style="padding: 16px;">
                            <div style="font-weight: 500;">{{ $donation->user->name }}</div>
                            <small style="color: #6b7280; font-size: 12px;">{{ $donation->user->email }}</small>
                        </td>
                        <td style="padding: 16px;">
                            @if($donation->type === 'food')
                                <span class="badge" style="background-color: #17AE13; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px;">
                                    <span class="iconify me-1" data-icon="mdi:food-apple"></span> Food
                                </span>
                            @elseif($donation->type === 'clothing')
                                <span class="badge" style="background-color: #0EA5E9; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px;">
                                    <span class="iconify me-1" data-icon="mdi:tshirt-crew"></span> Clothing
                                </span>
                            @else
                                <span class="badge" style="background-color: #FF9F43; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px;">
                                    <span class="iconify me-1" data-icon="mdi:cash-multiple"></span> Money
                                </span>
                            @endif
                        </td>
                        <td style="padding: 16px;">
                            @if($donation->type === 'money')
                                <strong>{{ number_format($donation->amount, 2) }} TND</strong>
                            @else
                                <strong>{{ $donation->amount }}</strong> {{ $donation->type === 'food' ? 'kg' : 'items' }}
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 13px; color: #6b7280;">{{ Str::limit($donation->description, 50) }}</td>
                        <td style="padding: 16px;">
                            @if($donation->status === 'pending')
                                <span class="badge" style="background-color: rgba(255, 159, 67, 0.1); color: #FF9F43; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Pending</span>
                            @elseif($donation->status === 'approved')
                                <span class="badge" style="background-color: rgba(23, 174, 19, 0.1); color: #17AE13; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Approved</span>
                            @else
                                <span class="badge" style="background-color: rgba(244, 63, 94, 0.1); color: #F43F5E; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Rejected</span>
                            @endif
                        </td>
                        <td style="padding: 16px; font-size: 12px;">
                            <div>{{ $donation->created_at->format('M d, Y') }}</div>
                            <small style="color: #9ca3af;">{{ $donation->created_at->format('h:i A') }}</small>
                        </td>
                        <td style="padding: 16px;">
                            <div class="d-flex gap-2">
                                @if($donation->status === 'pending')
                                <button type="button" 
                                        class="btn btn-sm"
                                        style="background-color: rgba(23, 174, 19, 0.1); color: #17AE13; border: none; padding: 6px 12px; border-radius: 6px;"
                                        onclick="approveDonation({{ $donation->id }})"
                                        title="Approve">
                                    <span class="iconify" data-icon="mdi:check"></span>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm"
                                        style="background-color: rgba(244, 63, 94, 0.1); color: #F43F5E; border: none; padding: 6px 12px; border-radius: 6px;"
                                        onclick="rejectDonation({{ $donation->id }})"
                                        title="Reject">
                                    <span class="iconify" data-icon="mdi:close"></span>
                                </button>
                                @endif
                                
                                <form action="{{ route('admin.donations.destroy', $donation) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this donation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm"
                                            style="background-color: rgba(244, 63, 94, 0.1); color: #F43F5E; border: none; padding: 6px 12px; border-radius: 6px;"
                                            title="Delete">
                                        <span class="iconify" data-icon="mdi:delete-outline"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 48px;">
                            <span class="iconify" data-icon="mdi:inbox" style="font-size: 48px; color: #d1d5db;"></span>
                            <p style="color: #9ca3af; margin-top: 16px; font-size: 14px;">No donations found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $donations->links() }}
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #17AE13 0%, #14940f 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <span class="iconify me-2" data-icon="mdi:check-circle"></span>
                        Approve Donation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">
                        When you approve this donation, a pride post will be automatically created from the donor's account celebrating their contribution! 🎉
                    </p>
                    <div class="alert alert-info d-flex align-items-center" style="background-color: rgba(0, 146, 126, 0.1); border: none; border-radius: 8px;">
                        <span class="iconify me-2" data-icon="mdi:information" style="font-size: 24px; color: var(--primary-color);"></span>
                        <div style="font-size: 13px;">
                            The donor will receive a notification and a post will be created automatically on their behalf to share their generous contribution with the community.
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-success" style="background-color: #17AE13; border: none; border-radius: 8px;">
                        <span class="iconify me-1" data-icon="mdi:check"></span> Approve & Create Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #F43F5E 0%, #e11d48 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title">
                        <span class="iconify me-2" data-icon="mdi:close-circle"></span>
                        Reject Donation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 20px;">Please provide a reason for rejection:</p>
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label" style="font-weight: 600; font-size: 13px;">
                            Reason <span style="color: #F43F5E;">*</span>
                        </label>
                        <textarea name="rejection_reason" 
                                  id="reject_notes" 
                                  rows="4" 
                                  class="form-control" 
                                  style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                  placeholder="Explain why this donation is being rejected..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="background-color: #F43F5E; border: none; border-radius: 8px;">
                        <span class="iconify me-1" data-icon="mdi:close"></span> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function approveDonation(donationId) {
    const form = document.getElementById('approveForm');
    form.action = `/admin/donations/${donationId}/approve`;
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectDonation(donationId) {
    const form = document.getElementById('rejectForm');
    form.action = `/admin/donations/${donationId}/reject`;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>
@endpush
