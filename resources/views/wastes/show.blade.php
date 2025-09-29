@extends('layouts.app')

@section('title', 'Waste #'.$waste->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Waste #{{ $waste->id }}</h2>
        <div>
            <a href="{{ route('wastes.edit', $waste) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('wastes.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Type</dt>
                <dd class="col-sm-9">{{ $waste->type }}</dd>

                <dt class="col-sm-3">Quantité</dt>
                <dd class="col-sm-9">{{ $waste->quantite }}</dd>

                <dt class="col-sm-3">Date dépôt</dt>
                <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($waste->dateDepot)->format('Y-m-d') }}</dd>

                <dt class="col-sm-3">Localisation</dt>
                <dd class="col-sm-9">{{ $waste->localisation }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $waste->created_at }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $waste->updated_at }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
