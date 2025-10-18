@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Edit Product</h2>
        <p>Update product information</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="edit-product" class="edit-product">
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
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Product #{{ $product->id }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('products.update', $product) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Waste Source <span class="text-danger">*</span></label>
                                <select 
                                    name="waste_id" 
                                    class="form-select @error('waste_id') is-invalid @enderror" 
                                    required
                                >
                                    <option value="">-- Select your waste source --</option>
                                    @foreach($wastes as $w)
                                        <option 
                                            value="{{ $w->id }}" 
                                            {{ old('waste_id', $product->waste_id) == $w->id ? 'selected' : '' }}
                                        >
                                            #{{ $w->id }} - {{ $w->type }} ({{ $w->localisation }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('waste_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Select the waste material this product is made from</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input 
                                        type="text" 
                                        name="nom" 
                                        class="form-control @error('nom') is-invalid @enderror" 
                                        value="{{ old('nom', $product->nom) }}" 
                                        placeholder="e.g., Recycled Plastic Chair"
                                        required
                                    >
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select 
                                        name="etat" 
                                        class="form-select @error('etat') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="">-- Select status --</option>
                                        <option value="recyclé" {{ old('etat', $product->etat) === 'recyclé' ? 'selected' : '' }}>♻️ Recyclé</option>
                                        <option value="non recyclé" {{ old('etat', $product->etat) === 'non recyclé' ? 'selected' : '' }}>Non recyclé</option>
                                    </select>
                                    @error('etat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea 
                                    name="description" 
                                    class="form-control @error('description') is-invalid @enderror" 
                                    rows="3" 
                                    placeholder="Provide a detailed description of the product"
                                    required
                                >{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (TND) <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="100" 
                                        name="prix" 
                                        class="form-control @error('prix') is-invalid @enderror" 
                                        value="{{ old('prix', $product->prix) }}" 
                                        placeholder="Minimum 100 TND"
                                        required
                                    >
                                    @error('prix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum price: 100 TND</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Available Quantity <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        step="1" 
                                        min="0" 
                                        name="quantite" 
                                        class="form-control @error('quantite') is-invalid @enderror" 
                                        value="{{ old('quantite', $product->quantite) }}" 
                                        placeholder="Stock quantity"
                                        required
                                    >
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-1"></i>Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Edit Product Section -->

@endsection

