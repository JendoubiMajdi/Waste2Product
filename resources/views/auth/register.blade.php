@extends('layouts.app')

@section('title', 'Register')

@section('content')
<!-- Hero Section: blurred background with centered register form -->
<section id="hero" class="hero section dark-background" style="position:relative; padding:0;">

  <style>
    /* Reuse login styles for register form */
    #hero { min-height: 100vh; display:flex; align-items:center; }
    #hero .hero-background { position:absolute; inset:0; z-index:0; overflow:hidden; }
    #hero .hero-background img { width:100%; height:100%; object-fit:cover; -webkit-filter: blur(6px) brightness(0.6); filter: blur(6px) brightness(0.6); transform: scale(1.03); }
    #hero .overlay { position:absolute; inset:0; background:rgba(6,14,9,0.25); z-index:1; }
    .hero-inner { position:relative; z-index:2; width:100%; }
    .login-card { width: calc(100% - 50px); max-width:480px; margin: 2rem auto; padding:2rem; border-radius:10px; background: rgba(110, 190, 116, 0.95); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
    .login-card .form-control{border-radius:6px}
    .login-brand { text-align:center; margin-bottom:1rem; }
  </style>

  <div class="hero-background">
    <img src="/assets/img/bg/bg-1.jpg" alt="" aria-hidden="true">
    <div class="overlay"></div>
  </div>

  <div class="container hero-inner">
    <div class="row justify-content-center align-items-center">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="feature-card login-card">
          <div class="login-brand">
            <h3 class="mb-1">Create your account</h3>
            <p class="text-muted">Register as a collector, customer or transporter</p>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Full name</label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Your full name" required value="{{ old('name') }}" autofocus>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" required value="{{ old('email') }}">
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Confirm password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">Select role</option>
                <option value="collector" {{ old('role') == 'collector' ? 'selected' : '' }}>Collector</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="transporter" {{ old('role') == 'transporter' ? 'selected' : '' }}>Transporter</option>
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Register</button>
            </div>

            <div class="text-center mt-3">
              <small class="text-muted">Already have an account? <a href="{{ route('login') }}">Sign in</a></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</section><!-- /Hero Section -->

@endsection