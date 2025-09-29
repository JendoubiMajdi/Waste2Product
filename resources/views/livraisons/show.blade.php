@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Détail Livraison</h2>
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $livraison->idLivraison }}</p>
            <p><strong>Commande:</strong> {{ $livraison->idOrder }}</p>
            <p><strong>Client:</strong> {{ $livraison->idClient }}</p>
            <p><strong>Adresse Livraison:</strong> {{ $livraison->adresseLivraison }}</p>
            <p><strong>Date Livraison:</strong> {{ $livraison->dateLivraison }}</p>
            <p><strong>Statut:</strong> {{ $livraison->statut }}</p>
            <a href="{{ route('livraisons.edit', $livraison) }}" class="btn btn-warning">Modifier</a>
            <a href="{{ route('livraisons.index') }}" class="btn btn-accent">Retour</a>
        </div>
    </div>
</div>
@endsection