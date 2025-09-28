@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Nouvelle Livraison</h2>

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
    <form action="{{ route('livraisons.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="idOrder" class="form-label">Commande</label>
            <input type="number" name="idOrder" id="idOrder" class="form-control" required value="{{ request('idOrder') }}" readonly>
        </div>
        <div class="mb-3">
            <label for="idClient" class="form-label">Client</label>
            <input type="text" class="form-control" value="{{ \App\Models\User::find(request('idClient'))->name ?? '' }}" readonly>
            <input type="hidden" name="idClient" id="idClient" value="{{ request('idClient') }}">
        </div>
        <div class="mb-3">
            <label for="adresseLivraison" class="form-label">Adresse Livraison</label>
            <input type="text" name="adresseLivraison" id="adresseLivraison" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="dateLivraison" class="form-label">Date Livraison</label>
            <input type="date" name="dateLivraison" id="dateLivraison" class="form-control" required min="{{ date('Y-m-d') }}">
        </div>
        <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select name="statut" id="statut" class="form-control" required>
                <option value="en attente">En attente</option>
                <option value="en cours">En cours</option>
                <option value="livrée">Livrée</option>
            </select>
        </div>
    <button type="submit" class="btn btn-success">Créer</button>
    <a href="{{ route('livraisons.index') }}" class="btn btn-accent">Annuler</a>
    </form>
    </div>
    </div>
</div>
@endsection