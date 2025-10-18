@extends('layouts.app')

@section('title', 'Order Details')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Order Details</h2>
        <p>View detailed information about this order</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="show-order" class="show-order">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Order #{{ $order->id }}</h5>
                        <div>
                            @auth
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'collector')
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-light me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Order Date</h6>
                                <p class="fs-6">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>
                                    {{ \Carbon\Carbon::parse($order->date)->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Status</h6>
                                <p class="fs-6">
                                    @if(strtolower($order->statut) === 'delivered' || strtolower($order->statut) === 'livré')
                                        <span class="badge bg-success fs-6">Delivered</span>
                                    @elseif(strtolower($order->statut) === 'in_delivery' || strtolower($order->statut) === 'en livraison')
                                        <span class="badge bg-info fs-6">In Delivery</span>
                                    @elseif(strtolower($order->statut) === 'pending' || strtolower($order->statut) === 'en cours')
                                        <span class="badge bg-warning fs-6">Pending</span>
                                    @elseif(strtolower($order->statut) === 'cancelled' || strtolower($order->statut) === 'annulé')
                                        <span class="badge bg-danger fs-6">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">{{ $order->statut }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Client</h6>
                                <p class="fs-6">
                                    @if($order->client)
                                        <i class="bi bi-person-circle text-primary me-1"></i>
                                        {{ $order->client->name }}
                                        <br>
                                        <small class="text-muted">{{ $order->client->email }}</small>
                                    @else
                                        <span class="text-muted fst-italic">No client assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($order->delivery_address || $order->transporter || $order->estimated_delivery_time)
                            <hr class="my-3">
                            <div class="row mb-3">
                                @if($order->delivery_address)
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2"><i class="bi bi-geo-alt text-primary me-2"></i>Delivery Address</h6>
                                        <p class="fs-6">{{ $order->delivery_address }}</p>
                                    </div>
                                @endif
                                
                                @if($order->transporter)
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2"><i class="bi bi-person-badge text-primary me-2"></i>Transporter</h6>
                                        <p class="fs-6">
                                            {{ $order->transporter->name }}
                                            <br>
                                            <small class="text-muted">{{ $order->transporter->email }}</small>
                                        </p>
                                    </div>
                                @endif
                            </div>

                            @if($order->estimated_delivery_time)
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2"><i class="bi bi-clock text-primary me-2"></i>Estimated Delivery</h6>
                                        <p class="fs-6">
                                            {{ $order->estimated_delivery_time->format('F d, Y') }} at {{ $order->estimated_delivery_time->format('h:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Products</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Ordered Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->products as $product)
                                        <tr>
                                            <td><strong>#{{ $product->id }}</strong></td>
                                            <td>
                                                {{ $product->nom }}
                                                <br>
                                                <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                            </td>
                                            <td><strong>{{ $product->pivot->quantite }}</strong> units</td>
                                            <td>{{ number_format($product->prix, 2) }} TND</td>
                                            <td><strong>{{ number_format($product->prix * $product->pivot->quantite, 2) }} TND</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted">
                                                No products in this order
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($order->products->isNotEmpty())
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                            <td>
                                                <strong class="text-primary fs-5">
                                                    {{ number_format($order->products->sum(function($product) {
                                                        return $product->prix * $product->pivot->quantite;
                                                    }), 2) }} TND
                                                </strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    @auth
                        @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                            @if($order->client && (strtolower($order->statut) === 'pending' || strtolower($order->statut) === 'en cours'))
                                <a href="{{ route('livraisons.create', ['idOrder' => $order->id, 'idClient' => $order->client->id]) }}" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Accept Delivery
                                </a>
                            @elseif(strtolower($order->statut) === 'in_delivery' || strtolower($order->statut) === 'en livraison')
                                @php
                                    $livraison = \App\Models\Livraison::where('idOrder', $order->id)->first();
                                @endphp
                                @if($livraison && auth()->user()->id === $order->transporter_id)
                                    <a href="{{ route('livraisons.edit', $livraison->idLivraison) }}" class="btn btn-warning">
                                        <i class="bi bi-check-square me-1"></i>Mark as Delivered
                                    </a>
                                @endif
                                <div class="alert alert-info mb-0 ms-2">
                                    <i class="bi bi-truck me-2"></i>This order is currently in delivery
                                </div>
                            @elseif(strtolower($order->statut) === 'delivered' || strtolower($order->statut) === 'livré')
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle me-2"></i>This order has been delivered
                                </div>
                            @endif
                        @endif
                        
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'collector')
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>

            </div>
        </div>

    </div>
</section><!-- End Show Order Section -->

@endsection

