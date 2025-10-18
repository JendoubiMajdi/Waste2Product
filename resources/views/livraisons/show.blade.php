@extends('layouts.app')

@section('title', 'Delivery Details')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Delivery Details</h2>
        <p>View delivery information</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="delivery-show" class="delivery-show">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Delivery #{{ $livraison->idLivraison }}</h5>
                        <span class="badge 
                            @if($livraison->statut === 'delivered') bg-success
                            @elseif($livraison->statut === 'in_delivery') bg-warning
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($livraison->statut) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Order ID</label>
                                <p class="fw-bold">#{{ $livraison->idOrder }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Client</label>
                                <p class="fw-bold">
                                    @if($livraison->client)
                                        {{ $livraison->client->name }}
                                        <br>
                                        <small class="text-muted">{{ $livraison->client->email }}</small>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted mb-1">Delivery Address</label>
                            <p class="fw-bold">{{ $livraison->adresseLivraison }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Scheduled Date</label>
                                <p class="fw-bold">{{ \Carbon\Carbon::parse($livraison->dateLivraison)->format('F d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted mb-1">Created At</label>
                                <p class="fw-bold">{{ $livraison->created_at->format('F d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($livraison->order)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Order Status:</strong> {{ ucfirst($livraison->order->statut) }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('livraisons.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
                            @auth
                                @if(auth()->user()->role === 'transporter' || auth()->user()->role === 'admin')
                                    @if($livraison->statut !== 'delivered')
                                        <a href="{{ route('livraisons.edit', $livraison->idLivraison) }}" class="btn btn-warning">
                                            <i class="bi bi-pencil me-1"></i> Edit Delivery
                                        </a>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Delivery Show Section -->

@endsection
