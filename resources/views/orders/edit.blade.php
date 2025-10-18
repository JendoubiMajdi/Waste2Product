@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Edit Order</h2>
        <p>Update order information and products</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="edit-order" class="edit-order">
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
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Order #{{ $order->id }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="statut" class="form-label">Order Status <span class="text-danger">*</span></label>
                                    <select 
                                        name="statut" 
                                        id="statut"
                                        class="form-select @error('statut') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="pending" {{ old('statut', strtolower($order->statut)) === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_delivery" {{ old('statut', strtolower($order->statut)) === 'in_delivery' ? 'selected' : '' }}>In Delivery</option>
                                        <option value="delivered" {{ old('statut', strtolower($order->statut)) === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ old('statut', strtolower($order->statut)) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                                    <select 
                                        name="client_id" 
                                        id="client_id"
                                        class="form-select @error('client_id') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="">-- Select a client --</option>
                                        @foreach($clients as $client)
                                            <option 
                                                value="{{ $client->id }}" 
                                                {{ old('client_id', $order->client_id) == $client->id ? 'selected' : '' }}
                                            >
                                                {{ $client->name }} ({{ $client->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="products" class="form-label">Products <span class="text-danger">*</span></label>
                                <select 
                                    name="products[]" 
                                    id="products" 
                                    class="form-select @error('products') is-invalid @enderror" 
                                    multiple 
                                    required
                                    size="8"
                                >
                                    @foreach($products as $product)
                                        <option 
                                            value="{{ $product->id }}" 
                                            {{ in_array($product->id, old('products', $order->products->pluck('id')->toArray())) ? 'selected' : '' }}
                                        >
                                            #{{ $product->id }} - {{ $product->nom }} (Stock: {{ $product->quantite }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('products')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Hold Ctrl (Windows) or Cmd (Mac) to select multiple products.
                                </small>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-1"></i>Update Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Edit Order Section -->

@endsection
