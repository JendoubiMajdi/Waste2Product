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
        <div class="form-group">
            <label>Products (avec quantités)</label>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Sélectionner</th>
                            <th>Produit</th>
                            <th>Stock disponible</th>
                            <th>Quantité commandée</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td style="width:120px;">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}" {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
                            </td>
                            <td>
                                #{{ $product->id }} - {{ $product->nom }}
                            </td>
                            <td>
                                {{ $product->quantite }}
                            </td>
                            <td style="width:220px;">
                                <input type="number" name="quantites[{{ $product->id }}]" class="form-control" min="1" max="{{ $product->quantite }}" value="{{ old('quantites.'.$product->id, 1) }}">
                                @error('quantites.'.$product->id)
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <small>Cochez les produits et indiquez la quantité souhaitée (ne dépasse pas le stock).</small>
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