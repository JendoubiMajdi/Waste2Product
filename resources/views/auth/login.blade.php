@extends('layouts.app')

@section('title', 'Sign In')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Sign In</h4>
                </div>
                <div class="card-body">
                    @if(session('registration_success'))
                        <div class="alert alert-success mt-3">
                            Registration successful! Please check your email for a verification code before logging in.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                    <div class="mb-3 text-end">
                        <a href="{{ route('password.request') }}">Mot de passe oublié&nbsp;?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
