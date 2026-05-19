@extends('layouts.public', ['currentRoute' => 'public.services'])

@push('styles')
<style>
.service-card {
  padding: 32px;
  transition: all 0.3s;
  cursor: pointer;
  display: block;
  text-decoration: none;
  color: inherit;
}

.service-card:hover {
  transform: translateY(-8px);
  background: rgba(255, 255, 255, 0.18);
}

.service-icon {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  margin-bottom: 20px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.service-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 8px;
}

.service-desc {
  font-size: 0.9rem;
  opacity: 0.75;
  margin-bottom: 16px;
}

.service-link {
  font-weight: 600;
  color: var(--mis-yellow);
  display: flex;
  align-items: center;
  gap: 8px;
}

.onsite-section, .online-section {
  margin-bottom: 50px;
}

.section-badge {
  display: inline-block;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 24px;
}

.onsite-badge {
  background: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
}

.online-badge {
  background: rgba(40, 167, 69, 0.2);
  color: #86efac;
}

.step-card {
  padding: 20px;
  display: flex;
  gap: 16px;
  margin-bottom: 12px;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  background: var(--mis-blue);
  flex-shrink: 0;
}

.glass-light {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 16px;
}
</style>
@endpush

@section('content')
<div class="page-header text-center mb-5">
  <h1 class="page-title">Our Services</h1>
  <p class="opacity-75">Choose between onsite or online services</p>
</div>

<!-- Onsite Services -->
<div class="onsite-section">
  <div class="section-badge onsite-badge">
    <i class="bi bi-building me-2"></i>Walk-in Services (Visit Barangay Hall)
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <a href="{{ route('public.services.blotter') }}" class="service-card glass">
        <div class="service-icon" style="color: #fca5a5;">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <h4 class="service-title">File a Blotter</h4>
        <p class="service-desc">Report incidents like theft, disputes, noise complaints, and other disturbances.</p>
        <span class="service-link">Learn more <i class="bi bi-arrow-right"></i></span>
      </a>
    </div>
    <div class="col-md-4">
      <div class="service-card glass">
        <div class="service-icon" style="color: #93c5fd;">
          <i class="bi bi-gavel"></i>
        </div>
        <h4 class="service-title">Open a Case</h4>
        <p class="service-desc">Start formal legal cases for disputes, land issues, or family matters.</p>
        <span class="service-link">Learn more <i class="bi bi-arrow-right"></i></span>
      </div>
    </div>
    <div class="col-md-4">
      <a href="{{ route('public.services.documents') }}" class="service-card glass">
        <div class="service-icon" style="color: #FEEE91;">
          <i class="bi bi-file-earmark-text"></i>
        </div>
        <h4 class="service-title">Document Request</h4>
        <p class="service-desc">Request clearance, certificates, and permits. Pay online or at the hall.</p>
        <span class="service-link">Learn more <i class="bi bi-arrow-right"></i></span>
      </a>
    </div>
  </div>

  <!-- How to Access -->
  <div class="glass p-4 mt-4" style="background: rgba(16, 85, 201, 0.15); border: 1px solid rgba(255,255,255,0.25);">
    <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-signpost me-2"></i>How to Access Onsite Services</h5>
    <div class="row g-3">
      <div class="col-md-3">
        <div class="step-card glass-light" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25);">
          <div class="step-number">1</div>
          <div>
            <strong style="color: #ffffff;">Visit the Hall</strong>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">Go to Barangay Bucandala 1 Hall</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="step-card glass-light" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25);">
          <div class="step-number">2</div>
          <div>
            <strong style="color: #ffffff;">Get a Number</strong>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">Take a queue number at the entrance</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="step-card glass-light" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25);">
          <div class="step-number">3</div>
          <div>
            <strong style="color: #ffffff;">Submit Requirements</strong>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">Provide necessary documents</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="step-card glass-light" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25);">
          <div class="step-number">4</div>
          <div>
            <strong style="color: #ffffff;">Pay & Receive</strong>
            <p class="small mb-0" style="color: rgba(255,255,255,0.85);">Pay fees and receive your document</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Online Services -->
<div class="online-section">
  <div class="section-badge online-badge">
    <i class="bi bi-laptop me-2"></i>Online Services (From Home)
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <a href="{{ route('public.residents.register') }}" class="service-card glass">
        <div class="service-icon" style="color: #86efac;">
          <i class="bi bi-person-plus"></i>
        </div>
        <h4 class="service-title">Register as Resident</h4>
        <p class="service-desc">Sign up as a new resident with OTP verification and ID upload.</p>
        <span class="service-link">Register now <i class="bi bi-arrow-right"></i></span>
      </a>
    </div>
    <div class="col-md-4">
      <div class="service-card glass">
        <div class="service-icon" style="color: #d8b4fe;">
          <i class="bi bi-paw-print"></i>
        </div>
        <h4 class="service-title">Pet Registration</h4>
        <p class="service-desc">Register your pets, upload vaccination records, and get QR codes.</p>
        <span class="service-link">Coming soon <i class="bi bi-arrow-right"></i></span>
      </div>
    </div>
    <div class="col-md-4">
      <div class="service-card glass">
        <div class="service-icon" style="color: #fbbf24;">
          <i class="bi bi-house"></i>
        </div>
        <h4 class="service-title">Household Register</h4>
        <p class="service-desc">Create and manage household profiles and add members.</p>
        <span class="service-link">Coming soon <i class="bi bi-arrow-right"></i></span>
      </div>
    </div>
  </div>
</div>

<!-- Info Cards -->
<div class="row g-4 mt-4">
  <div class="col-md-6">
    <div class="glass p-4">
      <h5 class="mb-3"><i class="bi bi-clock me-2"></i>Office Hours</h5>
      <div class="d-flex justify-content-between mb-2">
        <span>Monday - Friday</span>
        <span>8:00 AM - 5:00 PM</span>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span>Saturday</span>
        <span>8:00 AM - 12:00 PM</span>
      </div>
      <div class="d-flex justify-content-between">
        <span>Sunday</span>
        <span>Closed</span>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="glass p-4">
      <h5 class="mb-3"><i class="bi bi-telephone me-2"></i>Contact for Appointments</h5>
      <p class="mb-2"><i class="bi bi-phone me-2"></i>(046) 123-4567</p>
      <p class="mb-0"><i class="bi bi-envelope me-2"></i>info@bucandala1.gov.ph</p>
    </div>
  </div>
</div>
@endsection