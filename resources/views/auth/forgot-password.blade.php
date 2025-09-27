@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="container py-5">
    <h2>Mot de passe oublié</h2>
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
            @error('email')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
    </form>
</div>
@endsection
