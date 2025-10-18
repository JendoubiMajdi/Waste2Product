@extends('layouts.app')

@section('title', 'Manage My Collection Points')
@section('content')
<div class="container py-5">
    <h2>My Collection Points</h2>
    <a href="{{ route('collection_points.create') }}" class="btn btn-success mb-3">Create New Collection Point</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Waste Count</th>
                <th>Total Waste Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($points as $point)
                <tr>
                    <td>{{ $point->name }}</td>
                    <td>{{ ucfirst($point->status) }}</td>
                    <td>{{ $point->waste_count }}</td>
                    <td>{{ $point->waste_total }}</td>
                    <td>
                        <a href="{{ route('collection_points.edit', $point->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('collection_points.destroy', $point->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
