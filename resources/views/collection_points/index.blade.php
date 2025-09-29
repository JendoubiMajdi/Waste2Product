@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Available Collection Points</h2>
    @auth
        @if(Auth::user()->role === 'collector')
            <a href="{{ route('collection_points.dashboard') }}" class="btn btn-primary mb-3">Manage My Collection Points</a>
        @endif
    @endauth
    <div class="row">
        @foreach($collectionPoints as $point)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @php
                        $isBase64 = false;
                        if ($point->image) {
                            $isBase64 = Str::startsWith($point->image, '/9j/') || Str::startsWith($point->image, 'iVBOR');
                        }
                    @endphp
                    @if($point->image && $isBase64)
                        <img src="data:image/jpeg;base64,{{ $point->image }}" class="card-img-top" alt="Collection Point Image" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/no_image.jpg') }}" class="card-img-top" alt="No Image" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $point->name }}</h5>
                        <p class="card-text">{{ $point->address }}</p>
                        <p class="card-text"><strong>Status:</strong> {{ ucfirst($point->status) }}</p>
                        <p class="card-text"><strong>Contact:</strong> {{ $point->contact_phone }}</p>
                        <p class="card-text"><strong>Working Hours:</strong> {{ $point->working_hours }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
        {{ $collectionPoints->links() }}
    </div>
</div>
@endsection
