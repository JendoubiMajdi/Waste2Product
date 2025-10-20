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

                        <!-- Event Feedback Section -->
        @if($event->hasEnded())
        <div class="row mt-5">
            <div class="col-12">
                <!-- AI Analytics Button (For Admin/Organizer) -->
                @if(Auth::check() && (Auth::user()->isAdmin() || $event->user_id === Auth::id()))
                    <div class="mb-3 text-end">
                        <a href="{{ route('events.analytics', $event) }}" class="btn btn-lg btn-primary">
                            <i class="bi bi-robot"></i> View AI Analytics
                        </a>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="bi bi-star-fill text-warning"></i> Event Feedback
                            </h4>
                            @php
                                $avgRating = $event->averageRating();
                                $feedbackCount = $event->feedback()->count();
                            @endphp
                            @if($feedbackCount > 0)
                            <div class="text-end">
                                <div class="d-flex align-items-center">
                                    <span class="fs-3 fw-bold text-warning me-2">{{ number_format($avgRating, 1) }}</span>
                                    <div>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($avgRating))
                                                    <i class="bi bi-star-fill"></i>
                                                @elseif($i - 0.5 <= $avgRating)
                                                    <i class="bi bi-star-half"></i>
                                                @else
                                                    <i class="bi bi-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $feedbackCount }} {{ Str::plural('review', $feedbackCount) }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @auth
                            @php
                                $userFeedback = $event->getUserFeedback(Auth::id());
                            @endphp
                            
                            @if($userFeedback)
                                <!-- User's existing feedback (editable) -->
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-2"><i class="bi bi-info-circle"></i> Your Feedback</h6>
                                            <div class="mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $userFeedback->rating ? '-fill' : '' }} text-warning"></i>
                                                @endfor
                                                <span class="ms-2 text-muted">{{ $userFeedback->rating }}/5</span>
                                            </div>
                                            @if($userFeedback->comment)
                                                <p class="mb-0" id="feedback-comment-display">{{ $userFeedback->comment }}</p>
                                            @endif
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-2" onclick="toggleEditFeedback()">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form action="{{ route('events.feedback.destroy', $userFeedback) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete your feedback?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Edit Form (hidden by default) -->
                                    <div id="edit-feedback-form" style="display: none;" class="mt-3 pt-3 border-top">
                                        <form action="{{ route('events.feedback.update', $userFeedback) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Update Your Rating</label>
                                                <div class="star-rating-input" id="edit-star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star{{ $i <= $userFeedback->rating ? '-fill' : '' }} star-icon" 
                                                           data-rating="{{ $i }}" 
                                                           style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="edit-rating-input" value="{{ $userFeedback->rating }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit-comment" class="form-label fw-bold">Update Your Comment</label>
                                                <textarea name="comment" 
                                                          id="edit-comment" 
                                                          class="form-control" 
                                                          rows="3" 
                                                          maxlength="1000" 
                                                          placeholder="Share your experience...">{{ $userFeedback->comment }}</textarea>
                                                <small class="text-muted">Optional, max 1000 characters</small>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-save"></i> Save Changes
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="toggleEditFeedback()">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <!-- Add new feedback form -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-3">
                                            <i class="bi bi-chat-left-text"></i> Share Your Experience
                                        </h5>
                                        <form action="{{ route('events.feedback.store', $event) }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Rate this event</label>
                                                <div class="star-rating-input" id="star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="bi bi-star star-icon" 
                                                           data-rating="{{ $i }}" 
                                                           style="font-size: 2.5rem; cursor: pointer; color: #ffc107;"></i>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="rating-input" required>
                                                @error('rating')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="comment" class="form-label fw-bold">Your Comment</label>
                                                <textarea name="comment" 
                                                          id="comment" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          maxlength="1000" 
                                                          placeholder="Tell us what you thought about this event..."></textarea>
                                                <small class="text-muted">Optional, max 1000 characters</small>
                                                @error('comment')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="bi bi-send"></i> Submit Feedback
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Please <a href="{{ route('login') }}">login</a> to leave feedback for this event.
                            </div>
                        @endauth

                        <!-- Display all feedback -->
                        @if($feedbackCount > 0)
                            <h5 class="mb-3 mt-4">
                                <i class="bi bi-chat-dots"></i> All Reviews ({{ $feedbackCount }})
                            </h5>
                            <div class="feedback-list">
                                @foreach($event->feedback()->with('user')->latest()->get() as $feedback)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 45px; height: 45px; font-weight: bold;">
                                                        {{ $feedback->user->initials() }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $feedback->user->name }}</h6>
                                                        <div class="text-warning">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }}"></i>
                                                            @endfor
                                                            <span class="text-muted ms-2">{{ $feedback->rating }}/5</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                                            </div>
                                            @if($feedback->comment)
                                                <p class="mb-0 ms-5 ps-3">{{ $feedback->comment }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                                <p class="mt-3">No feedback yet. Be the first to share your experience!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

<style>
.star-icon:hover,
.star-icon.active {
    transform: scale(1.2);
    transition: transform 0.2s;
}
</style>

<script>
// Star rating interaction for new feedback
document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('star-rating');
    if (starRating) {
        const stars = starRating.querySelectorAll('.star-icon');
        const ratingInput = document.getElementById('rating-input');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        starRating.addEventListener('mouseleave', function() {
            stars.forEach(s => s.classList.remove('active'));
        });
    }

    // Star rating interaction for edit feedback
    const editStarRating = document.getElementById('edit-star-rating');
    if (editStarRating) {
        const stars = editStarRating.querySelectorAll('.star-icon');
        const ratingInput = document.getElementById('edit-rating-input');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = this.dataset.rating;
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
        
        editStarRating.addEventListener('mouseleave', function() {
            stars.forEach(s => s.classList.remove('active'));
        });
    }
});

function toggleEditFeedback() {
    const editForm = document.getElementById('edit-feedback-form');
    const commentDisplay = document.getElementById('feedback-comment-display');
    
    if (editForm.style.display === 'none') {
        editForm.style.display = 'block';
    } else {
        editForm.style.display = 'none';
    }
}
</script>

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
