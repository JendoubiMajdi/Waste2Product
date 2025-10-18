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
      <div class="credits">
        Building a sustainable future through waste transformation
      </div>
    </div>

  </footer>