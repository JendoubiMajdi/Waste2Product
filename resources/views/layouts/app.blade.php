<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Passion Template')</title>
  <link href="/assets/img/favicon.png" rel="icon">
  <link href="/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="/assets/css/main.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1 0 auto;
    }
    #footer {
      flex-shrink: 0;
      margin-top: auto;
    }
  </style>
  @stack('head')
</head>
<body class="@yield('bodyClass', '')">
  @includeIf('partials.header')
  <main>
    @yield('content')
  </main>

  <footer id="footer" class="footer position-relative dark-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="{{ url('/') }}" class="logo d-flex align-items-center">
            <span class="sitename">Waste2Product</span>
          </a>
          <p>Transforming waste into valuable resources. We connect waste collectors and recyclers to create usable materials and products, building a sustainable circular economy for a greener future.</p>
          <div class="social-links d-flex mt-4">
            <a href="#"><i class="bi bi-twitter-x"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ url('/') }}#about">About Us</a></li>
            <li><a href="{{ url('/') }}#services">Services</a></li>
            <li><a href="{{ url('/') }}#contact">Contact</a></li>
            @guest
            <li><a href="{{ route('login') }}">Login</a></li>
            @endguest
          </ul>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Our Platform</h4>
          <ul>
            <li><a href="{{ route('collection_points.map') }}">Collection Points</a></li>
            <li><a href="{{ route('products.index') }}">Products</a></li>
            <li><a href="{{ route('wastes.index') }}">Waste Management</a></li>
            <li><a href="{{ route('challenges.index') }}">Challenges</a></li>
            @auth
            <li><a href="{{ route('home') }}">Dashboard</a></li>
            @endauth
          </ul>
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Contact Us</h4>
          <p>Esprit El Ghazela</p>
          <p>Ariana, Tunisia</p>
          <p class="mt-4"><strong>Phone:</strong> <span>+216 98765432</span></p>
          <p><strong>Email:</strong> <span>waste2product@waste2product.com</span></p>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Waste2Product</strong> <span>All Rights Reserved</span></p>
      <div class="credits">Building a sustainable future through waste transformation</div>
    </div>
  </footer>

  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <div id="preloader"><div></div><div></div><div></div><div></div></div>

</script>
  <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/vendor/php-email-form/validate.js"></script>
  <script src="/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="/assets/js/main.js"></script>
  @stack('scripts')
</body>
</html>
