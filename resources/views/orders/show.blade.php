@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order Details</h1>

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
            <h5 class="card-title">Order #{{ $order->id }}</h5>
            <div class="row">
                <div class="col-md-6">
                    <p class="card-text"><strong>Date:</strong> {{ $order->date }}</p>
                    <p class="card-text"><strong>Status:</strong> <span class="badge bg-info">{{ $order->statut }}</span></p>
                    <p class="card-text"><strong>Client:</strong> {{ $order->client ? $order->client->name : 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="text-primary">Total: {{ number_format($order->total_amount, 2) }} DT</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Produits</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->products as $product)
                        <tr>
                            <td>{{ $product->nom }}</td>
                            <td>{{ number_format($product->pivot->unit_price, 2) }} DT</td>
                            <td>{{ $product->pivot->quantite }}</td>
                            <td class="text-end"><strong>{{ number_format($product->pivot->subtotal, 2) }} DT</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun produit</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end"><strong class="text-primary fs-5">{{ number_format($order->total_amount, 2) }} DT</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('orders.invoice.download', $order->id) }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf"></i> Download Invoice
                </a>
                <form action="{{ route('orders.invoice.email', $order->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to email this invoice to the client?')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-envelope"></i> Email Invoice
                    </button>
                </form>
                <a href="{{ route('orders.index') }}" class="btn btn-accent">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection