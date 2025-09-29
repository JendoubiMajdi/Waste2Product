@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1>Edit Order</h1>

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
    <form action="{{ route('orders.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="statut">Status</label>
            <input type="text" name="statut" class="form-control" value="{{ $order->statut }}" required>
        </div>
        <div class="form-group">
            <label for="products">Products</label>
            <select name="products[]" class="form-control" multiple required>
                @foreach($products as $product)
                <option value="{{ $product->id }}" {{ in_array($product->id, $order->products->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $product->nom }}</option>
                @endforeach
            </select>
            <small>Select one or more products for this order.</small>
        </div>
        <div class="form-group">
            <label for="client_id">Client</label>
            <select name="client_id" class="form-control" required>
                <option value="">Select a client</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ $order->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                @endforeach
            </select>
        </div>
    <button type="submit" class="btn btn-primary mt-2">Update</button>
    <a href="{{ route('orders.index') }}" class="btn btn-accent mt-2">Cancel</a>
    </form>
    </div>
    </div>
</div>
@endsection