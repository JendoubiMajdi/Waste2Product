@extends('layouts.app')

@section('title', 'Product Details')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Product Details</h2>
        <p>View detailed information about this product</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="show-product" class="show-product">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Product #{{ $product->id }}</h5>
                        <div>
                            @auth
                                @if(Auth::user()->isAdmin() || (isset($product->waste->user_id) && $product->waste->user_id === Auth::id()))
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <h6 class="text-muted mb-2">Product Name</h6>
                                <h4 class="mb-0">{{ $product->nom }}</h4>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <h6 class="text-muted mb-2">Status</h6>
                                @if($product->etat === 'recyclé')
                                    <span class="badge bg-success fs-6">♻️ Recyclé</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Non recyclé</span>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="fs-6">{{ $product->description }}</p>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Price</h6>
                                <h4 class="text-primary mb-0">{{ number_format($product->prix, 2) }} TND</h4>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Available Stock</h6>
                                <p class="fs-5 mb-0">
                                    @if($product->quantite > 10)
                                        <span class="badge bg-success">{{ $product->quantite }} units</span>
                                    @elseif($product->quantite > 0)
                                        <span class="badge bg-warning">{{ $product->quantite }} units - Low Stock</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Waste Source</h6>
                            @if($product->waste)
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="card-title">Waste #{{ $product->waste->id }} - {{ $product->waste->type }}</h6>
                                        <p class="card-text mb-1">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <small>{{ $product->waste->localisation }}</small>
                                        </p>
                                        <p class="card-text mb-1">
                                            <i class="bi bi-box me-1"></i>
                                            <small>Quantity: {{ $product->waste->quantite }} kg</small>
                                        </p>
                                        @if($product->waste->collectionPoint)
                                            <p class="card-text mb-0">
                                                <i class="bi bi-building me-1"></i>
                                                <small>{{ $product->waste->collectionPoint->name }}</small>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-muted fst-italic">No waste source assigned</p>
                            @endif
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Created At</h6>
                                <p class="fs-6">
                                    <i class="bi bi-clock text-muted me-2"></i>
                                    {{ $product->created_at ? $product->created_at->format('F d, Y H:i') : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Last Updated</h6>
                                <p class="fs-6">
                                    <i class="bi bi-clock text-muted me-2"></i>
                                    {{ $product->updated_at ? $product->updated_at->format('F d, Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        @auth
                            @if(Auth::user()->isAdmin() || (isset($product->waste->user_id) && $product->waste->user_id === Auth::id()))
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                </div>
                            @endif
                        @endauth

                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Show Product Section -->

@endsection

