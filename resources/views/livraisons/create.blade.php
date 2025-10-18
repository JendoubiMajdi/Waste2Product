@extends('layouts.app')

@section('title', 'Accept Delivery')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Accept Delivery</h2>
        <p>Accept and schedule this order for delivery</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="accept-delivery" class="accept-delivery">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Delivery Details</h5>
                    </div>
                    <div class="card-body">
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

                        <form action="{{ route('livraisons.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="idOrder" value="{{ request('idOrder') }}">
                            <input type="hidden" name="idClient" value="{{ request('idClient') }}">
                            <input type="hidden" name="statut" value="in_delivery">

                            <div class="mb-3">
                                <label for="adresseLivraison" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control @error('adresseLivraison') is-invalid @enderror" 
                                    id="adresseLivraison" 
                                    name="adresseLivraison" 
                                    rows="3" 
                                    required>{{ old('adresseLivraison') }}</textarea>
                                @error('adresseLivraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Enter the full delivery address</small>
                            </div>

                            <div class="mb-3">
                                <label for="dateLivraison" class="form-label">Scheduled Delivery Date <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('dateLivraison') is-invalid @enderror" 
                                    id="dateLivraison" 
                                    name="dateLivraison" 
                                    value="{{ old('dateLivraison') }}"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    required>
                                @error('dateLivraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select a date for delivery (must be at least tomorrow)</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Note:</strong> By accepting this delivery, you confirm that you will deliver this order on the scheduled date. The order status will be updated to "In Delivery".
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Accept & Schedule Delivery
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Accept Delivery Section -->

@endsection
