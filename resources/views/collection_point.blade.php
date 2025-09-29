@extends('layouts.app')

@section('title', 'Collection Points')
@section('content')
<div class="container py-5">
    <h2>Available Collection Points</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($points as $point)
                <tr>
                    <td>{{ $point->name }}</td>
                    <td>{{ $point->address }}</td>
                    <td>{{ ucfirst($point->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if (Auth::check() && Auth::user()->role === 'collector')
        <a href="{{ route('collection_points.dashboard') }}" class="btn btn-primary mt-3">Manage My Collection Points</a>
    @endif
</div>
@endsection
