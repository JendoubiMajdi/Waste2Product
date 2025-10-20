@extends('layouts.app')

@section('title', 'Orders')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Product Orders</h2>
        <p>Manage recycled and reusable product orders</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="orders" class="orders">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>
                @if(request('status') === 'pending')
                    Pending Orders
                @elseif(request('status') === 'in_delivery')
                    In Delivery
                @elseif(request('status') === 'delivered')
                    Completed Deliveries
                @else
                    All Orders
                @endif
            </h3>
            @auth
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'collector')
                    <a href="{{ route('orders.create') }}" class="btn-get-started">
                        <i class="bi bi-plus-circle"></i> Create New Order
                    </a>
                @endif
            @endauth
        </div>

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

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($order->date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($order->client)
                                            <i class="bi bi-person-circle text-primary me-1"></i>
                                            {{ $order->client->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($order->statut) === 'completed' || strtolower($order->statut) === 'livré')
                                            <span class="badge bg-success">{{ $order->statut }}</span>
                                        @elseif(strtolower($order->statut) === 'pending' || strtolower($order->statut) === 'en cours')
                                            <span class="badge bg-warning">{{ $order->statut }}</span>
                                        @elseif(strtolower($order->statut) === 'cancelled' || strtolower($order->statut) === 'annulé')
                                            <span class="badge bg-danger">{{ $order->statut }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $order->statut }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('orders.invoice.download', $order->id) }}" class="btn btn-sm btn-outline-danger" title="Download PDF Invoice">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        @auth
                                            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'collector')
                                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                                                @if($order->client)
                                                    @if(strtolower($order->statut) === 'pending' || strtolower($order->statut) === 'en cours')
                                                        <a href="{{ route('livraisons.create', ['idOrder' => $order->id, 'idClient' => $order->client->id]) }}" class="btn btn-sm btn-success" title="Accept Delivery">
                                                            <i class="bi bi-check-circle"></i> Accept Delivery
                                                        </a>
                                                    @elseif(strtolower($order->statut) === 'in_delivery' || strtolower($order->statut) === 'en livraison')
                                                        <span class="badge bg-info">
                                                            <i class="bi bi-truck"></i> In Transit
                                                        </span>
                                                    @endif
                                                @endif
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No orders found. Create your first order!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Orders Section -->

@endsection
