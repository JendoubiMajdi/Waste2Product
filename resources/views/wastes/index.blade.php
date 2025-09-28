@extends('layouts.app')

@section('title', 'Wastes')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Wastes</h2>
        <a href="{{ route('wastes.create') }}" class="btn btn-primary">Create Waste</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Date dépôt</th>
                            <th>Localisation</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wastes as $waste)
                            <tr>
                                <td>{{ $waste->id }}</td>
                                <td>{{ $waste->type }}</td>
                                <td>{{ $waste->quantite }}</td>
                                <td>{{ \Illuminate\Support\Carbon::parse($waste->dateDepot)->format('Y-m-d') }}</td>
                                <td>{{ $waste->localisation }}</td>
                                <td class="text-end">
                                    <a href="{{ route('wastes.show', $waste) }}" class="btn btn-sm btn-outline-secondary">Show</a>
                                    <a href="{{ route('wastes.edit', $waste) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('wastes.destroy', $waste) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this waste?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">No wastes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
