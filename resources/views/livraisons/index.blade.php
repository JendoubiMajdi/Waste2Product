@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Livraisons</h2>
    <a href="{{ route('livraisons.create') }}" class="btn btn-primary mb-3 shadow">Nouvelle Livraison</a>

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
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order</th>
                <th>Client</th>
                <th>Adresse</th>
                <th>Date Livraison</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($livraisons as $livraison)
            <tr>
                <td>{{ $livraison->idLivraison }}</td>
                <td>{{ $livraison->idOrder }}</td>
                <td>{{ $livraison->idClient }}</td>
                <td>{{ $livraison->adresseLivraison }}</td>
                <td>{{ $livraison->dateLivraison }}</td>
                <td>{{ $livraison->statut }}</td>
                <td>
                    <a href="{{ route('livraisons.show', $livraison) }}" class="btn btn-info btn-sm">Voir</a>
                    <a href="{{ route('livraisons.edit', $livraison) }}" class="btn btn-warning btn-sm">Modifier</a>
                    <form action="{{ route('livraisons.destroy', $livraison) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette livraison ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection