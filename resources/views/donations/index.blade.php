@extends('layouts.app')

@section('title', 'Donations')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Donations</h2>
        <p>Support our sustainability mission - View and make donations</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Recent Donations</h3>
                    <div>
                        @auth
                        <a href="{{ route('donations.my-donations') }}" class="btn btn-outline-primary me-2">
                            <i class="bi bi-person-heart"></i> My Donations
                        </a>
                        <a href="{{ route('donations.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Make Donation
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Login to Donate
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            @forelse($donations as $donation)
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    @if($donation->image)
                    <img src="data:image/jpeg;base64,{{ $donation->image }}" class="card-img-top" alt="{{ $donation->type }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="bi bi-gift" style="font-size: 4rem; color: #ccc;"></i>
                    </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ ucfirst($donation->type) }}</span>
                            @if($donation->amount)
                            <span class="text-success fw-bold">${{ number_format($donation->amount, 2) }}</span>
                            @endif
                        </div>
                        
                        <p class="card-text">{{ Str::limit($donation->description, 100) }}</p>
                        
                        <div class="d-flex align-items-center text-muted small">
                            <i class="bi bi-person-circle me-1"></i>
                            <span>{{ $donation->user->name }}</span>
                            <span class="mx-2">•</span>
                            <i class="bi bi-clock me-1"></i>
                            <span>{{ $donation->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('donations.show', $donation) }}" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    No donations found. Be the first to make a donation!
                </div>
            </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $donations->links() }}
        </div>
    </div>
</section>
@endsection
