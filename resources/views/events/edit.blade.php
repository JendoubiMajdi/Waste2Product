@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Event</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="form-label fs-5">Event Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="picture" class="form-label fs-5">Event Picture</label>
                            <input type="file" class="form-control @error('picture') is-invalid @enderror"
                                   id="picture" name="picture" accept="image/*">
                            @error('picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($event->picture)
                                <div class="mt-2">
                                    <small class="text-muted">Current picture:</small>
                                    <img src="{{ asset('storage/' . $event->picture) }}" alt="Current Event Picture" class="img-thumbnail mt-1" style="max-width: 200px;">
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fs-5">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="date" class="form-label fs-5">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-lg @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $event->date) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="time" class="form-label fs-5">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control form-control-lg @error('time') is-invalid @enderror" id="time" name="time" value="{{ old('time', $event->time) }}" required>
                                @error('time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="meet_link" class="form-label fs-5">Google Meet Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control form-control-lg @error('meet_link') is-invalid @enderror" id="meet_link" name="meet_link" value="{{ old('meet_link', $event->meet_link) }}" required>
                            @error('meet_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection
