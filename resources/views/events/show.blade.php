@extends('layouts.app')

@section('title', $event->title)

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>{{ $event->title }}</h2>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
            </ol>
        </nav>
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

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row">
            <!-- Event Details -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    @if($event->image)
                    <img src="data:image/jpeg;base64,{{ $event->image }}" 
                         class="card-img-top" 
                         style="height: 400px; object-fit: cover;" 
                         alt="{{ $event->title }}">
                    @endif
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            @if($event->event_date->isFuture())
                            <span class="badge bg-success fs-6">Upcoming Event</span>
                            @else
                            <span class="badge bg-secondary fs-6">Past Event</span>
                            @endif

                            @if(Auth::check() && Auth::user()->isAdmin())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="bi bi-pencil"></i> Edit Event
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('events.destroy', $event) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this event?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash"></i> Delete Event
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>

                        <h3 class="mb-3">{{ $event->title }}</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar3 text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Date & Time</small>
                                        <strong>{{ $event->event_date->format('l, F j, Y') }}</strong><br>
                                        <strong>{{ $event->event_date_time->format('h:i A') }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt text-danger fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Location</small>
                                        <strong>{{ $event->location }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people text-success fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Participants</small>
                                        <strong>{{ $event->participants_count }}/{{ $event->max_participants }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle text-info fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Organized by</small>
                                        <strong>{{ $event->user->name }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">About This Event</h5>
                        <p class="text-muted" style="white-space: pre-line;">{{ $event->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Registration Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        @auth
                            @if($event->event_date->isPast())
                                <div class="alert alert-secondary">
                                    <i class="bi bi-calendar-check fs-1"></i>
                                    <p class="mb-0 mt-2">This event has ended</p>
                                </div>
                            @elseif($event->isUserRegistered(Auth::id()))
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle fs-1"></i>
                                    <p class="mb-0 mt-2"><strong>You're registered!</strong></p>
                                </div>
                                <form action="{{ route('events.unregister', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-x-circle"></i> Cancel Registration
                                    </button>
                                </form>
                            @elseif($event->participants_count >= $event->max_participants)
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                    <p class="mb-0 mt-2">Event is full</p>
                                </div>
                            @else
                                <h4 class="mb-3">Join This Event</h4>
                                <div class="progress mb-3" style="height: 8px;">
                                    @php
                                        $percentage = ($event->participants_count / $event->max_participants) * 100;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                                <p class="text-muted mb-3">
                                    {{ $event->max_participants - $event->participants_count }} spots remaining
                                </p>
                                <form action="{{ route('events.register', $event) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 btn-lg">
                                        <i class="bi bi-check-circle"></i> Register Now
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle fs-1"></i>
                                <p class="mt-2">Please login to register for this event</p>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login to Register
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Participants List -->
                @if($event->participants->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-people"></i> Participants ({{ $event->participants->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($event->participants->take(10) as $participant)
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 35px; height: 35px; font-weight: bold; font-size: 0.8rem;">
                                        {{ $participant->initials() }}
                                    </div>
                                    <span class="ms-2">{{ $participant->name }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($event->participants->count() > 10)
                        <small class="text-muted">
                            and {{ $event->participants->count() - 10 }} more...
                        </small>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if(Auth::check() && Auth::user()->isAdmin())
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" 
                               value="{{ $event->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea name="description" id="edit_description" rows="4" 
                                  class="form-control" required>{{ $event->description }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_location" class="form-label">Location</label>
                            <input type="text" name="location" id="edit_location" class="form-control" 
                                   value="{{ $event->location }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_event_date" class="form-label">Date & Time</label>
                            <input type="datetime-local" name="event_date" id="edit_event_date" 
                                   class="form-control" value="{{ $event->event_date_time_local }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_max_participants" class="form-label">Max Participants</label>
                            <input type="number" name="max_participants" id="edit_max_participants" 
                                   class="form-control" value="{{ $event->max_participants }}" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Image</label>
                        <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
