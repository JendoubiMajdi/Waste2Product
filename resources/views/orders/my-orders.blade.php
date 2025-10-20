@extends('layouts.app')

@section('title', 'My Orders')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>My Product Orders</h2>
        <p>Track your recycled and reusable product orders</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="my-orders" class="my-orders">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>My Product Orders</h3>
            <a href="{{ route('orders.create') }}" class="btn-get-started">
                <i class="bi bi-plus-circle"></i> Order New Products
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @forelse($orders as $order)
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>Order #{{ $order->id }}
                    </h5>
                    <div>
                        @if(strtolower($order->statut) === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif(strtolower($order->statut) === 'in_delivery')
                            <span class="badge bg-info">In Delivery</span>
                        @elseif(strtolower($order->statut) === 'delivered')
                            <span class="badge bg-success">Delivered</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->statut) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-calendar-event me-2"></i>Order Date</h6>
                            <p>{{ \Carbon\Carbon::parse($order->date)->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-clock me-2"></i>Estimated Delivery</h6>
                            @if($order->estimated_delivery_time)
                                <p>
                                    <strong>{{ $order->estimated_delivery_time->format('F d, Y') }}</strong><br>
                                    <small class="text-muted">Around {{ $order->estimated_delivery_time->format('h:i A') }}</small>
                                </p>
                            @else
                                <p class="text-muted">Not available yet</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-person-badge me-2"></i>Transporter</h6>
                            @if($order->transporter)
                                <p>
                                    <strong>{{ $order->transporter->name }}</strong><br>
                                    <small class="text-muted">{{ $order->transporter->email }}</small>
                                </p>
                            @else
                                <p class="text-warning">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Not yet accepted by a transporter
                                </p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2"><i class="bi bi-geo-alt me-2"></i>Delivery Address</h6>
                            <p class="mb-0">{{ $order->delivery_address ?? 'Address not provided' }}</p>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="order-timeline mt-4">
                        <h6 class="text-muted mb-3"><i class="bi bi-clock-history me-2"></i>Order Status Timeline</h6>
                        <div class="timeline">
                            <!-- Order Created -->
                            <div class="timeline-item completed">
                                <div class="timeline-marker">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Order Created</h6>
                                    <small class="text-muted">{{ $order->created_at->format('M d, Y - h:i A') }}</small>
                                </div>
                            </div>

                            <!-- Accepted by Transporter -->
                            <div class="timeline-item {{ strtolower($order->statut) !== 'pending' ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if(strtolower($order->statut) !== 'pending')
                                        <i class="bi bi-check-circle-fill"></i>
                                    @else
                                        <i class="bi bi-circle"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>Accepted by Transporter</h6>
                                    @if($order->transporter)
                                        <small class="text-muted">Assigned to {{ $order->transporter->name }}</small>
                                    @else
                                        <small class="text-warning">Waiting for acceptance</small>
                                    @endif
                                </div>
                            </div>

                            <!-- In Delivery -->
                            <div class="timeline-item {{ strtolower($order->statut) === 'in_delivery' || strtolower($order->statut) === 'delivered' ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if(strtolower($order->statut) === 'in_delivery' || strtolower($order->statut) === 'delivered')
                                        <i class="bi bi-check-circle-fill"></i>
                                    @else
                                        <i class="bi bi-circle"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>In Transit</h6>
                                    <small class="text-muted">Order is on its way</small>
                                </div>
                            </div>

                            <!-- Delivered -->
                            <div class="timeline-item {{ strtolower($order->statut) === 'delivered' ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if(strtolower($order->statut) === 'delivered')
                                        <i class="bi bi-check-circle-fill"></i>
                                    @else
                                        <i class="bi bi-circle"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>Delivered</h6>
                                    @if(strtolower($order->statut) === 'delivered')
                                        <small class="text-success">Completed successfully</small>
                                    @else
                                        <small class="text-muted">Pending delivery</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Summary -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-2"><i class="bi bi-box-seam me-2"></i>Products</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->products as $product)
                                        <tr>
                                            <td>{{ $product->nom }}</td>
                                            <td>{{ $product->pivot->quantite }}</td>
                                            <td>{{ number_format($product->prix, 2) }} TND</td>
                                            <td>{{ number_format($product->prix * $product->pivot->quantite, 2) }} TND</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>{{ number_format($order->products->sum(function($product) { return $product->prix * $product->pivot->quantite; }), 2) }} TND</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        <a href="{{ route('orders.invoice.download', $order->id) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-pdf me-1"></i>Download PDF
                        </a>
                        
                        @if($order->livraison && strtolower($order->statut) === 'in_delivery')
                            <a href="{{ route('livraisons.show', $order->livraison->idLivraison) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-truck me-1"></i>Track Delivery
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                You haven't placed any orders yet. <a href="{{ route('orders.create') }}" class="alert-link">Create your first order!</a>
            </div>
        @endforelse

    </div>
</section><!-- End My Orders Section -->

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 2px solid #e0e0e0;
}

.timeline-item.completed .timeline-marker {
    background: #00927E;
    border-color: #00927E;
    color: white;
}

.timeline-item.pending .timeline-marker {
    background: #f5f5f5;
    border-color: #e0e0e0;
    color: #999;
}

.timeline-content {
    margin-left: 10px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>

@endsection
