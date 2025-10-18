@extends('layouts.app')

@section('title', 'Admin - Donation Management')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Donation Management</h2>
        <p>Review and approve donation submissions</p>
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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $pendingCount }}</h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $approvedCount }}</h3>
                        <p class="mb-0">Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $rejectedCount }}</h3>
                        <p class="mb-0">Rejected</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalCount }}</h3>
                        <p class="mb-0">Total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.donations.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="food" {{ request('type') == 'food' ? 'selected' : '' }}>Food</option>
                            <option value="clothing" {{ request('type') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option value="money" {{ request('type') == 'money' ? 'selected' : '' }}>Money</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Donations Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Donations List ({{ $donations->total() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Donor</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                            <tr>
                                <td><strong>#{{ $donation->id }}</strong></td>
                                <td>
                                    <div>{{ $donation->user->name }}</div>
                                    <small class="text-muted">{{ $donation->user->email }}</small>
                                </td>
                                <td>
                                    @if($donation->type === 'food')
                                        <span class="badge bg-success">
                                            <i class="bi bi-cart"></i> Food
                                        </span>
                                    @elseif($donation->type === 'clothing')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-bag"></i> Clothing
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-cash"></i> Money
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($donation->type === 'money')
                                        {{ number_format($donation->quantity, 2) }} TND
                                    @else
                                        {{ $donation->quantity }} {{ $donation->type === 'food' ? 'kg' : 'items' }}
                                    @endif
                                </td>
                                <td>{{ Str::limit($donation->description, 50) }}</td>
                                <td>
                                    @if($donation->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($donation->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $donation->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $donation->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('donations.show', $donation) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($donation->status === 'pending')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success" 
                                                onclick="approveDonation({{ $donation->id }})"
                                                title="Approve">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectDonation({{ $donation->id }})"
                                                title="Reject">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        @endif
                                        
                                        <form action="{{ route('admin.donations.destroy', $donation) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this donation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">No donations found</p>
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
    </div>
</section>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Approve Donation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this donation?</p>
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" id="approve_notes" rows="3" 
                                  class="form-control" 
                                  placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Donation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for rejection:</p>
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="admin_notes" id="reject_notes" rows="3" 
                                  class="form-control" 
                                  placeholder="Explain why this donation is being rejected..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
@endsection
