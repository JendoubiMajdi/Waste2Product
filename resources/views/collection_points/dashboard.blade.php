@extends('layouts.app')

@section('title', 'My Collection Points Dashboard')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Collection Points Dashboard</h2>
        <p>Manage your collection points and track waste deposits</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="dashboard" class="dashboard">
    <div class="container" data-aos="fade-up">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>My Collection Points</h3>
            <a href="{{ route('collection_points.create') }}" class="btn-get-started">
                <i class="bi bi-plus-circle"></i> Create New Collection Point
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Working Hours</th>
                                <th>Status</th>
                                <th>Waste Deposits</th>
                                <th>Total Quantity (kg)</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($points as $point)
                                <tr>
                                    <td>
                                        <strong>{{ $point->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ Str::limit($point->address, 40) }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($point->working_hours)
                                            <i class="bi bi-clock text-success"></i>
                                            <small>{{ $point->formatted_working_hours }}</small>
                                        @else
                                            <small class="text-muted">Not specified</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($point->status === 'active')
                                            <span class="badge bg-success">✅ Active</span>
                                        @else
                                            <span class="badge bg-secondary">❌ Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $point->waste_count }}</strong> deposits
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($point->waste_total, 2) }} kg</strong>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('collection_points.edit', $point->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('collection_points.destroy', $point->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this collection point? All associated wastes will be affected.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">You haven't created any collection points yet.</p>
                                        <a href="{{ route('collection_points.create') }}" class="btn btn-primary mt-2">
                                            <i class="bi bi-plus-circle me-1"></i>Create Your First Collection Point
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($points->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-building-check" style="font-size: 2.5rem; color: #28a745;"></i>
                            <h4 class="mt-2">{{ $points->count() }}</h4>
                            <p class="text-muted mb-0">Total Collection Points</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-trash" style="font-size: 2.5rem; color: #007bff;"></i>
                            <h4 class="mt-2">{{ $points->sum('waste_count') }}</h4>
                            <p class="text-muted mb-0">Total Waste Deposits</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-box-seam" style="font-size: 2.5rem; color: #ffc107;"></i>
                            <h4 class="mt-2">{{ number_format($points->sum('waste_total'), 2) }} kg</h4>
                            <p class="text-muted mb-0">Total Waste Quantity</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</section><!-- End Dashboard Section -->

@endsection
