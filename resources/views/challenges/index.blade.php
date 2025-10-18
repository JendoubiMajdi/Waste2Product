<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Daily Challenge - Waste2Product</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.webp') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.webp') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

  @include('partials.header')

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" style="background-image: url({{ asset('assets/img/page-title-bg.webp') }});">
      <div class="container position-relative">
        <h1>Daily Challenge</h1>
        <p>Complete today's recycling challenge and earn rewards!</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="/">Home</a></li>
            <li class="current">Challenges</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Challenges Section -->
    <section id="challenges" class="challenges section">
      <div class="container">

        @if($challenge)
          <div class="row gy-4">
            
            <!-- Challenge Card -->
            <div class="col-lg-8">
              <div class="card shadow-sm">
                <div class="card-body p-4">
                  
                  <!-- Challenge Header -->
                  <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                      <h2 class="card-title mb-2">{{ $challenge->title }}</h2>
                      <p class="text-muted mb-0">
                        <i class="bi bi-calendar-event"></i> Today's Challenge
                      </p>
                    </div>
                    <span class="badge bg-success fs-5 px-3 py-2">
                      <i class="bi bi-trophy"></i> {{ $challenge->points }} Points
                    </span>
                  </div>

                  <!-- Challenge Description -->
                  <div class="mb-4">
                    <h5 class="mb-3"><i class="bi bi-info-circle text-primary"></i> Description</h5>
                    <p class="lead">{{ $challenge->description }}</p>
                  </div>

                  <!-- Challenge Image -->
                  @if($challenge->image)
                    <div class="mb-4">
                      <img src="{{ $challenge->image }}" alt="{{ $challenge->title }}" class="img-fluid rounded shadow-sm">
                    </div>
                  @endif

                  <!-- Submission Status -->
                  @auth
                    @if($userSubmission)
                      <div class="alert alert-{{ $userSubmission->status === 'approved' ? 'success' : ($userSubmission->status === 'rejected' ? 'danger' : 'warning') }} d-flex align-items-center" role="alert">
                        <i class="bi bi-{{ $userSubmission->status === 'approved' ? 'check-circle' : ($userSubmission->status === 'rejected' ? 'x-circle' : 'clock') }} fs-4 me-3"></i>
                        <div>
                          <strong>
                            @if($userSubmission->status === 'approved')
                              Challenge Completed! 🎉
                            @elseif($userSubmission->status === 'rejected')
                              Submission Rejected
                            @else
                              Submission Under Review
                            @endif
                          </strong>
                          <p class="mb-0 mt-1">
                            @if($userSubmission->status === 'approved')
                              Congratulations! You earned {{ $challenge->points }} points.
                            @elseif($userSubmission->status === 'rejected')
                              Your submission didn't meet the requirements. Try again tomorrow!
                            @else
                              Your proof is being reviewed. Check back soon!
                            @endif
                          </p>
                        </div>
                      </div>

                      <!-- Show submitted proof -->
                      @if($userSubmission->proof_image)
                        <div class="mt-3">
                          <h6>Your Submission:</h6>
                          <img src="data:image/jpeg;base64,{{ $userSubmission->proof_image }}" alt="Submission proof" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                        </div>
                      @endif
                    @else
                      <!-- Submit Challenge Form -->
                      <div class="card bg-light">
                        <div class="card-body">
                          <h5 class="card-title mb-3"><i class="bi bi-upload text-success"></i> Submit Your Proof</h5>
                          <form action="{{ route('challenges.submit', $challenge->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                              <label for="proof_image" class="form-label">Upload Photo Evidence</label>
                              <input type="file" class="form-control @error('proof_image') is-invalid @enderror" 
                                     id="proof_image" name="proof_image" accept="image/*" required>
                              @error('proof_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                              <small class="form-text text-muted">Upload a clear photo showing you completed the challenge</small>
                            </div>
                            
                            <div class="mb-3">
                              <label for="description" class="form-label">Additional Notes (Optional)</label>
                              <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="3" 
                                        placeholder="Tell us about your experience..."></textarea>
                              @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                              @enderror
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100">
                              <i class="bi bi-send"></i> Submit Challenge
                            </button>
                          </form>
                        </div>
                      </div>
                    @endif
                  @else
                    <div class="alert alert-info" role="alert">
                      <i class="bi bi-info-circle"></i> Please <a href="{{ route('login') }}" class="alert-link">login</a> to participate in challenges.
                    </div>
                  @endauth

                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
              
              <!-- How It Works -->
              <div class="card shadow-sm mb-4">
                <div class="card-body">
                  <h5 class="card-title mb-3"><i class="bi bi-lightbulb text-warning"></i> How It Works</h5>
                  <ol class="ps-3">
                    <li class="mb-2">Complete today's challenge</li>
                    <li class="mb-2">Upload photo proof</li>
                    <li class="mb-2">Wait for admin approval</li>
                    <li class="mb-0">Earn points & rewards!</li>
                  </ol>
                </div>
              </div>

              <!-- Challenge Info -->
              <div class="card shadow-sm">
                <div class="card-body">
                  <h5 class="card-title mb-3"><i class="bi bi-clock-history text-primary"></i> Challenge Info</h5>
                  <ul class="list-unstyled">
                    <li class="mb-2">
                      <strong>Duration:</strong> 24 hours
                    </li>
                    <li class="mb-2">
                      <strong>Difficulty:</strong> 
                      <span class="badge bg-info">Medium</span>
                    </li>
                    <li class="mb-2">
                      <strong>Reward:</strong> {{ $challenge->points }} points
                    </li>
                    <li class="mb-0">
                      <strong>Category:</strong> Recycling
                    </li>
                  </ul>
                </div>
              </div>

            </div>

          </div>
        @else
          <!-- No Challenge Available -->
          <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
              <div class="card shadow-sm">
                <div class="card-body py-5">
                  <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                  <h3 class="mt-4">No Challenge Available</h3>
                  <p class="lead text-muted">Check back tomorrow for a new challenge!</p>
                  <a href="/" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> Back to Home
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endif

      </div>
    </section><!-- /Challenges Section -->

  </main>

  @include('partials.footer')

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
