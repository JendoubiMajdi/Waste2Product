@extends('layouts.app')

@section('title', 'Create Order')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Order Products</h2>
        <p>Place an order for recycled and reusable products</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="create-order" class="create-order">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Order Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf

                            <!-- Hidden field for status - automatically set to pending -->
                            <input type="hidden" name="statut" value="pending">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="delivery_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                    <textarea 
                                        name="delivery_address" 
                                        id="delivery_address"
                                        class="form-control @error('delivery_address') is-invalid @enderror" 
                                        rows="3"
                                        placeholder="Enter your complete delivery address..."
                                        required>{{ old('delivery_address') }}</textarea>
                                    @error('delivery_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Please provide your full address including city and postal code. The order will be delivered directly to this address.</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Products and Quantities <span class="text-danger">*</span></label>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 80px;">Select</th>
                                                <th>Product</th>
                                                <th>Available Stock</th>
                                                <th style="width: 200px;">Order Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($products as $product)
                                                <tr>
                                                    <td class="text-center">
                                                        <input 
                                                            type="checkbox" 
                                                            name="products[]" 
                                                            value="{{ $product->id }}" 
                                                            {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}
                                                            class="form-check-input"
                                                        >
                                                    </td>
                                                    <td>
                                                        <strong>#{{ $product->id }} - {{ $product->nom }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                                    </td>
                                                    <td>
                                                        @if($product->quantite > 10)
                                                            <span class="badge bg-success">{{ $product->quantite }} units</span>
                                                        @elseif($product->quantite > 0)
                                                            <span class="badge bg-warning">{{ $product->quantite }} units</span>
                                                        @else
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input 
                                                            type="number" 
                                                            name="quantites[{{ $product->id }}]" 
                                                            class="form-control form-control-sm" 
                                                            min="1" 
                                                            max="{{ $product->quantite }}" 
                                                            value="{{ old('quantites.'.$product->id, 1) }}"
                                                            placeholder="Qty"
                                                        >
                                                        @error('quantites.'.$product->id)
                                                            <small class="text-danger">{{ $message }}</small>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">
                                                        No products available. Please create products first.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Check the products you want to order and specify the quantity (cannot exceed available stock).
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Create Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Create Order Section -->

@endsection
