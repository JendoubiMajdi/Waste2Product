@extends('admin.layouts.app')

@section('title', 'Collection Points Management')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Collection Points Management</h1>
    <p style="color: #6b7280; font-size: 14px;">Manage waste collection points</p>
</div>

<!-- Collection Points Table -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0;">All Collection Points</h2>
        <button class="btn btn-primary" style="background: var(--admin-primary); border-color: var(--admin-primary);">
            <iconify-icon icon="mdi:plus" style="margin-right: 8px;"></iconify-icon>
            Add Collection Point
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Name</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Address</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Working Hours</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collection_points as $point)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 24px; font-size: 14px;">
                        <div class="d-flex align-items-center">
                            @if($point->image)
                                <img src="data:image/jpeg;base64,{{ $point->image }}" alt="{{ $point->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                            @else
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                                    <iconify-icon icon="mdi:map-marker" style="color: white; font-size: 24px;"></iconify-icon>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight: 500; color: #1a1a1a;">{{ $point->name }}</div>
                                <div style="font-size: 12px; color: #6b7280;">#{{ $point->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $point->address }}</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $point->formatted_working_hours ?? $point->working_hours }}</td>
                    <td style="padding: 16px 24px;">
                        @if($point->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-sm btn-outline-primary" onclick='viewCollectionPoint(@json($point))' title="View Details" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:eye" style="font-size: 16px;"></iconify-icon>
                                <span>View</span>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick='editCollectionPoint(@json($point))' title="Edit" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:pencil" style="font-size: 16px;"></iconify-icon>
                                <span>Edit</span>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $point->status === 'active' ? 'secondary' : 'success' }}" 
                                    onclick='toggleCollectionPointStatus(@json($point))' 
                                    title="{{ $point->status === 'active' ? 'Deactivate' : 'Activate' }}" 
                                    style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:{{ $point->status === 'active' ? 'pause-circle' : 'play-circle' }}" style="font-size: 16px;"></iconify-icon>
                                <span>{{ $point->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick='deleteCollectionPoint(@json($point))' title="Delete" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:delete" style="font-size: 16px;"></iconify-icon>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 48px; text-align: center; color: #6b7280;">
                        <iconify-icon icon="mdi:map-marker-off" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></iconify-icon>
                        <p style="margin: 0;">No collection points found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($collection_points) && $collection_points->hasPages())
    <div style="margin-top: 24px;">
        {{ $collection_points->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function viewCollectionPoint(point) {
    Swal.fire({
        title: point.name,
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <strong>Address:</strong> ${point.address}<br>
                    <strong>Working Hours:</strong> ${point.formatted_working_hours || point.working_hours}<br>
                    <strong>Status:</strong> <span class="badge bg-${point.status === 'active' ? 'success' : 'secondary'}">${point.status}</span><br>
                    <strong>Created:</strong> ${new Date(point.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}<br>
                </div>
            </div>
        `,
        icon: 'info',
        width: '500px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#00927E'
    });
}

function editCollectionPoint(point) {
    // Extract opening and closing times from working_hours
    const times = point.working_hours ? point.working_hours.split('-') : ['08:00', '17:00'];
    const openingTime = times[0] ? times[0].trim() : '08:00';
    const closingTime = times[1] ? times[1].trim() : '17:00';
    
    Swal.fire({
        title: 'Edit Collection Point',
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <label for="pointName" class="form-label">Name</label>
                    <input type="text" id="pointName" class="form-control" value="${point.name}">
                </div>
                <div class="mb-3">
                    <label for="pointAddress" class="form-label">Address</label>
                    <textarea id="pointAddress" class="form-control" rows="2">${point.address}</textarea>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="openingTime" class="form-label">Opening Time</label>
                        <input type="time" id="openingTime" class="form-control" value="${openingTime}">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="closingTime" class="form-label">Closing Time</label>
                        <input type="time" id="closingTime" class="form-control" value="${closingTime}">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="pointStatus" class="form-label">Status</label>
                    <select id="pointStatus" class="form-select">
                        <option value="active" ${point.status === 'active' ? 'selected' : ''}>Active</option>
                        <option value="inactive" ${point.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                    </select>
                </div>
            </div>
        `,
        icon: 'question',
        width: '600px',
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#00927E',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const name = document.getElementById('pointName').value;
            const address = document.getElementById('pointAddress').value;
            const openingTime = document.getElementById('openingTime').value;
            const closingTime = document.getElementById('closingTime').value;
            const status = document.getElementById('pointStatus').value;
            
            if (!name || !address || !openingTime || !closingTime) {
                Swal.showValidationMessage('All fields are required');
                return false;
            }
            
            const working_hours = openingTime + '-' + closingTime;
            
            return { name, address, working_hours, status };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            fetch(`/admin/collection-points/${point.id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Collection point updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update collection point');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to update collection point',
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}

function toggleCollectionPointStatus(point) {
    const newStatus = point.status === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';
    const actionCapitalized = action.charAt(0).toUpperCase() + action.slice(1);
    
    Swal.fire({
        title: `${actionCapitalized} Collection Point?`,
        html: `Are you sure you want to ${action}:<br><strong>${point.name}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Yes, ${action}!`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: newStatus === 'active' ? '#17AE13' : '#6c757d',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            fetch(`/admin/collection-points/${point.id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Collection point ${action}d successfully`,
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to update collection point status',
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}

function deleteCollectionPoint(point) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete collection point:<br><strong>${point.name}</strong><br><br>This action cannot be undone!`,
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
            fetch(`/admin/collection-points/${point.id}`, {
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
                        text: 'Collection point has been deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete collection point');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to delete collection point',
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
