@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Users Management</h1>
    <p style="color: #6b7280; font-size: 14px;">Manage all users, roles, and permissions</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="admin-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--admin-primary) 0%, #008a74 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <iconify-icon icon="mdi:account-group"></iconify-icon>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1a1a1a;">{{ $stats['total'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280;">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <iconify-icon icon="mdi:shield-account"></iconify-icon>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1a1a1a;">{{ $stats['admins'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280;">Admins</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <iconify-icon icon="mdi:truck"></iconify-icon>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1a1a1a;">{{ $stats['collectors'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280;">Collectors</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <iconify-icon icon="mdi:account"></iconify-icon>
                </div>
                <div>
                    <div style="font-size: 24px; font-weight: 700; color: #1a1a1a;">{{ $stats['customers'] ?? 0 }}</div>
                    <div style="font-size: 13px; color: #6b7280;">Customers</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0;">All Users</h2>
        <button class="btn btn-primary" style="background: var(--admin-primary); border-color: var(--admin-primary);">
            <iconify-icon icon="mdi:plus" style="margin-right: 8px;"></iconify-icon>
            Add User
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">ID</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Name</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Role</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Joined</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 24px; font-size: 14px;">#{{ $user->id }}</td>
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 500;">{{ $user->name }}</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $user->email }}</td>
                    <td style="padding: 16px 24px;">
                        @if($user->role === 'admin')
                            <span class="badge bg-danger">Admin</span>
                        @elseif($user->role === 'collector')
                            <span class="badge bg-success">Collector</span>
                        @elseif($user->role === 'transporter')
                            <span class="badge bg-info">Transporter</span>
                        @else
                            <span class="badge bg-secondary">Customer</span>
                        @endif
                    </td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $user->created_at->format('M d, Y') }}</td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-sm btn-outline-primary" onclick='viewUser(@json($user))' title="View Details" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:eye" style="font-size: 16px;"></iconify-icon>
                                <span>View</span>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick='editUser(@json($user))' title="Edit User" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:pencil" style="font-size: 16px;"></iconify-icon>
                                <span>Edit</span>
                            </button>
                            @if($user->id !== Auth::id())
                            <button class="btn btn-sm btn-outline-danger" onclick='deleteUser(@json($user))' title="Delete User" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:delete" style="font-size: 16px;"></iconify-icon>
                                <span>Delete</span>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 48px; text-align: center; color: #6b7280;">
                        <iconify-icon icon="mdi:account-off" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></iconify-icon>
                        <p style="margin: 0;">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($users) && $users->hasPages())
    <div style="margin-top: 24px;">
        {{ $users->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function viewUser(user) {
    Swal.fire({
        title: `User #${user.id} Details`,
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #00927E 0%, #00a88f 100%); margin: 0 auto; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 700;">
                            ${user.name.charAt(0).toUpperCase()}
                        </div>
                    </div>
                    <strong>Name:</strong> ${user.name}<br>
                    <strong>Email:</strong> ${user.email}<br>
                    <strong>Role:</strong> <span class="badge bg-${user.role === 'admin' ? 'danger' : (user.role === 'collector' ? 'success' : (user.role === 'transporter' ? 'info' : 'secondary'))}">${user.role}</span><br>
                    <strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}<br>
                </div>
            </div>
        `,
        icon: 'info',
        width: '500px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#00927E'
    });
}

function editUser(user) {
    Swal.fire({
        title: 'Edit User',
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <label for="userName" class="form-label">Name</label>
                    <input type="text" id="userName" class="form-control" value="${user.name}">
                </div>
                <div class="mb-3">
                    <label for="userEmail" class="form-label">Email</label>
                    <input type="email" id="userEmail" class="form-control" value="${user.email}">
                </div>
                <div class="mb-3">
                    <label for="userRole" class="form-label">Role</label>
                    <select id="userRole" class="form-select">
                        <option value="customer" ${user.role === 'customer' ? 'selected' : ''}>Customer</option>
                        <option value="collector" ${user.role === 'collector' ? 'selected' : ''}>Collector</option>
                        <option value="transporter" ${user.role === 'transporter' ? 'selected' : ''}>Transporter</option>
                        <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </div>
            </div>
        `,
        icon: 'question',
        width: '500px',
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#00927E',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const name = document.getElementById('userName').value;
            const email = document.getElementById('userEmail').value;
            const role = document.getElementById('userRole').value;
            
            if (!name || !email) {
                Swal.showValidationMessage('Name and email are required');
                return false;
            }
            
            return { name, email, role };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            
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
            fetch(`/admin/users/${user.id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'User updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update user');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to update user',
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}

function deleteUser(user) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete user:<br><strong>${user.name}</strong><br><br>This action cannot be undone!`,
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
            fetch(`/admin/users/${user.id}`, {
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
                        text: 'User has been deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete user');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to delete user',
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
