<footer class="footer-glass" style="background: rgba(255, 215, 0, 0.1); border-top: 1px solid rgba(255, 215, 0, 0.2); padding: 40px 0 20px; position: relative; z-index: 1;">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="50" height="50" class="rounded-circle" onerror="this.src='https://via.placeholder.com/40?text=BRGY'">
          <div>
            <h5 class="mb-0" style="color: #ffffff;">Barangay Bucandala 1</h5>
            <small style="color: rgba(255,255,255,0.7);">City of Imus, Cavite</small>
          </div>
        </div>
        <p class="opacity-75 small" style="color: rgba(255,255,255,0.7);">Serving our community since 1980. Committed to providing efficient services to all residents.</p>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5 style="color: #FFD700;">Quick Links</h5>
          <ul>
            <li><a href="{{ route('public.home') }}" style="color: rgba(255,255,255,0.75);">Home</a></li>
            <li><a href="{{ route('public.about') }}" style="color: rgba(255,255,255,0.75);">About</a></li>
            <li><a href="{{ route('public.services') }}" style="color: rgba(255,255,255,0.75);">Services</a></li>
            <li><a href="{{ route('public.officials') }}" style="color: rgba(255,255,255,0.75);">Officials</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5 style="color: #FFD700;">Services</h5>
          <ul>
            <li><a href="{{ route('public.residents.register') }}" style="color: rgba(255,255,255,0.75);">Register</a></li>
            <li><a href="{{ route('public.services.documents') }}" style="color: rgba(255,255,255,0.75);">Documents</a></li>
            <li><a href="{{ route('public.services.blotter') }}" style="color: rgba(255,255,255,0.75);">Blotter</a></li>
            <li><a href="{{ route('public.contact') }}" style="color: rgba(255,255,255,0.75);">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="col-lg-2 col-md-4">
        <div class="footer-links">
          <h5 style="color: #FFD700;">Support</h5>
          <ul>
            <li><a href="{{ route('public.faqs') }}" style="color: rgba(255,255,255,0.75);">FAQs</a></li>
            <li><a href="{{ route('public.data-privacy') }}" style="color: rgba(255,255,255,0.75);">Privacy</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-bottom" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 30px; padding-top: 20px; text-align: center; color: rgba(255,255,255,0.6);">
      <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved. Made with <i class="bi bi-heart-fill text-danger"></i> for our community.</p>
    </div>
  </div>
</footer>
