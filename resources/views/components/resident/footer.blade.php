<footer class="footer-glass">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="50" height="50" class="rounded-circle" onerror="this.src='https://via./50?text=BRGY'">
            <div>
              <h5 class="mb-0">Barangay Bucandala 1</h5>
              <small class="text-white-50">City of Imus, Cavite</small>
            </div>
          </div>
          <p class="small text-white-50">Your partner in building a safer, more connected community. Serving the residents of Bucandala 1 with dedication and integrity.</p>
        </div>

      

        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Quick Links</h5>
            <ul>
              <li><a href="#home">Home</a></li>
              <li><a href="#news">News</a></li>
              <li><a href="#events">Events</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2 col-md-4">
          <div class="footer-links">
            <h5>Account</h5>
            <ul>
              <li><a href="#contact">Login</a></li>
              <li><a href="{{ route('public.residents.register') }}">Register</a></li>
              <li><a href="#faqs">Help</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-2">
          <div class="footer-links">
            <h5>Follow Us</h5>
            <div class="d-flex gap-3">
              <a href="#" class="fs-5"><i class="bi bi-facebook"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-instagram"></i></a>
              <a href="#" class="fs-5"><i class="bi bi-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved. | Developed with love for our community</p>
      </div>
    </div>
  </footer>