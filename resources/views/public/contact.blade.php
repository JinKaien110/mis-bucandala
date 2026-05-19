@extends('layouts.public', ['currentRoute' => 'public.contact'])

@section('content')
<div class="container py-4">
  <!-- Page Header -->
  <div class="text-center mb-5">
    <h1 class="page-title mb-2">Contact Us</h1>
    <p class="opacity-75 mx-auto" style="max-width: 700px;">We're here to help! Reach out to us through any of the methods below. Our dedicated team is ready to assist you.</p>
  </div>

  <!-- Emergency Contacts -->
  <div class="mb-5">
    <h4 class="mb-3 fw-semibold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Emergency Contacts</h4>
    <p class="mb-4 opacity-75">In case of emergencies, please contact the appropriate service immediately.</p>
    <div class="row g-3">
      <div class="col-md-6 col-lg-3">
        <a href="tel:911" class="emergency-card police text-decoration-none p-4 rounded-4 d-flex align-items-center gap-3">
          <div class="emergency-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-shield-alt"></i>
          </div>
          <div>
            <h6 class="mb-1 fw-semibold">Police</h6>
            <p class="mb-0 small opacity-75">911 / (046) 123-4567</p>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="tel:911" class="emergency-card fire text-decoration-none p-4 rounded-4 d-flex align-items-center gap-3">
          <div class="emergency-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-fire"></i>
          </div>
          <div>
            <h6 class="mb-1 fw-semibold">Fire Department</h6>
            <p class="mb-0 small opacity-75">911 / (046) 234-5678</p>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="tel:911" class="emergency-card medical text-decoration-none p-4 rounded-4 d-flex align-items-center gap-3">
          <div class="emergency-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-ambulance"></i>
          </div>
          <div>
            <h6 class="mb-1 fw-semibold">Medical</h6>
            <p class="mb-0 small opacity-75">911 / (046) 345-6789</p>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="tel:911" class="emergency-card disaster text-decoration-none p-4 rounded-4 d-flex align-items-center gap-3">
          <div class="emergency-icon rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div>
            <h6 class="mb-1 fw-semibold">Disaster</h6>
            <p class="mb-0 small opacity-75">(046) 456-7890</p>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-7">
      <!-- Contact Form -->
      <div class="glass p-4 mb-4">
        <h5 class="mb-3"><i class="bi bi-envelope me-2"></i>Send us a Message</h5>
        <p class="opacity-75 small mb-4">Have a question or feedback? Fill out the form below.</p>
        
        <form id="contactForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="fullName" placeholder="Your full name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="email" placeholder="your@email.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" class="form-control" id="phone" placeholder="09xxxxxxxxx">
            </div>
            <div class="col-md-6">
              <label class="form-label">Subject <span class="text-danger">*</span></label>
              <select class="form-select" id="subject" required>
                <option value="">Select a subject</option>
                <option value="general">General Inquiry</option>
                <option value="document">Document Request</option>
                <option value="complaint">File a Complaint</option>
                <option value="suggestion">Suggestion</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Message <span class="text-danger">*</span></label>
              <textarea class="form-control" id="message" rows="4" placeholder="Your message here..." required></textarea>
            </div>
            
            <div class="col-12">
              <label class="form-label d-block">Rating (Optional)</label>
              <div class="rating-stars" id="ratingStars">
                <i class="bi bi-star-fill rating-star" data-rating="1"></i>
                <i class="bi bi-star-fill rating-star" data-rating="2"></i>
                <i class="bi bi-star-fill rating-star" data-rating="3"></i>
                <i class="bi bi-star-fill rating-star" data-rating="4"></i>
                <i class="bi bi-star-fill rating-star" data-rating="5"></i>
              </div>
            </div>
            
            <div class="col-12">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-send me-2"></i>Send Message
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Office Hours -->
      <div class="glass p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Office Hours</h6>
          <div id="statusBadgeLeft" class="status-badge">
            <span class="status-dot"></span>
            <span id="statusTextLeft">Open</span>
          </div>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span>Monday - Friday</span>
          <span class="fw-medium">8:00 AM - 5:00 PM</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span>Saturday</span>
          <span class="fw-medium">8:00 AM - 12:00 PM</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <span>Sunday</span>
          <span class="fw-medium opacity-75">Closed</span>
        </div>
      </div>

      <!-- Follow Us -->
      <div class="glass p-4">
        <h6 class="mb-3">Follow Us</h6>
        <div class="d-flex gap-3">
          <a href="https://facebook.com/bucandala1" target="_blank" class="social-link" title="Facebook">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="https://twitter.com/bucandala1" target="_blank" class="social-link" title="Twitter">
            <i class="fab fa-x-twitter"></i>
          </a>
          <a href="https://instagram.com/bucandala1" target="_blank" class="social-link" title="Instagram">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="https://youtube.com/bucandala1" target="_blank" class="social-link" title="YouTube">
            <i class="fab fa-youtube"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-5">
      <!-- Visit Us -->
      <div class="glass p-4 mb-4">
        <h5 class="mb-4"><i class="bi bi-geo-alt me-2"></i>Visit Us</h5>
        
        <div class="contact-card">
          <div class="contact-icon">
            <i class="bi bi-building"></i>
          </div>
          <div>
            <h6 class="mb-1">Barangay Hall</h6>
            <p class="mb-0 small opacity-75">Barangay Bucandala 1, City of Imus, Cavite</p>
          </div>
        </div>

        <div class="contact-card">
          <div class="contact-icon">
            <i class="bi bi-telephone"></i>
          </div>
          <div>
            <h6 class="mb-1">Phone</h6>
            <p class="mb-0 small opacity-75">(046) 123-4567</p>
          </div>
        </div>

        <div class="contact-card">
          <div class="contact-icon">
            <i class="bi bi-envelope"></i>
          </div>
          <div>
            <h6 class="mb-1">Email</h6>
            <p class="mb-0 small opacity-75">info@bucandala1.gov.ph</p>
          </div>
        </div>
      </div>

      <!-- Map -->
      <div class="glass p-4">
        <h5 class="mb-4"><i class="bi bi-map me-2"></i>Map</h5>
        <div class="map-container mb-3 rounded-4 overflow-hidden" style="height: 250px;">
          <iframe src="https://www.google.com/maps/embed?pb=!4v1775935144663!6m8!1m7!1s5ELMSmwBEJaGsFpnyHf-OQ!2m2!1d14.40819065824641!2d120.9306048943416!3f296.27!4f0.9500000000000028!5f0.7820865974627469" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <a href="https://www.google.com/maps/dir//Barangay+Bucandala+1,+Imus,+Cavite" target="_blank" class="btn btn-outline-primary w-100">
          <i class="bi bi-sign-turn-right me-2"></i>Get Directions
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .emergency-card.police { background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%); color: #1f2937; }
  .emergency-card.fire { background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #fff; }
  .emergency-card.medical { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; }
  .emergency-card.disaster { background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); color: #fff; }
  .emergency-card:hover { transform: translateY(-4px); }
  .emergency-icon { background: rgba(255,255,255,0.2); }
  
  .contact-card {
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 12px;
    border-radius: 12px;
    background: rgba(255,215,0,0.08);
    border: 1px solid rgba(255,215,0,0.15);
    transition: all 0.3s;
  }
  .contact-card:hover { background: rgba(255,215,0,0.15); transform: translateY(-2px); }
  .contact-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,215,0,0.2);
    flex-shrink: 0;
  }
  .contact-icon i { font-size: 18px; }
  
  .rating-stars { display: flex; gap: 8px; }
  .rating-star { font-size: 24px; cursor: pointer; color: #d1d5db; transition: all 0.2s; }
  .rating-star:hover, .rating-star.active { color: #FFD700; transform: scale(1.1); }
  
  .status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500;
  }
  .status-badge.status-open { background: rgba(34,197,94,0.2); color: #16a34a; }
  .status-badge.status-closed { background: rgba(239,68,68,0.2); color: #dc2626; }
  .status-dot { width: 6px; height: 6px; border-radius: 50%; }
  .status-open .status-dot { background: #16a34a; }
  .status-closed .status-dot { background: #dc2626; }
  
  .social-link {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,215,0,0.15);
    border: 1px solid rgba(255,215,0,0.3);
    color: #1f2937;
    transition: all 0.3s;
    text-decoration: none;
  }
  .social-link:hover { background: #FFD700; transform: translateY(-3px); }
  
  .map-container { border-radius: 16px; overflow: hidden; background: rgba(255,215,0,0.1); }
  
  .btn-primary { background: #FFD700; border: none; color: #1f2937; font-weight: 600; }
  .btn-primary:hover { background: #FFC107; color: #1f2937; }
  .btn-outline-primary { border-color: #FFD700; color: #1f2937; }
  .btn-outline-primary:hover { background: #FFD700; color: #1f2937; }
  
  .whatsapp-btn {
    position: fixed; bottom: 30px; right: 30px;
    width: 55px; height: 55px; border-radius: 50%;
    background: #25D366;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: white;
    box-shadow: 0 6px 20px rgba(37,211,102,0.4);
    z-index: 1000; transition: all 0.3s; text-decoration: none;
  }
  .whatsapp-btn:hover { transform: translateY(-4px) scale(1.1); color: white; }
  
  .modal-content {
    background: #fff; border: none; border-radius: 20px;
  }
  .modal-icon {
    width: 60px; height: 60px; border-radius: 50%;
    background: rgba(255,215,0,0.2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
  }
  .modal-icon i { font-size: 32px; color: #16a34a; }
  .modal-title { color: #1f2937; font-weight: 600; }
  .text-muted { color: #6b7280 !important; }
</style>
@endpush

@push('scripts')
<!-- WhatsApp Button -->
<a href="https://wa.me/639123456789" class="whatsapp-btn" target="_blank" title="Chat with us on WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="modal-icon">
          <i class="bi bi-check-lg"></i>
        </div>
        <h4 class="modal-title mb-3">Message Sent Successfully!</h4>
        <p class="text-muted mb-4">Thank you for contacting us. We have received your message and will respond within 24-48 hours.</p>
        <button type="button" class="btn btn-primary-custom w-100" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
  function updateStatus() {
    const now = new Date();
    const day = now.getDay();
    const hour = now.getHours();
    const minute = now.getMinutes();
    const currentTime = hour + minute / 60;
    
    const statusBadgeLeft = document.getElementById('statusBadgeLeft');
    const statusTextLeft = document.getElementById('statusTextLeft');
    
    let isOpen = false;
    
    if (day >= 1 && day <= 5) {
      isOpen = currentTime >= 8 && currentTime < 17;
    } else if (day === 6) {
      isOpen = currentTime >= 8 && currentTime < 12;
    }
    
    if (statusBadgeLeft && statusTextLeft) {
      if (isOpen) {
        statusBadgeLeft.className = 'status-badge status-open';
        statusTextLeft.textContent = 'Open Now';
      } else {
        statusBadgeLeft.className = 'status-badge status-closed';
        statusTextLeft.textContent = 'Closed';
      }
    }
  }
  updateStatus();
  setInterval(updateStatus, 60000);

  let selectedRating = 0;
  document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
      selectedRating = this.dataset.rating;
      document.querySelectorAll('.rating-star').forEach((s, index) => {
        s.classList.toggle('active', index < selectedRating);
      });
    });
  });

  document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value.trim();
    
    let isValid = true;
    
    if (!fullName) {
      document.getElementById('fullName').classList.add('is-invalid');
      isValid = false;
    } else {
      document.getElementById('fullName').classList.remove('is-invalid');
    }
    
    if (!email || !email.includes('@')) {
      document.getElementById('email').classList.add('is-invalid');
      isValid = false;
    } else {
      document.getElementById('email').classList.remove('is-invalid');
    }
    
    if (!subject) {
      document.getElementById('subject').classList.add('is-invalid');
      isValid = false;
    } else {
      document.getElementById('subject').classList.remove('is-invalid');
    }
    
    if (!message) {
      document.getElementById('message').classList.add('is-invalid');
      isValid = false;
    } else {
      document.getElementById('message').classList.remove('is-invalid');
    }
    
    if (isValid) {
      const modal = new bootstrap.Modal(document.getElementById('successModal'));
      modal.show();
      this.reset();
      document.querySelectorAll('.rating-star').forEach(s => s.classList.remove('active'));
      selectedRating = 0;
    }
  });
</script>
@endpush