@extends('layouts.app')

@section('title', 'My Events')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>My Events</h2>
        <p>Events you've registered for</p>
    </div>
</div>

<section class="section">
    <div class="container">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $upcomingEvents->count() }}</h3>
                        <p class="mb-0">Upcoming Events</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $pastEvents->count() }}</h3>
                        <p class="mb-0">Events Attended</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $upcomingEvents->count() + $pastEvents->count() }}</h3>
                        <p class="mb-0">Total Events</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upcoming-tab" data-bs-toggle="pill" 
                        data-bs-target="#upcoming" type="button" role="tab">
                    <i class="bi bi-calendar-event"></i> Upcoming ({{ $upcomingEvents->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="pill" 
                        data-bs-target="#past" type="button" role="tab">
                    <i class="bi bi-calendar-check"></i> Past ({{ $pastEvents->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Upcoming Events -->
            <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                @forelse($upcomingEvents as $event)
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                @if($event->image)
                                <img src="data:image/jpeg;base64,{{ $event->image }}" 
                                     class="img-fluid rounded" 
                                     style="height: 100px; width: 100px; object-fit: cover;" 
                                     alt="{{ $event->title }}">
                                @else
                                <div class="bg-success rounded d-flex align-items-center justify-content-center" 
                                     style="height: 100px; width: 100px;">
                                    <i class="bi bi-calendar-event text-white" style="font-size: 2.5rem;"></i>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-2">{{ $event->title }}</h5>
                                <p class="text-muted mb-2">{{ Str::limit($event->description, 120) }}</p>
                                
                                <div class="d-flex flex-wrap gap-3">
                                    <small>
                                        <i class="bi bi-calendar3 text-primary"></i>
                                        <strong>{{ $event->event_date->format('M d, Y') }}</strong>
                                    </small>
                                    @if($event->event_time)
                                    <small>
                                        <i class="bi bi-clock text-warning"></i>
                                        {{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                                    </small>
                                    @endif
                                    <small>
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        {{ Str::limit($event->location, 30) }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <!-- Days Until Event -->
                                @php
                                    $daysUntil = now()->diffInDays($event->event_date, false);
                                @endphp
                                @if($daysUntil >= 0)
                                <div class="mb-3">
                                    <span class="badge bg-warning text-dark fs-6">
                                        @if($daysUntil == 0)
                                            Today!
                                        @elseif($daysUntil == 1)
                                            Tomorrow
                                        @else
                                            In {{ $daysUntil }} days
                                        @endif
                                    </span>
                                </div>
                                @endif

                                <div class="d-flex gap-2 justify-content-md-end">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('events.unregister', $event) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to cancel your registration?');" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Upcoming Events</h5>
                        <p class="text-muted">You haven't registered for any upcoming events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-success">
                            <i class="bi bi-search"></i> Browse Events
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Past Events -->
            <div class="tab-pane fade" id="past" role="tabpanel">
                @forelse($pastEvents as $event)
                <div class="card shadow-sm mb-3 opacity-75">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center mb-3 mb-md-0">
                                @if($event->image)
                                <img src="data:image/jpeg;base64,{{ $event->image }}" 
                                     class="img-fluid rounded" 
                                     style="height: 100px; width: 100px; object-fit: cover;" 
                                     alt="{{ $event->title }}">
                                @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                     style="height: 100px; width: 100px;">
                                    <i class="bi bi-calendar-check text-white" style="font-size: 2.5rem;"></i>
                                </div>
                                @endif
                            </div>
                            
                            <div class="col-md-7">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-2">{{ $event->title }}</h5>
                                    <span class="badge bg-success">Attended</span>
                                </div>
                                <p class="text-muted mb-2">{{ Str::limit($event->description, 120) }}</p>
                                
                                <div class="d-flex flex-wrap gap-3">
                                    <small>
                                        <i class="bi bi-calendar3"></i>
                                        {{ $event->event_date->format('M d, Y') }}
                                    </small>
                                    <small>
                                        <i class="bi bi-geo-alt"></i>
                                        {{ Str::limit($event->location, 30) }}
                                    </small>
                                    <small>
                                        <i class="bi bi-people"></i>
                                        {{ $event->participants_count }} participants
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                <small class="text-muted d-block mb-2">
                                    {{ $event->event_date->diffForHumans() }}
                                </small>
                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-check text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No Past Events</h5>
                        <p class="text-muted">You haven't attended any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-success">
                            <i class="bi bi-calendar-plus"></i> Join an Event
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
