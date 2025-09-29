{{-- filepath: resources/views/events/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Event Card -->
            <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                <!-- Event Image -->
                @if($event->picture)
                <div class="event-image-container">
                    <img src="{{ asset('storage/' . $event->picture) }}" alt="Event Picture" class="img-fluid w-100 event-image">
                </div>
                @else
                <div class="event-image-container">
                    <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                        <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                    </div>
                </div>
                @endif

                <!-- Event Content -->
                <div class="card-body p-4">
                    <!-- Event Header with Action Buttons -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1 class="card-title h2 text-primary mb-0">{{ $event->title }}</h1>
                        <div class="d-flex align-items-center">
                            <div class="badge bg-primary fs-6 me-3">{{ $participants->count() }} Participants</div>

                            <!-- Edit/Delete Buttons for Event Creator -->
                            @auth
                                @if($event->user_id && $event->user_id === auth()->id())
                                <div class="btn-group">
                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <!-- Event Creator Info -->
                    <div class="mb-4">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>
                            Created by: {{ $event->user ? $event->user->name : 'Unknown User' }}
                        </small>
                    </div>

                    <!-- Event Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-alt text-muted me-2"></i>
                                <strong class="text-muted">Date:</strong>
                                <span class="ms-2">{{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-muted me-2"></i>
                                <strong class="text-muted">Time:</strong>
                                <span class="ms-2">{{ $event->time }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Description</h5>
                        <p class="card-text fs-6 text-muted lh-base">{{ $event->description }}</p>
                    </div>

                    <!-- Google Meet Link -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">Join Meeting</h5>
                        <a href="{{ $event->meet_link }}" target="_blank" class="btn btn-danger btn-lg d-inline-flex align-items-center">
                            <i class="fas fa-video me-2"></i>
                            Join Google Meet
                        </a>
                        <small class="d-block text-muted mt-2">{{ $event->meet_link }}</small>
                    </div>

                    <!-- Participants Section -->
                    <div class="participants-section">
                        <h5 class="text-primary mb-3">Participants ({{ $participants->count() }})</h5>
                        <div class="participants-list">
                            @forelse($participants as $user)
                            <div class="participant-item d-flex align-items-center mb-2 p-2 bg-light rounded">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <strong class="d-block">{{ $user->name }}</strong>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No participants yet. Be the first to join!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Join Event Button -->
                    @auth
                    <div class="join-section mt-4 pt-4 border-top">
                        @if(!$participants->contains(auth()->user()))
                        <form action="{{ route('events.join', $event->id) }}" method="POST" class="text-center">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg px-5 py-2">
                                <i class="fas fa-user-plus me-2"></i>
                                Join This Event
                            </button>
                        </form>
                        @else
                        <div class="alert alert-success text-center mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            You have successfully joined this event!
                        </div>
                        @endif
                    </div>
                    @endauth
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Events
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Event Modal -->
@auth
    @if($event->user_id && $event->user_id === auth()->id())
    <div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEventModalLabel">Delete Event</h5>
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

<style>
.event-image-container {
    height: 400px;
    overflow: hidden;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.event-image {
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.event-image:hover {
    transform: scale(1.05);
}

.participants-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
}

.participant-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.participant-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.avatar {
    font-weight: bold;
    font-size: 1.1rem;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.join-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
}
</style>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Add Bootstrap JS for modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection
