@extends('layouts.app')

@section('title', 'Edit Waste #'.$waste->id)

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Edit Waste #{{ $waste->id }}</h2>

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
            <form action="{{ route('wastes.update', $waste) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type', $waste->type) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" step="0.01" min="10" name="quantite" class="form-control" value="{{ old('quantite', $waste->quantite) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date dépôt</label>
                    <input type="date" name="dateDepot" class="form-control" value="{{ old('dateDepot', \Illuminate\Support\Carbon::parse($waste->dateDepot)->format('Y-m-d')) }}" min="{{ \Illuminate\Support\Carbon::tomorrow()->format('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Localisation</label>
                    <input type="text" name="localisation" class="form-control" value="{{ old('localisation', $waste->localisation) }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('wastes.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
