@extends('layouts.app')

@section('title', 'Create Event')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Create New Event</h2>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Events</a></li>
                <li class="breadcrumb-item active">Create Event</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-calendar-event"></i> Organize a Community Event
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Please fix the following errors:</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" id="createEventForm">
                            @csrf

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">
                                    Event Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       placeholder="e.g., Beach Cleanup Drive, Tree Planting Event"
                                       value="{{ old('title') }}"
                                       required
                                       maxlength="255">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-bold">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          rows="6" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Describe the event, its purpose, what participants should bring, etc."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="mb-4">
                                <label for="location" class="form-label fw-bold">
                                    Location <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="location" 
                                       id="location" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       placeholder="e.g., Central Park, 123 Main Street, Online"
                                       value="{{ old('location') }}"
                                       required>
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date & Time -->
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="event_date" class="form-label fw-bold">
                                        Date & Time <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" 
                                           name="event_date" 
                                           id="event_date" 
                                           class="form-control @error('event_date') is-invalid @enderror" 
                                           value="{{ old('event_date') }}"
                                           min="{{ now()->addDay()->format('Y-m-d\TH:i') }}"
                                           required>
                                    @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Event must be at least 24 hours from now</small>
                                </div>

                                <!-- Max Participants -->
                                <div class="col-md-6 mb-4">
                                    <label for="max_participants" class="form-label fw-bold">
                                        Max Participants <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="max_participants" 
                                           id="max_participants" 
                                           class="form-control @error('max_participants') is-invalid @enderror" 
                                           placeholder="e.g., 50"
                                           value="{{ old('max_participants', 50) }}"
                                           min="1"
                                           max="1000"
                                           required>
                                    @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="image" class="form-label fw-bold">
                                    Event Image (Optional)
                                </label>
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*"
                                       onchange="previewImage(event)">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Recommended size: 1200x600px. Max: 2MB</small>
                                
                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="preview" src="" class="img-fluid rounded border" style="max-height: 300px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                        <i class="bi bi-x-circle"></i> Remove Image
                                    </button>
                                </div>
                            </div>

                            <!-- Guidelines -->
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="bi bi-info-circle"></i> Event Guidelines
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Choose an environmental or community-focused theme</li>
                                    <li>Provide clear instructions and meeting points</li>
                                    <li>Specify what participants should bring</li>
                                    <li>Set realistic participant limits based on venue capacity</li>
                                    <li>Include safety precautions if applicable</li>
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('events.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-calendar-check"></i> Create Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card mt-4 border-success">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-success">
                            <i class="bi bi-lightbulb"></i> Tips for Successful Events
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Be Descriptive:</strong> Include all important details about the event</li>
                            <li><strong>Choose the Right Time:</strong> Consider your audience's availability</li>
                            <li><strong>Set Clear Expectations:</strong> What should participants bring or prepare?</li>
                            <li><strong>Promote Early:</strong> Give people time to plan and register</li>
                            <li><strong>Follow Up:</strong> Send reminders to registered participants</li>
                            <li><strong>Safety First:</strong> Include any necessary safety information</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Image preview functionality
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('preview').src = '';
}

// Form validation
document.getElementById('createEventForm').addEventListener('submit', function(e) {
    const eventDate = new Date(document.getElementById('event_date').value);
    const now = new Date();
    const minDate = new Date(now.getTime() + 24 * 60 * 60 * 1000); // 24 hours from now

    if (eventDate < minDate) {
        e.preventDefault();
        alert('Event must be scheduled at least 24 hours from now');
        return;
    }

    const maxParticipants = parseInt(document.getElementById('max_participants').value);
    if (maxParticipants < 1 || maxParticipants > 1000) {
        e.preventDefault();
        alert('Max participants must be between 1 and 1000');
        return;
    }

    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-hourglass-split"></i> Creating...';
});

// Set minimum date to tomorrow
document.addEventListener('DOMContentLoaded', function() {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('event_date').min = tomorrow.toISOString().slice(0, 16);
});
</script>
@endsection
