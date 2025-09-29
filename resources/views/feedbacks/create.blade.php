{{-- filepath: resources/views/feedbacks/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Feedback</h2>
    <form action="{{ route('feedbacks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="note" class="form-label">Note (1-5)</label>
            <input type="number" name="note" id="note" class="form-control" min="1" max="5" required>
        </div>
        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaire</label>
            <textarea name="commentaire" id="commentaire" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>
@endsection
