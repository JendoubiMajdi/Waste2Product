@extends('layouts.app')

@section('title', 'Create Waste')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Create Waste</h2>

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
            <form action="{{ route('wastes.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" step="0.01" min="10" name="quantite" class="form-control" value="{{ old('quantite') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date dépôt</label>
                    <input type="date" name="dateDepot" class="form-control" value="{{ old('dateDepot') }}" min="{{ \Illuminate\Support\Carbon::tomorrow()->format('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Localisation</label>
                    <input type="text" name="localisation" class="form-control" value="{{ old('localisation') }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('wastes.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
