@extends('layouts.app')

@section('title', 'Social Feed')

@push('head')
<style>
  .feed-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
  }

  .feed-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(0, 146, 126, 0.2);
  }

  .create-post-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
  }

  .create-post-form textarea {
    width: 100%;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    font-size: 15px;
    resize: vertical;
    min-height: 100px;
    transition: all 0.3s ease;
  }

  .create-post-form textarea:focus {
    outline: none;
    border-color: #00927E;
  }

  .create-post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
  }

  .visibility-selector {
    display: flex;
    gap: 12px;
  }

  .visibility-option {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 20px;
    border: 2px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
  }

  .visibility-option:has(input:checked) {
    border-color: #00927E;
    background: rgba(0, 146, 126, 0.1);
    color: #00927E;
  }

  .visibility-option input[type="radio"] {
    display: none;
  }

  .btn-post {
    padding: 10px 32px;
    border-radius: 25px;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .btn-post:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 146, 126, 0.3);
  }

  .upload-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6b7280;
    font-size: 20px;
  }

  .upload-btn:hover {
    background: #00927E;
    color: white;
    transform: translateY(-2px);
  }

  #imagePreview {
    position: relative;
  }

  #imagePreview img {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .btn-add-friend {
    padding: 4px 8px;
    border-radius: 15px;
    background: rgba(0, 146, 126, 0.1);
    color: #00927E;
    border: 1px solid #00927E;
    font-size: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .btn-add-friend:hover {
    background: #00927E;
    color: white;
    transform: translateY(-1px);
  }

  .post-image {
    margin-top: 12px;
  }

  .post-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    transition: all 0.3s ease;
  }

  .post-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }

  .post-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .post-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 18px;
    flex-shrink: 0;
    text-decoration: none;
  }

  .post-author-info {
    flex: 1;
  }

  .post-author-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
    text-decoration: none;
  }

  .post-author-name:hover {
    color: #00927E;
  }

  .post-time {
    color: #6b7280;
    font-size: 13px;
  }

  .post-visibility {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 12px;
    color: #6b7280;
  }

  .post-content {
    color: #374151;
    line-height: 1.6;
    margin-bottom: 16px;
  }

  .shared-post-preview {
    background: #f9fafb;
    border-left: 3px solid #00927E;
    padding: 16px;
    border-radius: 8px;
    margin-top: 12px;
  }

  .shared-post-preview small {
    display: block;
    color: #6b7280;
    margin-bottom: 8px;
  }

  .post-actions {
    display: flex;
    gap: 20px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
  }

  .post-action-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: #6b7280;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
  }

  .post-action-btn:hover {
    background: #f3f4f6;
    color: #00927E;
  }

  .post-menu {
    position: relative;
  }

  .post-menu-btn {
    padding: 8px;
    border: none;
    background: transparent;
    color: #6b7280;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .post-menu-btn:hover {
    background: #f3f4f6;
    color: #1f2937;
  }

  .empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
  }

  .empty-state i {
    font-size: 64px;
    color: #d1d5db;
    margin-bottom: 20px;
  }

  /* Modal Styles */
  .modal-content {
    border-radius: 16px;
    border: none;
  }

  .modal-header {
    background: linear-gradient(135deg, #00927E 0%, #00b894 100%);
    color: white;
    border-radius: 16px 16px 0 0;
  }

  .btn-modal-action {
    padding: 10px 24px;
    border-radius: 25px;
    border: none;
    font-weight: 500;
  }
</style>
@endpush

@section('content')
<div class="feed-container">
  
  <div class="feed-header">
    <h1 class="mb-2"><i class="bi bi-file-post-fill"></i> Social Feed</h1>
    <p class="mb-0">Share your thoughts and connect with the community</p>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <!-- Create Post -->
  <div class="create-post-card">
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" class="create-post-form">
      @csrf
      <textarea name="content" placeholder="What's on your mind?" required>{{ old('content') }}</textarea>
      
      <!-- Image Upload Preview -->
      <div id="imagePreview" class="mt-3" style="display: none;">
        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border-radius: 12px;">
        <button type="button" onclick="removeImage()" class="btn btn-sm btn-danger mt-2">
          <i class="bi bi-x-circle"></i> Remove Image
        </button>
      </div>
      
      <div class="create-post-footer">
        <div class="d-flex align-items-center gap-3">
          <!-- Image Upload Button -->
          <label for="imageUpload" class="upload-btn" title="Add Photo">
            <i class="bi bi-image"></i>
            <input type="file" id="imageUpload" name="image" accept="image/*" onchange="previewImage(event)" style="display: none;">
          </label>
          
          <!-- Visibility Selector -->
          <div class="visibility-selector">
            <label class="visibility-option">
              <input type="radio" name="visibility" value="public" checked>
              <i class="bi bi-globe"></i> Public
            </label>
            <label class="visibility-option">
              <input type="radio" name="visibility" value="friends">
              <i class="bi bi-people"></i> Friends Only
            </label>
          </div>
        </div>
        
        <button type="submit" class="btn-post">
          <i class="bi bi-send-fill"></i> Post
        </button>
      </div>
    </form>
  </div>

  <!-- Posts Feed -->
  @forelse($posts as $post)
  <div class="post-card">
    <div class="post-header">
      <a href="{{ route('profile.show', $post->user->id) }}" class="post-avatar">
        {{ strtoupper(substr($post->user->name, 0, 1)) }}
      </a>
      <div class="post-author-info">
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('profile.show', $post->user->id) }}" class="post-author-name">
            {{ $post->user->name }}
          </a>
          @if($post->user_id != Auth::id() && !Auth::user()->isFriendsWith($post->user_id))
          <form action="{{ route('friends.request') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="friend_id" value="{{ $post->user_id }}">
            <button type="submit" class="btn-add-friend" title="Add Friend">
              <i class="bi bi-person-plus"></i>
            </button>
          </form>
          @endif
        </div>
        <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
      </div>
      <span class="post-visibility">
        <i class="bi bi-{{ $post->visibility == 'public' ? 'globe' : 'people' }}"></i>
        {{ ucfirst($post->visibility) }}
      </span>
      <div class="post-menu dropdown">
        <button class="post-menu-btn" type="button" data-bs-toggle="dropdown">
          <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          @if($post->user_id == Auth::id())
          <li>
            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-trash"></i> Delete
              </button>
            </form>
          </li>
          @else
          <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#reportModal{{ $post->id }}">
              <i class="bi bi-flag"></i> Report
            </button>
          </li>
          @endif
          <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#shareModal{{ $post->id }}">
              <i class="bi bi-share"></i> Share
            </button>
          </li>
        </ul>
      </div>
    </div>

    <div class="post-content">
      {{ $post->content }}
    </div>

    @if($post->image)
    <div class="post-image mb-3">
      <img src="data:image/jpeg;base64,{{ $post->image }}" alt="Post image" style="max-width: 100%; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
    </div>
    @endif

    @if($post->sharedPost)
    <div class="shared-post-preview">
      <small>
        <i class="bi bi-share"></i> Shared from 
        <a href="{{ route('profile.show', $post->sharedPost->user->id) }}">{{ $post->sharedPost->user->name }}</a>
      </small>
      <div>{{ $post->sharedPost->content }}</div>
      @if($post->sharedPost->image)
      <img src="data:image/jpeg;base64,{{ $post->sharedPost->image }}" alt="Shared post image" style="max-width: 100%; margin-top: 12px; border-radius: 8px;">
      @endif
    </div>
    @endif

    <div class="post-actions">
      <button class="post-action-btn">
        <i class="bi bi-heart"></i> Like ({{ $post->likes_count ?? 0 }})
      </button>
      <button class="post-action-btn">
        <i class="bi bi-chat"></i> Comment ({{ $post->comments_count ?? 0 }})
      </button>
      <button class="post-action-btn" data-bs-toggle="modal" data-bs-target="#shareModal{{ $post->id }}">
        <i class="bi bi-share"></i> Share ({{ $post->share_count ?? 0 }})
      </button>
    </div>
  </div>

  <!-- Share Modal -->
  <div class="modal fade" id="shareModal{{ $post->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-share"></i> Share Post</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('posts.share') }}" method="POST">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            
            <!-- Original Post Preview -->
            <div class="mb-3 p-3 bg-light rounded">
              <small class="text-muted">Original post by {{ $post->user->name }}</small>
              <p class="mb-0 mt-2">{{ Str::limit($post->content, 100) }}</p>
              @if($post->image)
              <img src="data:image/jpeg;base64,{{ $post->image }}" alt="Post" style="max-width: 100%; max-height: 150px; margin-top: 8px; border-radius: 8px;">
              @endif
            </div>
            
            <div class="mb-3">
              <label class="form-label">Add a comment (optional)</label>
              <textarea name="content" class="form-control" rows="3" placeholder="Say something about this..."></textarea>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Visibility</label>
              <div class="d-flex gap-2">
                <label class="visibility-option flex-fill">
                  <input type="radio" name="visibility" value="public" checked>
                  <i class="bi bi-globe"></i> Public
                </label>
                <label class="visibility-option flex-fill">
                  <input type="radio" name="visibility" value="friends">
                  <i class="bi bi-people"></i> Friends Only
                </label>
              </div>
            </div>
            
            <button type="submit" class="btn btn-modal-action" style="background: #00927E; color: white; width: 100%;">
              <i class="bi bi-share-fill"></i> Share to Feed
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Report Modal -->
  @if($post->user_id != Auth::id())
  <div class="modal fade" id="reportModal{{ $post->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-flag"></i> Report Post</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('posts.report') }}" method="POST" id="reportForm{{ $post->id }}">
            @csrf
            <input type="hidden" name="post_id" value="{{ $post->id }}">
            
            <div class="mb-3">
              <label class="form-label fw-medium">Select Reason *</label>
              <select class="form-select" id="reportReason{{ $post->id }}" onchange="toggleReasonField({{ $post->id }})" required>
                <option value="">Choose a reason...</option>
                <option value="Spam or misleading">Spam or misleading</option>
                <option value="Harassment or hate speech">Harassment or hate speech</option>
                <option value="Violence or dangerous content">Violence or dangerous content</option>
                <option value="Nudity or sexual content">Nudity or sexual content</option>
                <option value="False information">False information</option>
                <option value="Scam or fraud">Scam or fraud</option>
                <option value="Intellectual property violation">Intellectual property violation</option>
                <option value="Other">Other (please specify)</option>
              </select>
            </div>
            
            <div class="mb-3" id="customReasonField{{ $post->id }}" style="display: none;">
              <label class="form-label fw-medium">Please specify your reason *</label>
              <textarea class="form-control" rows="4" id="customReasonText{{ $post->id }}" placeholder="Please explain why you're reporting this post..."></textarea>
            </div>
            
            <input type="hidden" name="reason" id="finalReason{{ $post->id }}">
            
            <button type="submit" class="btn btn-modal-action" style="background: #ef4444; color: white; width: 100%;">
              <i class="bi bi-flag-fill"></i> Submit Report
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
  @endif
  @empty
  <div class="empty-state">
    <i class="bi bi-file-post"></i>
    <h3>No posts yet</h3>
    <p>Be the first to share something!</p>
  </div>
  @endforelse

  <!-- Pagination -->
  @if($posts->hasPages())
  <div class="d-flex justify-content-center mt-4">
    {{ $posts->links() }}
  </div>
  @endif

</div>

<script>
function previewImage(event) {
  const preview = document.getElementById('imagePreview');
  const previewImg = document.getElementById('previewImg');
  const file = event.target.files[0];
  
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImg.src = e.target.result;
      preview.style.display = 'block';
    }
    reader.readAsDataURL(file);
  }
}

function removeImage() {
  document.getElementById('imageUpload').value = '';
  document.getElementById('imagePreview').style.display = 'none';
  document.getElementById('previewImg').src = '';
}

function toggleReasonField(postId) {
  const dropdown = document.getElementById('reportReason' + postId);
  const customField = document.getElementById('customReasonField' + postId);
  const customText = document.getElementById('customReasonText' + postId);
  
  if (dropdown.value === 'Other') {
    customField.style.display = 'block';
    customText.required = true;
  } else {
    customField.style.display = 'none';
    customText.required = false;
  }
}

// Handle form submission to combine dropdown and custom reason
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('[id^="reportForm"]').forEach(form => {
    form.addEventListener('submit', function(e) {
      const postId = this.id.replace('reportForm', '');
      const dropdown = document.getElementById('reportReason' + postId);
      const customText = document.getElementById('customReasonText' + postId);
      const finalReason = document.getElementById('finalReason' + postId);
      
      if (dropdown.value === 'Other') {
        if (!customText.value.trim()) {
          e.preventDefault();
          alert('Please specify your reason for reporting this post.');
          return false;
        }
        finalReason.value = customText.value.trim();
      } else {
        finalReason.value = dropdown.value;
      }
    });
  });
});
</script>
@endsection
