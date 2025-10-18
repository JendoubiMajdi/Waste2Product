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

    <div class="card mb-3">
        <div class="card-body">
            <h5>Current Order Total: <span class="text-primary">{{ number_format($order->total_amount, 2) }} DT</span></h5>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="statut">Status</label>
                    <input type="text" name="statut" class="form-control" value="{{ old('statut', $order->statut) }}" required>
                </div>

                <div class="form-group mb-3">
                    <label for="client_id">Client</label>
                    <select name="client_id" class="form-control" required>
                        <option value="">Select a client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $order->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label>Products (with quantities)</label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock Available</th>
                                    <th>Current Qty</th>
                                    <th>New Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                @php
                                $orderProduct = $order->products->firstWhere('id', $product->id);
                                $currentQty = $orderProduct ? $orderProduct->pivot->quantite : 0;
                                $isSelected = $orderProduct !== null;
                                @endphp
                                <tr>
                                    <td style="width:80px;">
                                        <input type="checkbox" name="products[]" value="{{ $product->id }}" {{ $isSelected ? 'checked' : '' }} class="form-check-input">
                                    </td>
                                    <td>
                                        <strong>{{ $product->nom }}</strong>
                                        <br><small class="text-muted">{{ $product->description }}</small>
                                    </td>
                                    <td>
                                        <span class="text-success"><strong>{{ number_format($product->prix, 2) }} DT</strong></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->quantite + $currentQty }} units</span>
                                    </td>
                                    <td>
                                        @if($isSelected)
                                        <span class="badge bg-info">{{ $currentQty }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="width:150px;">
                                        <input type="number" name="quantites[{{ $product->id }}]" class="form-control" min="1" max="{{ $product->quantite + $currentQty }}" value="{{ old('quantites.'.$product->id, $currentQty ?: 1) }}" placeholder="Qty">
                                        @error('quantites.'.$product->id)
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">Stock available includes current order quantities. Total will be recalculated on update.</small>
                </div>

                <button type="submit" class="btn btn-primary mt-2">Update Order</button>
                <a href="{{ route('orders.index') }}" class="btn btn-accent mt-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection