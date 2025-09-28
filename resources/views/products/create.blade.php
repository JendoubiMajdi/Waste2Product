@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Create Product</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Waste</label>
                    <select name="waste_id" class="form-select" required>
                        <option value="">-- Select your waste --</option>
                        @foreach($wastes as $w)
                            <option value="{{ $w->id }}" {{ old('waste_id') == $w->id ? 'selected' : '' }}>#{{ $w->id }} - {{ $w->type }} ({{ $w->localisation }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">État</label>
                    <select name="etat" class="form-select" required>
                        <option value="">-- Sélectionner l'état --</option>
                        <option value="recyclé" {{ old('etat') === 'recyclé' ? 'selected' : '' }}>recyclé</option>
                        <option value="non recyclé" {{ old('etat') === 'non recyclé' ? 'selected' : '' }}>non recyclé</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prix</label>
                    <input type="number" step="0.01" min="100" name="prix" class="form-control" value="{{ old('prix') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantité disponible</label>
                    <input type="number" step="1" min="0" name="quantite" class="form-control" value="{{ old('quantite', 0) }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
