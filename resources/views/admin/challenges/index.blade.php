@extends('admin.layouts.app')

@section('title', 'Challenges Management')

@section('content')
<div class="d-flex justify-content-between align-items-center" style="margin-bottom: 32px;">
    <div>
        <h1 style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">Challenges Management</h1>
        <p style="color: #6b7280; font-size: 14px; margin: 0;">Create and manage environmental challenges</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createChallengeModal">
        <iconify-icon icon="mdi:plus" style="margin-right: 8px;"></iconify-icon>
        Create Challenge
    </button>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="admin-card">
            @if($challenges->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Points</th>
                                <th>Status</th>
                                <th>Submissions</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($challenges as $challenge)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($challenge->image)
                                                <img src="data:image/jpeg;base64,{{ $challenge->image }}" alt="{{ $challenge->title }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 12px;">
                                            @else
                                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center;">
                                                    <iconify-icon icon="mdi:trophy" style="color: white; font-size: 20px;"></iconify-icon>
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 500; color: #1a1a1a;">{{ $challenge->title }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #6b7280;">
                                            {{ $challenge->description }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $challenge->points }} pts</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $challenge->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($challenge->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $challenge->submissions_count ?? 0 }}</span>
                                    </td>
                                    <td style="color: #6b7280; font-size: 13px;">
                                        {{ $challenge->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editChallenge({{ $challenge->id }})">
                                                <iconify-icon icon="mdi:pencil" style="margin-right: 4px;"></iconify-icon>
                                                Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteChallenge({{ $challenge->id }})">
                                                <iconify-icon icon="mdi:delete" style="margin-right: 4px;"></iconify-icon>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $challenges->links() }}
                </div>
            @else
                <div style="text-align: center; padding: 60px 24px;">
                    <iconify-icon icon="mdi:trophy-outline" style="font-size: 64px; color: #d1d5db; margin-bottom: 16px;"></iconify-icon>
                    <h3 style="font-size: 18px; font-weight: 600; color: #6b7280; margin-bottom: 8px;">No Challenges Yet</h3>
                    <p style="color: #9ca3af; font-size: 14px; margin-bottom: 24px;">Create your first challenge to get started</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createChallengeModal">
                        <iconify-icon icon="mdi:plus" style="margin-right: 8px;"></iconify-icon>
                        Create First Challenge
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Challenge Modal -->
<div class="modal fade" id="createChallengeModal" tabindex="-1" aria-labelledby="createChallengeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createChallengeModalLabel">Create New Challenge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.challenges.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="points" class="form-label">Points Reward</label>
                        <input type="number" class="form-control" id="points" name="points" min="1" max="100" value="10" required>
                        <small class="text-muted">Points awarded to users who complete this challenge (1-100)</small>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Challenge Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewChallengeImage(event)">
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="preview" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Challenge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewChallengeImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function editChallenge(id) {
    // TODO: Implement edit functionality
    alert('Edit challenge #' + id);
}

function deleteChallenge(id) {
    if (confirm('Are you sure you want to delete this challenge?')) {
        fetch(`/admin/challenges/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error deleting challenge');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting challenge');
        });
    }
}
</script>

<style>
.table {
    margin-bottom: 0;
}

.table thead th {
    background-color: #f9fafb;
    color: #6b7280;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
    padding: 12px 16px;
}

.table tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}

.table tbody tr:hover {
    background-color: #f9fafb;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.modal-content {
    border-radius: 12px;
    border: none;
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 20px 24px;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 16px 24px;
}
</style>
@endsection
