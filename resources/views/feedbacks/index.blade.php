{{-- filepath: resources/views/feedbacks/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Feedbacks</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('feedbacks.create') }}" class="btn btn-primary mb-3">Add Feedback</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($feedbacks as $feedback)
                <tr>
                    <td>{{ $feedback->note }}</td>
                    <td>{{ $feedback->commentaire }}</td>
                    <td>{{ $feedback->date }}</td>
                    <td>
                        <a href="{{ route('feedbacks.show', $feedback->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('feedbacks.edit', $feedback->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('feedbacks.destroy', $feedback->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this feedback?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No feedbacks found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
