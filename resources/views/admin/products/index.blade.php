@extends('admin.layouts.app')

@section('title', 'Products Management')

@section('content')
<div style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Products Management</h1>
    <p style="color: #6b7280; font-size: 14px;">Manage all products in the marketplace</p>
</div>

<!-- Products Table -->
<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #1a1a1a; margin: 0;">All Products</h2>
        <button class="btn btn-primary" style="background: var(--admin-primary); border-color: var(--admin-primary);">
            <iconify-icon icon="mdi:plus" style="margin-right: 8px;"></iconify-icon>
            Add Product
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Product Name</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Price</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Quantity</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                    <th style="padding: 16px 24px; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr style="border-bottom: 1px solid #f3f4f6;">
                    <td style="padding: 16px 24px; font-size: 14px;">
                        <div class="d-flex align-items-center">
                            @if($product->image)
                                <img src="data:image/jpeg;base64,{{ $product->image }}" alt="{{ $product->nom }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                            @else
                                <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                                    <iconify-icon icon="mdi:package-variant" style="color: white; font-size: 24px;"></iconify-icon>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight: 500; color: #1a1a1a;">{{ $product->nom }}</div>
                                <div style="font-size: 12px; color: #6b7280;">#{{ $product->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 16px 24px; font-size: 14px; font-weight: 600;">{{ number_format($product->prix, 2) }} TND</td>
                    <td style="padding: 16px 24px; font-size: 14px;">{{ $product->quantite }}</td>
                    <td style="padding: 16px 24px;">
                        @if($product->etat === 'disponible')
                            <span class="badge bg-success">Available</span>
                        @elseif($product->etat === 'rupture')
                            <span class="badge bg-danger">Out of Stock</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($product->etat) }}</span>
                        @endif
                    </td>
                    <td style="padding: 16px 24px;">
                        <div style="display: flex; gap: 8px;">
                            <button class="btn btn-sm btn-outline-primary" onclick='viewProduct(@json($product))' title="View Product" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:eye" style="font-size: 16px;"></iconify-icon>
                                <span>View</span>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick='editProduct(@json($product))' title="Edit Product" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:pencil" style="font-size: 16px;"></iconify-icon>
                                <span>Edit</span>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick='deleteProduct(@json($product))' title="Delete Product" style="display: flex; align-items: center; gap: 4px; padding: 6px 12px;">
                                <iconify-icon icon="mdi:delete" style="font-size: 16px;"></iconify-icon>
                                <span>Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 48px; text-align: center; color: #6b7280;">
                        <iconify-icon icon="mdi:package-variant-closed-remove" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></iconify-icon>
                        <p style="margin: 0;">No products found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($products) && $products->hasPages())
    <div style="margin-top: 24px;">
        {{ $products->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function viewProduct(product) {
    Swal.fire({
        title: product.nom,
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <strong>Description:</strong> ${product.description || 'N/A'}<br>
                    <strong>Price:</strong> ${parseFloat(product.prix).toFixed(2)} TND<br>
                    <strong>Quantity:</strong> ${product.quantite}<br>
                    <strong>Status:</strong> <span class="badge bg-${product.etat === 'disponible' ? 'success' : 'danger'}">${product.etat}</span><br>
                    <strong>Created:</strong> ${new Date(product.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                </div>
            </div>
        `,
        icon: 'info',
        width: '500px',
        confirmButtonText: 'Close',
        confirmButtonColor: '#00927E'
    });
}

function editProduct(product) {
    Swal.fire({
        title: 'Edit Product',
        html: `
            <div style="text-align: left;">
                <div class="mb-3">
                    <label for="productNom" class="form-label">Product Name</label>
                    <input type="text" id="productNom" class="form-control" value="${product.nom}">
                </div>
                <div class="mb-3">
                    <label for="productDescription" class="form-label">Description</label>
                    <textarea id="productDescription" class="form-control" rows="3">${product.description || ''}</textarea>
                </div>
                <div class="mb-3">
                    <label for="productPrix" class="form-label">Price (TND)</label>
                    <input type="number" step="0.01" id="productPrix" class="form-control" value="${product.prix}">
                </div>
                <div class="mb-3">
                    <label for="productQuantite" class="form-label">Quantity</label>
                    <input type="number" id="productQuantite" class="form-control" value="${product.quantite}">
                </div>
                <div class="mb-3">
                    <label for="productEtat" class="form-label">Status</label>
                    <select id="productEtat" class="form-select">
                        <option value="disponible" ${product.etat === 'disponible' ? 'selected' : ''}>Available</option>
                        <option value="rupture" ${product.etat === 'rupture' ? 'selected' : ''}>Out of Stock</option>
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
            const nom = document.getElementById('productNom').value;
            const description = document.getElementById('productDescription').value;
            const prix = document.getElementById('productPrix').value;
            const quantite = document.getElementById('productQuantite').value;
            const etat = document.getElementById('productEtat').value;
            
            if (!nom || !prix || !quantite) {
                Swal.showValidationMessage('Name, price, and quantity are required');
                return false;
            }
            
            return { nom, description, prix, quantite, etat };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            fetch(`/admin/products/${product.id}`, {
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
                        text: 'Product updated successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Failed to update product');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#F43F5E'
                });
            });
        }
    });
}

function deleteProduct(product) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete product:<br><strong>${product.nom}</strong><br><br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#F43F5E',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            fetch(`/admin/products/${product.id}`, {
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
                        text: 'Product has been deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#00927E'
                    }).then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Failed to delete product');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
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
