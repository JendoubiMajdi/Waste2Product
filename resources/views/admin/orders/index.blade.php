@extends('admin.layouts.app')

@section('title', 'Orders Management')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Orders Management</h1>
    <p style="color: #6b7280; font-size: 14px;">View and manage all orders</p>
</div>

<!-- Orders Table -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0;">All Orders</h2>
        <div style="display: flex; gap: 12px;">
            <select class="form-select" style="width: auto;">
                <option>All Status</option>
                <option>En cours</option>
                <option>Livré</option>
                <option>Annulé</option>
            </select>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Order ID</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Date</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">#{{ $order->id }}</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $order->client->name ?? 'N/A' }}</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">{{ number_format($order->total, 2) }} TND</td>
                    <td style="padding: 16px 24px;">
                        <span class="badge bg-{{ $order->statut === 'livré' ? 'success' : ($order->statut === 'en cours' ? 'warning' : 'secondary') }}">
                            {{ $order->statut }}
                        </span>
                    </td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-sm btn-outline-primary" onclick='viewOrder(@json($order))' title="View Order Details" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:eye" style="font-size: 16px;"></iconify-icon>
                                <span>View</span>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick='updateOrderStatus(@json($order))' title="Update Status" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:refresh" style="font-size: 16px;"></iconify-icon>
                                <span>Status</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 48px; text-align: center; color: #6b7280;">
                        <iconify-icon icon="mdi:cart-off" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></iconify-icon>
                        <p style="margin: 0;">No orders found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($orders) && $orders->hasPages())
    <div style="margin-top: 24px;">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function viewOrder(order) {
    // Get products HTML
    let productsHtml = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';
    
    if (order.products && order.products.length > 0) {
        order.products.forEach(product => {
            const quantity = product.pivot.quantite;
            const price = parseFloat(product.prix);
            const total = quantity * price;
            productsHtml += `
                <tr>
                    <td>${product.nom}</td>
                    <td>${quantity}</td>
                    <td>${price.toFixed(2)} TND</td>
                    <td>${total.toFixed(2)} TND</td>
                </tr>
            `;
        });
    } else {
        productsHtml += '<tr><td colspan="4" class="text-center text-muted">No products</td></tr>';
    }
    
    productsHtml += '</tbody></table></div>';
    
    Swal.fire({
        title: `Order #${order.id} Details`,
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <strong>Customer:</strong> ${order.client ? order.client.name : 'N/A'}<br>
                    <strong>Email:</strong> ${order.client ? order.client.email : 'N/A'}<br>
                    <strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}<br>
                    <strong>Status:</strong> <span class="badge bg-${order.statut === 'livré' ? 'success' : (order.statut === 'en cours' ? 'warning' : 'secondary')}">${order.statut}</span><br>
                    <strong>Total:</strong> ${parseFloat(order.total).toFixed(2)} TND
                </div>
                <hr>
                <h6>Order Items:</h6>
                ${productsHtml}
            </div>
        `,
        icon: 'info',
        width: '600px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#00927E'
    });
}

function updateOrderStatus(order) {
    Swal.fire({
        title: 'Update Order Status',
        html: `
            <div style="text-align: left;">
                <p><strong>Order #${order.id}</strong></p>
                <p>Customer: ${order.client ? order.client.name : 'N/A'}</p>
                <p>Current Status: <span class="badge bg-secondary">${order.statut}</span></p>
                <hr>
                <label for="newStatus" class="form-label">Select New Status:</label>
                <select id="newStatus" class="form-select">
                    <option value="en cours" ${order.statut === 'en cours' ? 'selected' : ''}>en cours</option>
                    <option value="livré" ${order.statut === 'livré' ? 'selected' : ''}>livré</option>
                    <option value="annulé" ${order.statut === 'annulé' ? 'selected' : ''}>annulé</option>
                </select>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Update Status',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#00927E',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const newStatus = document.getElementById('newStatus').value;
            if (newStatus === order.statut) {
                Swal.showValidationMessage('Please select a different status');
                return false;
            }
            return newStatus;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const newStatus = result.value;
            
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
            fetch(`/admin/orders/${order.id}/update-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ statut: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Order status updated to: ${newStatus}`,
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
                    text: error.message || 'Failed to update order status',
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
