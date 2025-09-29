@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Event</h4>
                </div>
                <div class="card-body p-4">
                    {{-- FIX: Changed action from events.create (GET) to events.store (POST) --}}
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="form-label fs-5">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                id="title" name="title" value="{{ old('title') }}"
                                placeholder="Enter event title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="picture" class="form-label fs-5">Event Picture</label>
                            <input type="file" class="form-control @error('picture') is-invalid @enderror"
                                id="picture" name="picture" accept="image/*">
                            <div class="form-text">Upload a picture for your event (JPEG, PNG, JPG, GIF, max 2MB)</div>
                            @error('picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fs-5">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="5"
                                    placeholder="Describe your event..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="date" class="form-label fs-5">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-lg @error('date') is-invalid @enderror"
                                        id="date" name="date" value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="time" class="form-label fs-5">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-lg @error('time') is-invalid @enderror"
                                        id="time" name="time" value="{{ old('time') }}" required>
                                @error('time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="meet_link" class="form-label fs-5">Google Meet Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control form-control-lg @error('meet_link') is-invalid @enderror"
                                id="meet_link" name="meet_link" value="{{ old('meet_link') }}"
                                placeholder="https://meet.google.com/xxx-xxxx-xxx" required>
                            @error('meet_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-lg me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-1"></i> Create Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.card-header {
    border-bottom: 2px solid rgba(255,255,255,0.1);
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
