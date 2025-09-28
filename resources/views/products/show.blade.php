@extends('layouts.app')

@section('title', 'Product #'.$product->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Product #{{ $product->id }}</h2>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Waste</dt>
                <dd class="col-sm-9">#{{ $product->waste_id }} - {{ $product->waste->type ?? '' }}</dd>

                <dt class="col-sm-3">Nom</dt>
                <dd class="col-sm-9">{{ $product->nom }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $product->description }}</dd>

                <dt class="col-sm-3">État</dt>
                <dd class="col-sm-9">{{ $product->etat }}</dd>

                <dt class="col-sm-3">Prix</dt>
                <dd class="col-sm-9">{{ $product->prix }}</dd>

                <dt class="col-sm-3">Quantité</dt>
                <dd class="col-sm-9">{{ $product->quantite }}</dd>

                <dt class="col-sm-3">Created At</dt>
                <dd class="col-sm-9">{{ $product->created_at }}</dd>

                <dt class="col-sm-3">Updated At</dt>
                <dd class="col-sm-9">{{ $product->updated_at }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
