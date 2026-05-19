<footer class="footer-glass" style="background: rgba(255,255,255,0.95); margin-top: 60px;">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="50" height="50" class="rounded-circle" onerror="this.src='https://via.placeholder.com/40?text=BRGY'">
          <div>
            <h5 class="mb-0" style="color: #1f2937;">Barangay Bucandala 1</h5>
            <small style="color: #6b7280;">City of Imus, Cavite</small>
          </div>
        </div>
        <p class="small" style="color: #4b5563;">Serving our community since 1980. Committed to providing efficient and accessible services to all residents of Bucandala 1.</p>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5 style="color: #1f2937;">Quick Links</h5>
          <ul>
            <li><a href="{{ route('public.home') }}" style="color: #1055C9;">Home</a></li>
            <li><a href="{{ route('public.about') }}" style="color: #1055C9;">About</a></li>
            <li><a href="{{ route('public.services') }}" style="color: #1055C9;">Services</a></li>
            <li><a href="{{ route('public.officials') }}" style="color: #1055C9;">Officials</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5>Services</h5>
          <ul>
            <li><a href="{{ route('public.residents.register') }}">Register</a></li>
            <li><a href="{{ route('public.services.documents') }}">Documents</a></li>
            <li><a href="{{ route('public.services.blotter') }}">Blotter</a></li>
            <li><a href="{{ route('public.contact') }}">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5>Support</h5>
          <ul>
            <li><a href="{{ route('public.faqs') }}">FAQs</a></li>
            <li><a href="{{ route('public.data-privacy') }}">Privacy Policy</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved. Made with <i class="bi bi-heart-fill text-danger"></i> for our community.</p>
    </div>
  </div>
</footer>
