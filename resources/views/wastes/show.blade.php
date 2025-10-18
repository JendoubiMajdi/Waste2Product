@extends('layouts.app')

@section('title', 'Waste Details')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Waste Deposit Details</h2>
        <p>View detailed information about this waste deposit</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="show-waste" class="show-waste">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Waste #{{ $waste->id }}</h5>
                        <div>
                            <a href="{{ route('wastes.edit', $waste) }}" class="btn btn-sm btn-light me-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('wastes.index') }}" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Waste Type</h6>
                                <p class="fs-5"><span class="badge bg-primary">{{ $waste->type }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Quantity</h6>
                                <p class="fs-5"><strong>{{ $waste->quantite }} kg</strong></p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Deposit Date</h6>
                                <p class="fs-6">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>
                                    {{ \Carbon\Carbon::parse($waste->dateDepot)->format('F d, Y') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Created At</h6>
                                <p class="fs-6">
                                    <i class="bi bi-clock text-muted me-2"></i>
                                    {{ $waste->created_at ? $waste->created_at->format('M d, Y H:i') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Location Details</h6>
                            <p class="fs-6">
                                <i class="bi bi-geo-alt text-danger me-2"></i>
                                {{ $waste->localisation }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Collection Point</h6>
                            @if($waste->collectionPoint)
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $waste->collectionPoint->name }}</h6>
                                        <p class="card-text mb-1">
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <small>{{ $waste->collectionPoint->address }}</small>
                                        </p>
                                        @if($waste->collectionPoint->contact_phone)
                                            <p class="card-text mb-1">
                                                <i class="bi bi-telephone me-1"></i>
                                                <small>{{ $waste->collectionPoint->contact_phone }}</small>
                                            </p>
                                        @endif
                                        @if($waste->collectionPoint->working_hours)
                                            <p class="card-text mb-0">
                                                <i class="bi bi-clock me-1"></i>
                                                <small>{{ $waste->collectionPoint->working_hours }}</small>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="text-muted fst-italic">No collection point assigned</p>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <form action="{{ route('wastes.destroy', $waste) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this waste deposit?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </form>
                            <a href="{{ route('wastes.edit', $waste) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Show Waste Section -->

@endsection
