@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Products</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">Create Product</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>État</th>
                            <th>Prix</th>
                            <th>Waste</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->nom }}</td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->etat }}</td>
                                <td>{{ $product->prix }}</td>
                                <td>#{{ $product->waste_id }} - {{ $product->waste->type ?? '' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">Show</a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
