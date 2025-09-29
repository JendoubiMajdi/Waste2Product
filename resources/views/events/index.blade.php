{{-- filepath: resources/views/events/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="display-5 text-primary fw-bold">Upcoming Events</h1>
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('events.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Create New Event
                    </a>
                    @else
                    <button class="btn btn-secondary btn-lg" disabled title="Please verify your email to create events">
                        <i class="fas fa-plus me-2"></i>Create New Event
                    </button>
                    @endif
                @endauth
            </div>
            <p class="lead text-muted">Discover and join amazing events in our community</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('events.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-primary text-white border-0">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       class="form-control border-0"
                                       placeholder="Search events by title..."
                                       value="{{ request('search') }}"
                                       style="background: #f8f9fa;">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-lg w-100">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events as $event)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card event-card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                <!-- Event Image -->
                @if($event->picture)
                <div class="event-card-image">
                    <img src="{{ asset('storage/' . $event->picture) }}" alt="{{ $event->title }}" class="img-fluid">
                </div>
                @else
                <div class="event-card-image bg-light d-flex align-items-center justify-content-center">
                    <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                </div>
                @endif

                <!-- Event Content -->
                <div class="card-body p-4">
                    <!-- Event Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title fw-bold text-dark mb-0">{{ Str::limit($event->title, 50) }}</h5>
                        <div class="badge bg-primary">{{ $event->participants->count() }} Participants</div>
                    </div>

                    <!-- Event Meta -->
                    <div class="event-meta mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt text-muted me-2 small"></i>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('M j, Y') }}</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-clock text-muted me-2 small"></i>
                            <small class="text-muted">{{ $event->time }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-muted me-2 small"></i>
                            <small class="text-muted">By {{ $event->user ? $event->user->name : 'Unknown' }}</small>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <p class="card-text text-muted small mb-4">
                        {{ Str::limit($event->description, 100) }}
                    </p>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>

                        @auth
                            @if($event->user_id === auth()->id() && auth()->user()->hasVerifiedEmail())
                            <div class="btn-group w-100">
                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $event->id }}">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                            @elseif($event->user_id === auth()->id() && !auth()->user()->hasVerifiedEmail())
                            <div class="btn-group w-100">
                                <button class="btn btn-outline-warning btn-sm" disabled title="Verify email to edit">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger btn-sm" disabled title="Verify email to delete">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal for each event -->
        @auth
            @if($event->user_id === auth()->id() && auth()->user()->hasVerifiedEmail())
            <div class="modal fade" id="deleteModal{{ $event->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $event->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $event->id }}">Delete Event</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the event "{{ $event->title }}"? This action cannot be undone.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Event</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endauth
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No events found</h3>
                <p class="text-muted">
                    @if(request('search'))
                        No events match your search "{{ request('search') }}".
                    @else
                        There are no events available at the moment.
                    @endif
                </p>
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                    <a href="{{ route('events.create') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-plus me-2"></i>Create the First Event
                    </a>
                    @else
                    <button class="btn btn-secondary btn-lg mt-3" disabled>
                        <i class="fas fa-plus me-2"></i>Verify Email to Create Events
                    </button>
                    <div class="mt-3">
                        <a href="{{ route('verification.notice') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i>Resend Verification Email
                        </a>
                    </div>
                    @endif
                @endauth
            </div>
        </div>
        @endforelse
    </div>

    <!-- My Events Section for logged-in users -->
    @auth
    @php
        $myEvents = $events->where('user_id', auth()->id());
    @endphp
    @if($myEvents->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary mb-0">My Events</h2>
                @if(!auth()->user()->hasVerifiedEmail())
                <small class="text-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>Verify email to manage events
                </small>
                @endif
            </div>
            <div class="row">
                @foreach($myEvents as $event)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card border-primary h-100">
                        <div class="card-body">
                            <h6 class="card-title">{{ Str::limit($event->title, 30) }}</h6>
                            <small class="text-muted d-block mb-2">
                                {{ \Carbon\Carbon::parse($event->date)->format('M j') }} at {{ $event->time }}
                            </small>
                            <small class="text-primary">
                                <i class="fas fa-users me-1"></i>{{ $event->participants->count() }} participants
                            </small>
                        </div>
                        <div class="card-footer bg-transparent">
                            @if(auth()->user()->hasVerifiedEmail())
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary btn-sm w-100">
                                Manage
                            </a>
                            @else
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                Verify to Manage
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @endauth

    <!-- Verification Notice for unverified users -->
    @auth
        @if(!auth()->user()->hasVerifiedEmail())
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-2">Email Verification Required</h5>
                        <p class="mb-2">Please verify your email address to create and manage events.</p>
                        <a href="{{ route('verification.notice') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-1"></i>Resend Verification Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endauth
</div>

<style>
.event-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.event-card-image {
    height: 200px;
    overflow: hidden;
    position: relative;
}

.event-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.event-card:hover .event-card-image img {
    transform: scale(1.05);
}

.event-meta {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 0 8px 8px 0;
}

.card-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
}

.btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
</style>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Add Bootstrap JS for modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
