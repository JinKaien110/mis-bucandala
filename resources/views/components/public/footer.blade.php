<footer class="footer-glass" style="background: rgba(255,255,255,0.95); border-top: 1px solid rgba(255,215,0,0.2);">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="50" height="50" class="rounded-circle">
          <div>
            <h5 class="mb-0" style="color: #1f2937;">Barangay Bucandala 1</h5>
            <small style="color: #6b7280;">City of Imus, Cavite</small>
          </div>
        </div>
        <p class="opacity-75 small" style="color: #4b5563;">Serving our community since 1980. Committed to providing efficient services.</p>
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
          <h5 style="color: #1f2937;">Services</h5>
          <ul>
            <li><a href="{{ route('public.residents.register') }}" style="color: #1055C9;">Register</a></li>
            <li><a href="{{ route('public.services.documents') }}" style="color: #1055C9;">Documents</a></li>
            <li><a href="{{ route('public.services.blotter') }}" style="color: #1055C9;">Blotter</a></li>
            <li><a href="{{ route('public.contact') }}" style="color: #1055C9;">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5 style="color: #1f2937;">Support</h5>
          <ul>
            <li><a href="{{ route('public.faqs') }}" style="color: #1055C9;">FAQs</a></li>
            <li><a href="{{ route('public.data-privacy') }}" style="color: #1055C9;">Privacy</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-bottom" style="border-top: 1px solid rgba(255,215,0,0.2); margin-top: 20px; padding-top: 20px; text-align: center; color: #6b7280;">
      <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved.</p>
    </div>
  </div>
</footer>