@extends('layouts.app')

@section('title', 'Community Events')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>Community Events</h2>
                <p>Join environmental events and make a difference</p>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('events.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Create Event
            </a>
            @endif
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Filter Tabs -->
        <ul class="nav nav-pills mb-4 justify-content-center" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="pill" 
                        data-bs-target="#upcoming" type="button" role="tab">
                    <i class="bi bi-calendar-event"></i> Upcoming Events
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="pill" 
                        data-bs-target="#past" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> Past Events
                </button>
            </li>
        </ul>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="tab-content">
            <!-- Upcoming Events -->
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                <div class="row">
                    @forelse($upcomingEvents as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            @if($event->image)
                            <img src="data:image/jpeg;base64,{{ $event->image }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="{{ $event->title }}">
                            @else
                            <div class="card-img-top bg-success d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                            </div>
                            @endif
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-success">Upcoming</span>
                                    @if(Auth::check() && $event->isUserRegistered(Auth::id()))
                                    <span class="badge bg-info">Registered</span>
                                    @endif
                                </div>
                                
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="mb-3">
                                    <small class="d-block mb-1">
                                        <i class="bi bi-calendar3 text-primary"></i>
                                        <strong>{{ $event->event_date->format('M d, Y') }}</strong>
                                        @if($event->event_time)
                                        at {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                                        @endif
                                    </small>
                                    <small class="d-block mb-1">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        {{ $event->location }}
                                    </small>
                                    <small class="d-block">
                                        <i class="bi bi-people text-success"></i>
                                        {{ $event->participants_count }}/{{ $event->max_participants }} Participants
                                    </small>
                                </div>

                                <div class="progress mb-3" style="height: 5px;">
                                    @php
                                        $percentage = ($event->participants_count / $event->max_participants) * 100;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-light">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            No upcoming events at the moment. Check back soon!
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Past Events -->
            <div class="tab-pane fade" id="past" role="tabpanel">
                <div class="row">
                    @forelse($pastEvents as $event)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm opacity-75">
                            @if($event->image)
                            <img src="data:image/jpeg;base64,{{ $event->image }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="{{ $event->title }}">
                            @else
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="bi bi-calendar-check text-white" style="font-size: 4rem;"></i>
                            </div>
                            @endif
                            
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-secondary">Completed</span>
                                    @if(Auth::check() && $event->isUserRegistered(Auth::id()))
                                    <span class="badge bg-success">You Attended</span>
                                    @endif
                                </div>
                                
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="mb-3">
                                    <small class="d-block mb-1">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $event->event_date->format('M d, Y') }}
                                    </small>
                                    <small class="d-block">
                                        <i class="bi bi-people"></i>
                                        {{ $event->participants_count }} Participants
                                    </small>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-light">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            No past events to display.
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
