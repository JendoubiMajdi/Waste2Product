@extends('layouts.app')

@section('title', 'Create Forum Post')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Create New Post</h2>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item active">Create Post</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Share Your Thoughts
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                            @csrf

                            <!-- Title -->
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">
                                    Post Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       placeholder="Enter a descriptive title for your post"
                                       value="{{ old('title') }}"
                                       required
                                       maxlength="255">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maximum 255 characters</small>
                            </div>

                            <!-- Content -->
                            <div class="mb-4">
                                <label for="content" class="form-label fw-bold">
                                    Content <span class="text-danger">*</span>
                                </label>
                                <textarea name="content" 
                                          id="content" 
                                          rows="8" 
                                          class="form-control @error('content') is-invalid @enderror" 
                                          placeholder="Share your thoughts, ideas, or questions with the community..."
                                          required>{{ old('content') }}</textarea>
                                @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Be respectful and constructive. Minimum 10 characters.</small>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="image" class="form-label fw-bold">
                                    Image (Optional)
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
                                <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                                
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
                                    <i class="bi bi-info-circle"></i> Community Guidelines
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Be respectful and courteous to all members</li>
                                    <li>Stay on topic and provide constructive feedback</li>
                                    <li>No spam, advertising, or self-promotion</li>
                                    <li>No offensive, abusive, or discriminatory content</li>
                                    <li>Protect privacy - don't share personal information</li>
                                </ul>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('forum.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-send"></i> Publish Post
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card mt-4 border-success">
                    <div class="card-header bg-light">
                        <h5 class="mb-0 text-success">
                            <i class="bi bi-lightbulb"></i> Tips for Great Posts
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Clear Title:</strong> Use a descriptive title that summarizes your post</li>
                            <li><strong>Detailed Content:</strong> Provide context and details to help others understand</li>
                            <li><strong>Use Images:</strong> Visual content can enhance your message</li>
                            <li><strong>Be Specific:</strong> Ask clear questions or share specific information</li>
                            <li><strong>Engage:</strong> Respond to comments and feedback from the community</li>
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
document.getElementById('createPostForm').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();

    if (title.length < 5) {
        e.preventDefault();
        alert('Title must be at least 5 characters long');
        return;
    }

    if (content.length < 10) {
        e.preventDefault();
        alert('Content must be at least 10 characters long');
        return;
    }

    // Disable submit button to prevent double submission
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-hourglass-split"></i> Publishing...';
});

// Character counter for content
const contentTextarea = document.getElementById('content');
contentTextarea.addEventListener('input', function() {
    const length = this.value.length;
    const color = length < 10 ? 'text-danger' : 'text-success';
    // Optional: Add character counter if desired
});
</script>
@endsection
