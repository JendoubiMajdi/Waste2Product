@extends('layouts.app')

@section('title', 'Products')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Products Catalog</h2>
        <p>Browse recycled and upcycled products from waste materials</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="products" class="products">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>All Products</h3>
            <a href="{{ route('products.create') }}" class="btn-get-started">
                <i class="bi bi-plus-circle"></i> Create New Product
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Price (TND)</th>
                                <th>Stock</th>
                                <th>Waste Source</th>
                                <th>Collection Point</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>#{{ $product->id }}</td>
                                    <td><strong>{{ $product->nom }}</strong></td>
                                    <td>{{ Str::limit($product->description, 50) }}</td>
                                    <td>
                                        @if($product->etat === 'recyclé')
                                            <span class="badge bg-success">♻️ Recyclé</span>
                                        @else
                                            <span class="badge bg-secondary">Non recyclé</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($product->prix, 2) }} TND</strong></td>
                                    <td>
                                        @if($product->quantite > 10)
                                            <span class="badge bg-success">{{ $product->quantite }}</span>
                                        @elseif($product->quantite > 0)
                                            <span class="badge bg-warning">{{ $product->quantite }}</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->waste)
                                            <small class="text-muted">
                                                #{{ $product->waste_id }} - {{ $product->waste->type }}
                                            </small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->waste && $product->waste->collectionPoint)
                                            <small class="text-muted">
                                                <i class="bi bi-building text-primary"></i>
                                                {{ $product->waste->collectionPoint->name }}
                                            </small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No products found. Create your first product!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Products Section -->

@endsection
