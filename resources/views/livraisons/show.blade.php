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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Main Delivery Information -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
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

                        @if($livraison->livreur)
                            <div class="mb-3">
                                <label class="text-muted mb-1">Assigned Transporter</label>
                                <p class="fw-bold">
                                    <i class="bi bi-person-badge"></i> {{ $livraison->livreur->name }}
                                    <br>
                                    <small class="text-muted">{{ $livraison->livreur->email }}</small>
                                </p>
                            </div>
                        @endif

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
                                        <div>
                                            <form action="{{ route('livraisons.markDelivered', $livraison->idLivraison) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Mark this delivery as delivered?')">
                                                    <i class="bi bi-check-circle me-1"></i> Mark as Delivered
                                                </button>
                                            </form>
                                            <a href="{{ route('livraisons.edit', $livraison->idLivraison) }}" class="btn btn-warning">
                                                <i class="bi bi-pencil me-1"></i> Edit Delivery
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Delivery Proof Section (if uploaded) -->
                @if($livraison->delivery_proof_photo || $livraison->delivery_signature || $livraison->delivery_notes)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-camera me-2"></i>Delivery Proof</h5>
                        </div>
                        <div class="card-body">
                            @if($livraison->delivery_proof_photo)
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Delivery Photo</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $livraison->delivery_proof_photo) }}" alt="Delivery Proof" class="img-fluid rounded" style="max-height: 400px;">
                                    </div>
                                </div>
                            @endif

                            @if($livraison->delivery_signature)
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Signature</label>
                                    <div>
                                        <img src="{{ asset('storage/' . $livraison->delivery_signature) }}" alt="Signature" class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>
                            @endif

                            @if($livraison->delivery_notes)
                                <div class="mb-3">
                                    <label class="text-muted mb-1">Delivery Notes</label>
                                    <p class="fw-bold">{{ $livraison->delivery_notes }}</p>
                                </div>
                            @endif

                            @if($livraison->proof_uploaded_at)
                                <div class="text-muted">
                                    <small><i class="bi bi-clock"></i> Uploaded on {{ $livraison->proof_uploaded_at->format('F d, Y H:i') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Client Confirmation (if confirmed) -->
                @if($livraison->client_confirmed)
                    <div class="card shadow-sm mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Delivery Confirmed</h5>
                        </div>
                        <div class="card-body">
                            <p><i class="bi bi-person-check"></i> <strong>Confirmed by client</strong></p>
                            @if($livraison->client_confirmation_notes)
                                <div class="mb-2">
                                    <label class="text-muted mb-1">Client Notes</label>
                                    <p class="fw-bold">{{ $livraison->client_confirmation_notes }}</p>
                                </div>
                            @endif
                            <div class="text-muted">
                                <small><i class="bi bi-clock"></i> Confirmed on {{ $livraison->client_confirmed_at->format('F d, Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-lg-4">
                <!-- Upload Proof Form (for Transporter) -->
                @auth
                    @if(auth()->id() === $livraison->livreur_id && $livraison->statut !== 'delivered')
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Delivery Proof</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('livraisons.uploadProof', $livraison->idLivraison) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="delivery_proof_photo" class="form-label">
                                            <i class="bi bi-camera"></i> Delivery Photo
                                        </label>
                                        <input type="file" class="form-control @error('delivery_proof_photo') is-invalid @enderror" id="delivery_proof_photo" name="delivery_proof_photo" accept="image/jpeg,image/png,image/jpg">
                                        <small class="text-muted">Max size: 5MB (JPEG, PNG, JPG)</small>
                                        @error('delivery_proof_photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_signature" class="form-label">
                                            <i class="bi bi-pencil-square"></i> Signature
                                        </label>
                                        <input type="file" class="form-control @error('delivery_signature') is-invalid @enderror" id="delivery_signature" name="delivery_signature" accept="image/jpeg,image/png,image/jpg">
                                        <small class="text-muted">Max size: 2MB (JPEG, PNG, JPG)</small>
                                        @error('delivery_signature')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="delivery_notes" class="form-label">
                                            <i class="bi bi-chat-left-text"></i> Delivery Notes
                                        </label>
                                        <textarea class="form-control @error('delivery_notes') is-invalid @enderror" id="delivery_notes" name="delivery_notes" rows="3" maxlength="1000" placeholder="Any additional notes about the delivery...">{{ old('delivery_notes', $livraison->delivery_notes) }}</textarea>
                                        <small class="text-muted">Max 1000 characters</small>
                                        @error('delivery_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="bi bi-upload me-1"></i> Upload Proof
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Client Confirmation Form -->
                    @if(auth()->id() === $livraison->idClient && $livraison->statut !== 'delivered' && !$livraison->client_confirmed)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Confirm Delivery</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Did you receive your delivery successfully?</p>
                                
                                <form action="{{ route('livraisons.confirmReceipt', $livraison->idLivraison) }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-3">
                                        <label for="client_confirmation_notes" class="form-label">
                                            <i class="bi bi-chat-left-text"></i> Notes (Optional)
                                        </label>
                                        <textarea class="form-control @error('client_confirmation_notes') is-invalid @enderror" id="client_confirmation_notes" name="client_confirmation_notes" rows="3" maxlength="500" placeholder="Any feedback about the delivery...">{{ old('client_confirmation_notes') }}</textarea>
                                        <small class="text-muted">Max 500 characters</small>
                                        @error('client_confirmation_notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Confirm that you received this delivery?')">
                                        <i class="bi bi-check-circle me-1"></i> Confirm Receipt
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

    </div>
</section><!-- End Delivery Show Section -->

@endsection
