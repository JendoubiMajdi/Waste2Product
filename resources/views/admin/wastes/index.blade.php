@extends('admin.layouts.app')

@section('title', 'Wastes Management')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Wastes Management</h1>
    <p style="color: #6b7280; font-size: 14px;">Monitor and manage waste submissions</p>
</div>

<!-- Wastes Table -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0;">All Wastes</h2>
        <div style="display: flex; gap: 12px;">
            <select class="form-select" style="width: auto;">
                <option>All Types</option>
                <option>Plastique</option>
                <option>Verre</option>
                <option>Métal</option>
                <option>Papier</option>
            </select>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Type</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Quantity (kg)</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">User</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wastes as $waste)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 24px; font-size: 14px;">
                        <div class="d-flex align-items-center">
                            @if($waste->image)
                                <img src="data:image/jpeg;base64,{{ $waste->image }}" alt="{{ $waste->type }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                            @else
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                                    <iconify-icon icon="mdi:delete" style="color: white; font-size: 24px;"></iconify-icon>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight: 500; color: #1a1a1a;">{{ $waste->type }}</div>
                                <div style="font-size: 12px; color: #6b7280;">#{{ $waste->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">{{ number_format($waste->quantite, 2) }} kg</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $waste->user->name ?? 'N/A' }}</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $waste->created_at->format('M d, Y') }}</td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-sm btn-outline-primary" onclick='viewWaste(@json($waste))' title="View Details" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:eye" style="font-size: 16px;"></iconify-icon>
                                <span>View</span>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick='approveWaste(@json($waste))' title="Approve/Process" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:check-circle" style="font-size: 16px;"></iconify-icon>
                                <span>Approve</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick='deleteWaste(@json($waste))' title="Delete" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:delete" style="font-size: 16px;"></iconify-icon>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 48px; text-align: center; color: #6b7280;">
                        <iconify-icon icon="mdi:delete-empty" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></iconify-icon>
                        <p style="margin: 0;">No wastes found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($wastes) && $wastes->hasPages())
    <div style="margin-top: 24px;">
        {{ $wastes->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function viewWaste(waste) {
    Swal.fire({
        title: `Waste Submission #${waste.id}`,
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <strong>Type:</strong> ${waste.type}<br>
                    <strong>Quantity:</strong> ${parseFloat(waste.quantite).toFixed(2)} kg<br>
                    <strong>Submitted by:</strong> ${waste.user ? waste.user.name : 'N/A'}<br>
                    <strong>User Email:</strong> ${waste.user ? waste.user.email : 'N/A'}<br>
                    <strong>Submission Date:</strong> ${new Date(waste.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}<br>
                </div>
            </div>
        `,
        icon: 'info',
        width: '500px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#00927E'
    });
}

function approveWaste(waste) {
    Swal.fire({
        title: 'Approve Waste Submission',
        html: `
            <div style="text-align: left;">
                <p><strong>Waste #${waste.id}</strong></p>
                <p>Type: <strong>${waste.type}</strong></p>
                <p>Quantity: <strong>${parseFloat(waste.quantite).toFixed(2)} kg</strong></p>
                <p>Submitted by: <strong>${waste.user ? waste.user.name : 'N/A'}</strong></p>
                <hr>
                <p class="text-muted">This will mark the waste as processed and approved.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Approve & Process',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#17AE13',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            fetch(`/admin/wastes/${waste.id}/approve`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Approved!',
                        text: 'Waste submission has been approved and processed',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to approve waste');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to approve waste submission',
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}

function deleteWaste(waste) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete waste submission:<br><strong>${waste.type} (${parseFloat(waste.quantite).toFixed(2)} kg)</strong><br><br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#F43F5E',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            fetch(`/admin/wastes/${waste.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Waste submission has been deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete waste');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to delete waste submission',
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}
</script>
@endpush

@endsection
