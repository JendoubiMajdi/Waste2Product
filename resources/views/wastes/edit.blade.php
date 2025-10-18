@extends('layouts.app')

@section('title', 'Edit Waste Deposit')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Edit Waste Deposit</h2>
        <p>Update waste deposit information</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="edit-waste" class="edit-waste">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Waste #{{ $waste->id }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('wastes.update', $waste) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waste Type <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        name="type" 
                                        class="form-control @error('type') is-invalid @enderror" 
                                        value="{{ old('type', $waste->type) }}" 
                                        placeholder="e.g., Plastic, Metal, Paper"
                                        required
                                    >
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        name="quantite" 
                                        min="10" 
                                        step="0.01"
                                        class="form-control @error('quantite') is-invalid @enderror" 
                                        value="{{ old('quantite', $waste->quantite) }}" 
                                        placeholder="Minimum 10 kg"
                                        required
                                    >
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum quantity: 10 kg</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Deposit Date <span class="text-danger">*</span></label>
                                    <input 
                                        type="date" 
                                        name="dateDepot" 
                                        min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                        class="form-control @error('dateDepot') is-invalid @enderror" 
                                        value="{{ old('dateDepot', $waste->dateDepot) }}" 
                                        required
                                    >
                                    @error('dateDepot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Deposit must be scheduled for tomorrow or later</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Collection Point <span class="text-danger">*</span></label>
                                    <select 
                                        name="collection_point_id" 
                                        class="form-select @error('collection_point_id') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="">-- Select a collection point --</option>
                                        @foreach($collectionPoints as $point)
                                            <option 
                                                value="{{ $point->id }}" 
                                                {{ old('collection_point_id', $waste->collection_point_id) == $point->id ? 'selected' : '' }}
                                            >
                                                #{{ $point->id }} - {{ $point->name }} ({{ $point->address }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('collection_point_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location Details <span class="text-danger">*</span></label>
                                <textarea 
                                    name="localisation" 
                                    class="form-control @error('localisation') is-invalid @enderror" 
                                    rows="3" 
                                    placeholder="Enter specific location details within the collection point"
                                    required
                                >{{ old('localisation', $waste->localisation) }}</textarea>
                                @error('localisation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('wastes.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-1"></i>Update Waste Deposit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Edit Waste Section -->

@endsection
