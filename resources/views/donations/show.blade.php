@extends('layouts.app')

@section('title', 'Donation Details')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Donation Details</h2>
        <p><a href="{{ route('donations.index') }}">Back to Donations</a></p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow">
                    @if($donation->image)
                    <img src="data:image/jpeg;base64,{{ $donation->image }}" class="card-img-top" alt="{{ $donation->type }}" style="max-height: 400px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary fs-6">{{ ucfirst($donation->type) }}</span>
                            @if($donation->amount)
                            <span class="text-success fw-bold fs-4">${{ number_format($donation->amount, 2) }}</span>
                            @endif
                        </div>

                        <h3 class="card-title">{{ ucfirst($donation->type) }} Donation</h3>
                        
                        <div class="mb-4">
                            <p class="card-text" style="white-space: pre-wrap;">{{ $donation->description }}</p>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Donor:</strong> {{ $donation->user->name }}</p>
                                <p class="mb-2"><strong>Date:</strong> {{ $donation->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="badge bg-success">{{ ucfirst($donation->status) }}</span>
                                </p>
                                <p class="mb-2"><strong>Time:</strong> {{ $donation->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">About Donations</h5>
                        <p class="card-text">Your donations help us transform waste into valuable resources and support our sustainability mission.</p>
                        
                        <a href="{{ route('donations.create') }}" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-plus-circle"></i> Make a Donation
                        </a>
                        <a href="{{ route('donations.index') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-left"></i> View All Donations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
