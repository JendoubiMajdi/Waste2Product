@extends('layouts.app')

@section('title', 'Wastes Management')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Waste Management</h2>
        <p>Manage and track waste deposits at collection points</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="wastes" class="wastes">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>All Wastes</h3>
            <a href="{{ route('wastes.create') }}" class="btn-get-started">
                <i class="bi bi-plus-circle"></i> Create New Waste
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Search and Filters -->
        @include('partials.waste-search')

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Quantity (kg)</th>
                                <th>Deposit Date</th>
                                <th>Location</th>
                                <th>Collection Point</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wastes as $waste)
                                <tr>
                                    <td>#{{ $waste->id }}</td>
                                    <td><span class="badge bg-primary">{{ $waste->type }}</span></td>
                                    <td>{{ $waste->quantite }} kg</td>
                                    <td>{{ \Carbon\Carbon::parse($waste->dateDepot)->format('M d, Y') }}</td>
                                    <td>{{ $waste->localisation }}</td>
                                    <td>
                                        @if($waste->collectionPoint)
                                            <small class="text-muted">
                                                #{{ $waste->collection_point_id }} - {{ $waste->collectionPoint->name }}
                                            </small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('wastes.show', $waste) }}" class="btn btn-sm btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @auth
                                            @if(Auth::user()->isAdmin() || $waste->user_id === Auth::id())
                                                <a href="{{ route('wastes.edit', $waste) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('wastes.destroy', $waste) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this waste?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">No wastes found. Create your first waste deposit!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section><!-- End Wastes Section -->

@endsection
