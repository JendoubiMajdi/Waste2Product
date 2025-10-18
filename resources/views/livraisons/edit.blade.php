@extends('layouts.app')

@section('title', 'Edit Delivery')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Edit Delivery</h2>
        <p>Update delivery information</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="edit-delivery" class="edit-delivery">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Delivery #{{ $livraison->idLivraison }}</h5>
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

                        <form action="{{ route('livraisons.update', $livraison->idLivraison) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="idOrder" value="{{ $livraison->idOrder }}">
                            <input type="hidden" name="idClient" value="{{ $livraison->idClient }}">

                            <div class="mb-3">
                                <label for="adresseLivraison" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control @error('adresseLivraison') is-invalid @enderror" 
                                    id="adresseLivraison" 
                                    name="adresseLivraison" 
                                    rows="3" 
                                    required>{{ old('adresseLivraison', $livraison->adresseLivraison) }}</textarea>
                                @error('adresseLivraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="dateLivraison" class="form-label">Scheduled Delivery Date <span class="text-danger">*</span></label>
                                <input 
                                    type="date" 
                                    class="form-control @error('dateLivraison') is-invalid @enderror" 
                                    id="dateLivraison" 
                                    name="dateLivraison" 
                                    value="{{ old('dateLivraison', $livraison->dateLivraison) }}"
                                    required>
                                @error('dateLivraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="statut" class="form-label">Delivery Status <span class="text-danger">*</span></label>
                                <select 
                                    class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut" 
                                    required>
                                    <option value="in_delivery" {{ old('statut', $livraison->statut) === 'in_delivery' ? 'selected' : '' }}>In Delivery</option>
                                    <option value="delivered" {{ old('statut', $livraison->statut) === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Mark as "Delivered" when the order has been successfully delivered</small>
                            </div>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Changing the status to "Delivered" will also update the order status to completed.
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('livraisons.show', $livraison->idLivraison) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Update Delivery
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Edit Delivery Section -->

@endsection
