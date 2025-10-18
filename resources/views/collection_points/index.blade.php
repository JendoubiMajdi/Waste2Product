@extends('layouts.app')

@section('title', 'Collection Points')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Collection Points</h2>
        <p>Find active waste collection points near you</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="collection-points" class="collection-points">
    <div class="container" data-aos="fade-up">

        @auth
            @if(Auth::user()->role === 'collector' || Auth::user()->role === 'admin')
                <div class="mb-4">
                    <a href="{{ route('collection_points.dashboard') }}" class="btn btn-primary me-2">
                        <i class="bi bi-speedometer2 me-1"></i>Manage My Collection Points
                    </a>
                    <a href="{{ route('collection_points.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Create New Collection Point
                    </a>
                </div>
            @endif
        @endauth

        <h3 class="mb-4">Available Collection Points</h3>

        <div class="row gy-4">
            @forelse($collectionPoints as $point)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        @php
                            $isBase64 = false;
                            if ($point->image) {
                                $isBase64 = Str::startsWith($point->image, '/9j/') || Str::startsWith($point->image, 'iVBOR');
                            }
                        @endphp
                        
                        @if($point->image && $isBase64)
                            <img src="data:image/jpeg;base64,{{ $point->image }}" class="card-img-top" alt="{{ $point->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-building" style="font-size: 4rem; color: #ccc;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $point->name }}
                                @if($point->status === 'active')
                                    <span class="badge bg-success ms-1">Active</span>
                                @else
                                    <span class="badge bg-secondary ms-1">Inactive</span>
                                @endif
                            </h5>
                            
                            <p class="card-text">
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                <small>{{ $point->address }}</small>
                            </p>
                            
                            @if($point->contact_phone)
                                <p class="card-text">
                                    <i class="bi bi-telephone text-primary me-1"></i>
                                    <small>{{ $point->contact_phone }}</small>
                                </p>
                            @endif
                            
                            @if($point->working_hours)
                                <p class="card-text">
                                    <i class="bi bi-clock text-success me-1"></i>
                                    <small><strong>Hours:</strong> {{ $point->formatted_working_hours }}</small>
                                </p>
                            @endif
                            
                            @if($point->latitude && $point->longitude)
                                <p class="card-text">
                                    <i class="bi bi-pin-map text-info me-1"></i>
                                    <small>{{ $point->latitude }}, {{ $point->longitude }}</small>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No collection points available at the moment.</p>
                </div>
            @endforelse
        </div>

        @if($collectionPoints->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $collectionPoints->links() }}
            </div>
        @endif

    </div>
</section><!-- End Collection Points Section -->

@endsection
