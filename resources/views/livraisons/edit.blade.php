@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Modifier Livraison</h2>

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
    <form action="{{ route('livraisons.update', $livraison) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="idOrder" class="form-label">Commande</label>
            <input type="number" name="idOrder" id="idOrder" class="form-control" value="{{ $livraison->idOrder }}" required>
        </div>
        <div class="mb-3">
            <label for="idClient" class="form-label">Client</label>
            <input type="number" name="idClient" id="idClient" class="form-control" value="{{ $livraison->idClient }}" required>
        </div>
        <div class="mb-3">
            <label for="adresseLivraison" class="form-label">Adresse Livraison</label>
            <input type="text" name="adresseLivraison" id="adresseLivraison" class="form-control" value="{{ $livraison->adresseLivraison }}" required>
        </div>
        <div class="mb-3">
            <label for="dateLivraison" class="form-label">Date Livraison</label>
            <input type="date" name="dateLivraison" id="dateLivraison" class="form-control" value="{{ $livraison->dateLivraison }}" required>
        </div>
        <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select name="statut" id="statut" class="form-control" required>
                <option value="en attente" @if($livraison->statut=='en attente') selected @endif>En attente</option>
                <option value="en cours" @if($livraison->statut=='en cours') selected @endif>En cours</option>
                <option value="livrée" @if($livraison->statut=='livrée') selected @endif>Livrée</option>
            </select>
        </div>
    <button type="submit" class="btn btn-success">Mettre à jour</button>
    <a href="{{ route('livraisons.index') }}" class="btn btn-accent">Annuler</a>
    </form>
    </div>
    </div>
</div>
@endsection