{{-- filepath: resources/views/feedbacks/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Feedback</h2>
    <form action="{{ route('feedbacks.update', $feedback->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="note" class="form-label">Note (1-5)</label>
            <input type="number" name="note" id="note" class="form-control" min="1" max="5" value="{{ $feedback->note }}" required>
        </div>
        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" required>{{ $feedback->commentaire }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Feedback</button>
        <a href="{{ route('feedbacks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
