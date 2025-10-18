@extends('layouts.app')

@section('title', 'Login')

@section('content')
<!-- Hero Section: blurred background with centered login form -->
<section id="hero" class="hero section dark-background" style="position:relative; padding:0;">

  <style>
    /* Keep styling minimal and reuse template classes where possible */
    #hero { min-height: 100vh; display:flex; align-items:center; }
    #hero .hero-background { position:absolute; inset:0; z-index:0; overflow:hidden; }
    #hero .hero-background img { width:100%; height:100%; object-fit:cover; -webkit-filter: blur(6px) brightness(0.6); filter: blur(6px) brightness(0.6); transform: scale(1.03); }
    #hero .overlay { position:absolute; inset:0; background:rgba(6,14,9,0.25); z-index:1; }
    /* Content sits above the blurred background */
    .hero-inner { position:relative; z-index:2; width:100%; }
  /* Make the card fit content with 25px left/right margins on small screens */
  .login-card { width: calc(100% - 50px); max-width:420px; margin: 2rem auto; padding:2rem; border-radius:10px; background: rgba(110, 190, 116, 0.95); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
    .login-card .form-control{border-radius:6px}
    .login-brand { text-align:center; margin-bottom:1rem; }
  </style>

  <div class="hero-background">
    <img src="/assets/img/bg/bg-1.jpg" alt="" aria-hidden="true">
    <div class="overlay"></div>
  </div>

  <div class="container hero-inner">
    <div class="row justify-content-center align-items-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="feature-card login-card">
          <div class="login-brand">
            <h3 class="mb-1">Welcome back</h3>
            <p class="text-muted">Sign in to your account</p>
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

          @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="name@example.com" required value="{{ old('email') }}" autofocus>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-2 d-flex justify-content-start align-items-center">
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label ms-2" for="remember">Remember me</label>
              </div>
            </div>
            <div class="mb-3 text-center">
              <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Sign In</button>
            </div>

            <div class="text-center mt-3">
              <small class="text-muted">Don't have an account? <a href="{{ route('register') }}">Register here</a></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</section><!-- /Hero Section -->

@endsection