@extends('layouts.app')

@section('title', 'Forgot Password')

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
            <h3 class="mb-1">Forgot your password?</h3>
            <p class="text-muted">Enter your email to get a reset link</p>
          </div>
          <form action="{{ route('password.email') }}" method="POST" class="php-email-form">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Send link</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</section><!-- /Hero Section -->

@endsection