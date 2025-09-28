@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Order Details</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Order #{{ $order->id }}</h5>
            <p class="card-text"><strong>Date:</strong> {{ $order->date }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $order->statut }}</p>
            <p class="card-text"><strong>Client:</strong> {{ $order->client ? $order->client->name : '' }}</p>
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('orders.index') }}" class="btn btn-accent">Back</a>
        </div>
    </div>
</div>
@endsection