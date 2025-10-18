@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Create Order</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="statut">Status</label>
                    <input type="text" name="statut" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label>Products (avec quantités)</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock Available</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td style="width:80px;">
                                        <input type="checkbox" name="products[]" value="{{ $product->id }}" {{ in_array($product->id, old('products', [])) ? 'checked' : '' }} class="form-check-input">
                                    </td>
                                    <td>
                                        <strong>{{ $product->nom }}</strong>
                                        <br><small class="text-muted">{{ $product->description }}</small>
                                    </td>
                                    <td>
                                        <span class="text-success"><strong>{{ number_format($product->prix, 2) }} DT</strong></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->quantite }} units</span>
                                    </td>
                                    <td style="width:150px;">
                                        <input type="number" name="quantites[{{ $product->id }}]" class="form-control" min="1" max="{{ $product->quantite }}" value="{{ old('quantites.'.$product->id, 1) }}" placeholder="Qty">
                                        @error('quantites.'.$product->id)
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No products available in stock</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Select products and specify quantities. Total will be calculated automatically.</small>
                </div>
                <div class="form-group">
                    <label for="client_id">Client</label>
                    <select name="client_id" class="form-control" required>
                        <option value="">Select a client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Create</button>
                <a href="{{ route('orders.index') }}" class="btn btn-accent mt-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection