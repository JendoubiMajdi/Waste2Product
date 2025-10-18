@extends('layouts.app')

@section('title', 'Deliveries')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Delivery Management</h2>
        <p>Manage and track all deliveries</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="deliveries" class="deliveries">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>All Deliveries</h3>
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
                                <th>Order ID</th>
                                <th>Client</th>
                                <th>Delivery Address</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livraisons as $livraison)
                                <tr>
                                    <td>{{ $livraison->idLivraison }}</td>
                                    <td><strong>#{{ $livraison->idOrder }}</strong></td>
                                    <td>
                                        @if($livraison->client)
                                            {{ $livraison->client->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($livraison->adresseLivraison, 40) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($livraison->dateLivraison)->format('M d, Y') }}</td>
                                    <td>
                                        @if($livraison->statut === 'delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @elseif($livraison->statut === 'in_delivery')
                                            <span class="badge bg-warning">In Delivery</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($livraison->statut) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('livraisons.show', $livraison->idLivraison) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @auth
                                            @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                                                @if($livraison->statut !== 'delivered')
                                                    <a href="{{ route('livraisons.edit', $livraison->idLivraison) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endif
                                            @endif
                                            
                                            @if(auth()->user()->role === 'admin')
                                                <form action="{{ route('livraisons.destroy', $livraison->idLivraison) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this delivery?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No deliveries found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Deliveries Section -->

@endsection
