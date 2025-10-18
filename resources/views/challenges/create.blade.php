<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Create Challenge - Waste2Product</title>
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

  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="index-page">

  @include('partials.header')

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" style="background-image: url({{ asset('assets/img/page-title-bg.webp') }});">
      <div class="container position-relative">
        <h1>Create New Challenge</h1>
        <p>Set up a new recycling challenge for users</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="/">Home</a></li>
            <li><a href="{{ route('challenges.index') }}">Challenges</a></li>
            <li class="current">Create</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Create Challenge Section -->
    <section id="create-challenge" class="create-challenge section">
      <div class="container">

        <div class="row justify-content-center">
          <div class="col-lg-8">

            <div class="card shadow-sm">
              <div class="card-body p-4">
                
                <form action="{{ route('challenges.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf

                  <div class="mb-4">
                    <label for="title" class="form-label">Challenge Title *</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="mb-4">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Provide clear instructions on how to complete the challenge</small>
                  </div>

                  <div class="mb-4">
                    <label for="points" class="form-label">Points Reward *</label>
                    <input type="number" class="form-control @error('points') is-invalid @enderror" 
                           id="points" name="points" value="{{ old('points', 10) }}" min="1" max="100" required>
                    @error('points')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Points users will earn for completing this challenge</small>
                  </div>

                  <div class="mb-4">
                    <label for="image" class="form-label">Challenge Image (Optional)</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                           id="image" name="image" accept="image/*">
                    @error('image')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Upload an image to illustrate the challenge</small>
                  </div>

                  <div class="mb-4">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                      <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                      <i class="bi bi-plus-circle"></i> Create Challenge
                    </button>
                    <a href="{{ route('challenges.index') }}" class="btn btn-secondary">
                      <i class="bi bi-x-circle"></i> Cancel
                    </a>
                  </div>

                </form>

              </div>
            </div>

          </div>
        </div>

      </div>
    </section><!-- /Create Challenge Section -->

  </main>

  @include('partials.footer')

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
