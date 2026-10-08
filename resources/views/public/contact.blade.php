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
        <div class="col-lg-7">
          <!-- Contact Form -->
          <div class="glass p-4 mb-4">
            <h5 class="mb-3" style="color: #ffffff;"><i class="bi bi-envelope me-2"></i>Send us a Message</h5>
            <p class="small mb-4" style="color: rgba(255,255,255,0.8);">Have a question or feedback? Fill out the form below.</p>
            <form id="homeContactForm">
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
                  <button type="submit" class="btn btn-glass px-4">
                    <i class="bi bi-send me-2"></i>Send Message
                  </button>
                </div>
              </div>
            </form>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-clock me-2"></i>Office Hours</h6>
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
                  <span class="opacity-75">Closed</span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="glass p-4" style="height: 100%;">
                <h6 class="mb-3"><i class="bi bi-geo-alt me-2"></i>Location</h6>
                <p class="small mb-2 opacity-75">Barangay Hall, Bucandala 1<br>City of Imus, Cavite</p>
                <a href="https://www.google.com/maps/dir//Barangay+Bucandala+1,+Imus,+Cavite" target="_blank" class="btn btn-glass btn-sm">
                  <i class="bi bi-sign-turn-right me-1"></i>Get Directions
                </a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <!-- Contact Info -->
          <div class="glass p-4 mb-4">
            <h5 class="mb-4"><i class="bi bi-building me-2"></i>Barangay Hall</h5>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-geo-alt"></i>
              </div>
              <div>
                <h6 class="mb-1">Address</h6>
                <p class="small mb-0 opacity-75">Barangay Bucandala 1, City of Imus, Cavite</p>
              </div>
            </div>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-telephone"></i>
              </div>
              <div>
                <h6 class="mb-1">Phone</h6>
                <p class="small mb-0 opacity-75">(046) 123-4567</p>
              </div>
            </div>
            <div class="contact-card" style="padding: 16px; display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; border-radius: 12px; background: rgba(255,215,0,0.08); border: 1px solid rgba(255,215,0,0.15);">
              <div style="width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255,215,0,0.2);">
                <i class="bi bi-envelope"></i>
              </div>
              <div>
                <h6 class="mb-1">Email</h6>
                <p class="small mb-0 opacity-75">info@bucandala1.gov.ph</p>
              </div>
            </div>
          </div>

          <!-- Map -->
          <div class="glass p-4" style="height: 50%;">
            <h5 class="mb-4"><i class="bi bi-map me-2"></i>Map</h5>
            <div class="rounded-4 overflow-hidden" style="height: 80%; background: rgba(255,215,0,0.1);">
              <iframe style="height: 100%;" src="https://www.google.com/maps/embed?pb=!4v1775935144663!6m8!1m7!1s5ELMSmwBEJaGsFpnyHf-OQ!2m2!1d14.40819065824641!2d120.9306048943416!3f296.27!4f0.9500000000000028!5f0.7820865974627469" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
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
  

      :root {
      --mis-blue: #1055C9;
      --mis-blue-light: #3b82f6;
      --mis-blue-dark: #0d47a1;
      --mis-yellow: #FFD700;
      --mis-yellow-dark: #FFC107;
      --mis-green: #28a745;
      --mis-gray: #f8f9fa;
      --glass-bg: rgba(255, 255, 255, 0.15);
      --glass-border: rgba(255, 255, 255, 0.25);
      --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html {
      overflow-x: hidden;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, #0d47a1 0%, #1055C9 50%, #1976D2 100%);
      min-height: 100vh;
      overflow-x: hidden;
      color: #ffffff;
      position: relative;
      z-index: 2;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      z-index: 0;
      pointer-events: none;
    }

    a { color: var(--mis-yellow); }
    a:hover { color: #ffffff; }

    h1, h2, h3, h4, h5, h6 { color: #ffffff; }
    p, span, li, td, th { color: rgba(255,255,255,0.9); }
    .text-white { color: #ffffff !important; }
    .text-muted { color: rgba(255,255,255,0.7) !important; }

    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
      pointer-events: none;
    }

    .floating-icons {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
      overflow: hidden;
    }

    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(100px);
      animation: float 25s ease-in-out infinite;
    }

    .orb-1 {
      width: 600px;
      height: 600px;
      background: rgba(255, 215, 0, 0.08);
      top: -200px;
      left: -200px;
    }

    .orb-2 {
      width: 500px;
      height: 500px;
      background: rgba(255, 255, 255, 0.05);
      bottom: -100px;
      right: -100px;
      animation-delay: -8s;
    }

    .orb-3 {
      width: 400px;
      height: 400px;
      background: rgba(255, 193, 7, 0.06);
      top: 40%;
      left: 50%;
      animation-delay: -16s;
    }

    .orb-4 {
      width: 300px;
      height: 300px;
      background: rgba(255, 228, 77, 0.08);
      top: 10%;
      right: 20%;
      animation-delay: -12s;
    }

    @keyframes float {
      0%, 100% { transform: translate(0, 0) scale(1) rotate(0deg); }
      25% { transform: translate(30px, -30px) scale(1.05) rotate(5deg); }
      50% { transform: translate(-20px, 20px) scale(0.95) rotate(-5deg); }
      75% { transform: translate(20px, 30px) scale(1.02) rotate(3deg); }
    }

    /* Floating Icons Animation */
    .floating-icons {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
      overflow: hidden;
    }

    .floating-icon {
      position: absolute;
      font-size: 2rem;
      opacity: 0.15;
      animation: drift 20s ease-in-out infinite;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    }

    .floating-icon.house { color: #FFD700; }
    .floating-icon.paw { color: #FFC107; }
    .floating-icon.document { color: #1055C9; }
    .floating-icon.bell { color: #FFD700; }
    .floating-icon.badge { color: #FFC107; }
    .floating-icon.users { color: #1055C9; }
    .floating-icon.calendar { color: #FFD700; }
    .floating-icon.child { color: #FFD700; }

    @keyframes drift {
      0%, 100% { 
        transform: translate(0, 0) rotate(0deg) scale(1);
        opacity: 0;
      }
      10% { opacity: 0.15; }
      50% { 
        transform: translate(var(--tx), var(--ty)) rotate(var(--rot)) scale(1.1);
        opacity: 0.12;
      }
      90% { opacity: 0.15; }
    }

    .floating-icon:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; --tx: 100px; --ty: 50px; --rot: 15deg; }
    .floating-icon:nth-child(2) { top: 20%; left: 85%; animation-delay: -3s; --tx: -80px; --ty: 80px; --rot: -10deg; }
    .floating-icon:nth-child(3) { top: 50%; left: 3%; animation-delay: -5s; --tx: 120px; --ty: -60px; --rot: 20deg; }
    .floating-icon:nth-child(4) { top: 70%; left: 90%; animation-delay: -8s; --tx: -100px; --ty: -40px; --rot: -15deg; }
    .floating-icon:nth-child(5) { top: 35%; left: 50%; animation-delay: -12s; --tx: 60px; --ty: 100px; --rot: 10deg; }
    .floating-icon:nth-child(6) { top: 80%; left: 20%; animation-delay: -2s; --tx: 80px; --ty: -80px; --rot: -20deg; }
    .floating-icon:nth-child(7) { top: 15%; left: 35%; animation-delay: -6s; --tx: -60px; --ty: 40px; --rot: 25deg; }
    .floating-icon:nth-child(8) { top: 60%; left: 75%; animation-delay: -10s; --tx: -90px; --ty: 60px; --rot: -5deg; }
    .floating-icon:nth-child(9) { top: 45%; left: 15%; animation-delay: -15s; --tx: 50px; --ty: -90px; --rot: 12deg; }
    .floating-icon:nth-child(10) { top: 25%; left: 65%; animation-delay: -18s; --tx: -70px; --ty: -70px; --rot: -18deg; }

    /* Glassmorphism Base */
    .glass {
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 20px;
      box-shadow: var(--glass-shadow);
    }

    .glass-light {
      background: rgba(12, 8, 8, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 16px;
    }

    /* Navigation */
    .navbar-glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
      z-index: 1000;
    }

    .navbar-glass.fixed-top {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .navbar-glass .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 8px 14px;
      border-radius: 10px;
      display: flex;
      align-items: center;
    }

    .navbar-glass .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }

    .navbar-glass .nav-link.active {
      background: linear-gradient(90deg, rgba(255, 215, 0, 0.5) 0%, rgba(255, 215, 0, 0.25) 100%);
      color: #ffffff !important;
    }

    .navbar-glass .user-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }

    .navbar-glass .dropdown-menu-glass {
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 16px;
      padding: 8px;
      box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
    }

    .navbar-glass .dropdown-item-glass {
      color: rgba(255, 255, 255, 0.9);
      padding: 12px 16px;
      border-radius: 10px;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none !important;
    }

    .navbar-glass .dropdown-item-glass:hover {
      background: linear-gradient(90deg, rgba(255, 215, 0, 0.25) 0%, rgba(255, 215, 0, 0.1) 100%);
      color: #ffffff;
      transform: translateX(4px);
    }

    .navbar-glass .dropdown-icon {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 215, 0, 0.15);
      border-radius: 8px;
      font-size: 14px;
    }

    .navbar-glass .btn-primary-glass {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #ffffff;
      padding: 8px 16px;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
    }

    .navbar-glass .btn-primary-glass:hover {
      transform: translateY(-2px);
      color: #ffffff;
    }

    .navbar-glass .dropdown-divider {
      border-color: rgba(255, 215, 0, 0.2);
      margin: 4px 0;
    }

    .navbar-glass.fixed-top {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      transition: all 0.3s ease;
      position: relative;
      padding: 8px 14px;
      border-radius: 10px;
    }

    .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }

    .dropdown-menu {
      background: rgba(255, 255, 255, 0.98);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 12px;
      padding: 8px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
      color: rgba(255, 255, 255, 0.9);
      padding: 10px 16px;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff;
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      padding: 120px 0 80px;
      text-align: center;
    }

    .hero-logo {
      width: 120px;
      height: 120px;
      border-radius: 24px;
      object-fit: cover;
      border: 4px solid rgba(255, 215, 0, 0.5);
      box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3);
      margin-bottom: 24px;
      animation: pulse-glow 3s ease-in-out infinite;
    }

    @keyframes pulse-glow {
      0%, 100% { box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3), 0 0 0 0 rgba(255, 215, 0, 0.4); }
      50% { box-shadow: 0 12px 40px rgba(255, 215, 0, 0.3), 0 0 0 15px rgba(255, 215, 0, 0); }
    }

    .hero-title {
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 8px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.2);
      color: #ffffff;
    }

    .hero-subtitle {
      font-size: 1.3rem;
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 8px;
    }
    
    .hero-description {
      max-width: 600px;
      margin: 16px auto 32px;
      font-size: 1rem;
      color: rgba(255, 255, 255, 0.8);
      line-height: 1.6;
    }

    .hero-location {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 20px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 50px;
      font-size: 0.95rem;
      color: #ffffff;
    }

    /* Quick Actions */
    .quick-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-top: 40px;
    }

    .action-card {
      padding: 24px 16px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      background: rgba(19, 11, 11, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 16px;
      color: #ffffff;
    }

    .action-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 215, 0, 0.2);
      border-color: rgba(255, 215, 0, 0.5);
      box-shadow: 0 20px 40px rgba(255, 215, 0, 0.2);
    }

    .action-icon {
      width: 64px;
      height: 64px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 28px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .action-title {
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 4px;
      color: #ffffff;
    }

    .action-desc {
      font-size: 0.8rem;
      color: rgba(11, 1, 1, 0.7);
    }

    /* Section Styles */
    .section {
      padding: 60px 0;
      position: relative;
      z-index: 1;
    }

    .section-title {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      color: #ffffff;
    }

    .section-subtitle {
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 32px;
    }

    /* Announcements */
    .announcement-card {
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 12px;
      color: #ffffff;
    }

    .announcement-card:hover {
      background: rgba(255, 215, 0, 0.2);
      transform: translateX(8px);
      border-color: rgba(255, 215, 0, 0.5);
    }

    .announcement-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .badge-important {
      background: rgba(220, 38, 38, 0.25);
      color: #fca5a5;
    }

    .badge-event {
      background: rgba(255, 215, 0, 0.25);
      color: #fef08a;
    }

    .badge-general {
      background: rgba(34, 197, 94, 0.25);
      color: #86efac;
    }

    .badge-news {
      background: rgba(59, 130, 246, 0.25);
      color: #93c5fd;
    }

    /* Event Card */
    .event-card {
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      gap: 12px;
      align-items: flex-start;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.25);
      border-radius: 12px;
      color: #ffffff;
    }

    .event-card:hover {
      background: rgba(255, 215, 0, 0.2);
      border-color: rgba(255, 215, 0, 0.5);
    }

    .event-date-box {
      min-width: 50px;
      text-align: center;
      padding: 8px;
      border-radius: 10px;
      background: rgba(255, 215, 0, 0.2);
    }

    .event-month {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: #FFD700;
      font-weight: 600;
    }

    .event-day {
      font-size: 1.2rem;
      font-weight: 700;
      color: #ffffff;
    }

    .event-day {
      font-size: 1.3rem;
      font-weight: 700;
      color: #ffffff;
    }

    .badge-important {
      background: rgba(239, 68, 68, 0.2);
      color: #dc2626;
    }

    .badge-event {
      background: rgba(255, 215, 0, 0.2);
      color: #b45309;
    }

    .badge-general {
      background: rgba(40, 167, 69, 0.2);
      color: #059669;
    }

    /* Services Grid */
    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .service-card {
      padding: 28px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      color: #ffffff;
    }

    .service-card:hover {
      transform: translateY(-5px);
      background: rgba(255, 215, 0, 0.15);
    }

    .service-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
      background: rgba(255, 215, 0, 0.2);
    }

    .service-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }

    .service-desc {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.5;
    }

    /* Events */
    .event-card {
      padding: 20px;
      margin-bottom: 16px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
      transition: all 0.3s ease;
    }

    .event-card:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .event-date-box {
      min-width: 60px;
      text-align: center;
      padding: 12px 8px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .event-month {
      font-size: 0.7rem;
      text-transform: uppercase;
      color: var(--mis-yellow);
    }

    .event-day {
      font-size: 1.5rem;
      font-weight: 700;
    }

    .event-details {
      flex: 1;
    }

    .event-title {
      font-weight: 600;
      margin-bottom: 6px;
      color: #ffffff;
    }

    .event-meta {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .event-meta i {
      margin-right: 4px;
    }

    /* Statistics */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
    }

    .stat-card {
      padding: 24px;
      text-align: center;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      color: #FFC107;
    }

    .stat-label {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.75);
      margin-top: 4px;
    }

    /* Contact Section */
    .contact-card {
      padding: 24px;
      display: flex;
      align-items: flex-start;
      gap: 16px;
      background: rgba(14, 5, 5, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      color: #110808;
    }

    .contact-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.3);
      flex-shrink: 0;
    }

    .contact-info h5 {
      font-weight: 600;
      margin-bottom: 4px;
    }

    .contact-info p {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      margin: 0;
    }

    /* Footer */
    .footer-glass {
      background: rgba(255, 215, 0, 0.1);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      padding: 40px 0 20px;
      color: #ffffff;
    }

    .footer-links h5 {
      font-weight: 600;
      margin-bottom: 16px;
      color: #ffffff;
    }

    .footer-links ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-links li {
      margin-bottom: 8px;
    }

    .footer-links a {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: color 0.3s;
      font-size: 0.9rem;
    }

    .footer-links a:hover {
      color: #FFD700;
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 215, 0, 0.2);
      margin-top: 30px;
      padding-top: 20px;
      text-align: center;
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* Buttons */
    .btn-glass {
      background: rgba(255, 215, 0, 0.2);
      border: 1px solid rgba(255, 215, 0, 0.35);
      color: #ffffff;
      padding: 12px 28px;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-glass:hover {
      background: rgba(255, 215, 0, 0.35);
      color: #ffffff;
      transform: translateY(-2px);
    }

    .btn-primary-glass {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border: none;
      color: #ffffff;
      padding: 14px 32px;
      border-radius: 14px;
      font-weight: 600;
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
      transition: all 0.3s ease;
    }

    .btn-primary-glass:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(16, 85, 201, 0.5);
      color: #fff;
    }

    .btn-primary-glass.btn-sm {
      padding: 8px 16px;
      font-size: 0.875rem;
      border-radius: 10px;
    }

    .btn-outline-light.btn-sm {
      padding: 8px 16px;
      font-size: 0.875rem;
      border-radius: 10px;
      border-width: 1.5px;
    }

    .btn-outline-light.btn-sm:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: #fff;
    }

    /* Swiper */
    .swiper {
      padding-bottom: 40px !important;
    }

    .swiper-pagination-bullet {
      background: rgba(255, 255, 255, 0.5) !important;
    }

    .swiper-pagination-bullet-active {
      background: #fff !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2rem;
      }
      
      .hero-subtitle {
        font-size: 1rem;
      }

      .section {
        padding: 40px 0;
      }

      .section-title {
        font-size: 1.4rem;
      }

      .quick-actions {
        grid-template-columns: repeat(2, 1fr);
      }

      .action-card {
        padding: 16px 12px;
      }

      .action-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
      }

      .navbar-nav {
        gap: 8px;
      }
      .navbar-nav .btn {
        width: 100%;
        text-align: center;
        margin-top: 5px;
      }
      .btn-primary-glass.btn-sm, .btn-outline-light.btn-sm {
        padding: 10px 20px;
      }
    }

    /* Stagger Animation */
    .fade-up {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeUp 0.6s ease forwards;
    }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }

    /* FAQ Toggle */
    .faq-item {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
      overflow: hidden;
    }

    .faq-question {
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .faq-question i {
      transition: transform 0.3s;
    }

    .faq-item.open .faq-question i {
      transform: rotate(180deg);
    }

    .faq-item.open .faq-answer {
      display: block !important;
      max-height: 500px !important;
      padding: 0 20px 20px !important;
    }

    html {
      scroll-behavior: smooth;
    }

    /* Form Elements */
    .form-label {
      color: #ffffff;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .form-control, .form-select {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      border-radius: 12px;
      padding: 12px 16px;
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      background: rgba(255, 255, 255, 0.18);
      border-color: #FFD700;
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.15);
      color: #ffffff;
      outline: none;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .form-control.is-invalid {
      border-color: #dc2626;
    }

    .invalid-feedback {
      color: #fca5a5;
      font-size: 0.85rem;
      margin-top: 4px;
    }

    .form-select option {
      background: #1055C9;
      color: #ffffff;
    }

    /* Buttons */
    .btn {
      border-radius: 12px;
      font-weight: 600;
      padding: 12px 24px;
      transition: all 0.3s ease;
    }

    .btn-warning {
      background: #FFD700;
      border: none;
      color: #1f2937;
    }

    .btn-warning:hover {
      background: #FFC107;
      color: #1f2937;
      transform: translateY(-2px);
    }

    .btn-outline-primary {
      background: transparent;
      border: 2px solid #FFD700;
      color: #FFD700;
    }

    .btn-outline-primary:hover {
      background: #FFD700;
      color: #1f2937;
    }

    .btn-primary {
      background: #FFD700;
      border: none;
      color: #1f2937;
    }

    .btn-primary:hover {
      background: #FFC107;
      color: #1f2937;
    }

    /* Tables */
    .table {
      color: #ffffff;
    }

    .table thead th {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff;
      font-weight: 600;
      border-bottom: 2px solid rgba(255, 215, 0, 0.3);
      padding: 14px 16px;
    }

    .table tbody td {
      background: rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 14px 16px;
      color: rgba(255, 255, 255, 0.9);
    }

    .table tbody tr:hover td {
      background: rgba(255, 255, 255, 0.1);
    }

    /* Cards */
    .card {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 16px;
      color: #ffffff;
    }

    .card-header {
      background: rgba(255, 215, 0, 0.1);
      border-bottom: 1px solid rgba(255, 215, 0, 0.2);
      color: #ffffff;
      font-weight: 600;
      padding: 16px 20px;
    }

    .card-body {
      color: #ffffff;
      padding: 20px;
    }

    /* Badges */
    .badge {
      padding: 6px 12px;
      border-radius: 50px;
      font-weight: 500;
      font-size: 0.8rem;
    }

    .badge.bg-primary {
      background: #FFD700 !important;
      color: #1f2937;
    }

    .badge.bg-secondary {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }

    /* Pagination */
    .pagination {
      gap: 8px;
    }

    .page-link {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      border-radius: 8px;
      padding: 10px 16px;
      transition: all 0.3s ease;
    }

    .page-link:hover {
      background: rgba(255, 215, 0, 0.2);
      border-color: #FFD700;
      color: #ffffff;
    }

    .page-item.active .page-link {
      background: #FFD700;
      border-color: #FFD700;
      color: #1f2937;
    }

    .page-item.disabled .page-link {
      background: rgba(255, 255, 255, 0.05);
      color: rgba(255, 255, 255, 0.4);
    }

    /* Modal */
    .modal-content {
      background: rgba(16, 85, 201, 0.95);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 20px;
      color: #ffffff;
    }

    .modal-header {
      border-bottom: 1px solid rgba(255, 215, 0, 0.2);
      color: #ffffff;
    }

    .modal-footer {
      border-top: 1px solid rgba(255, 215, 0, 0.2);
    }

    .btn-close {
      filter: invert(1);
    }

    /* Alerts */
    .alert {
      border-radius: 12px;
      padding: 16px 20px;
      border: none;
    }

    .alert-success {
      background: rgba(34, 197, 94, 0.2);
      color: #86efac;
      border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .alert-danger {
      background: rgba(220, 38, 38, 0.2);
      color: #fca5a5;
      border: 1px solid rgba(220, 38, 38, 0.3);
    }

    .alert-warning {
      background: rgba(255, 215, 0, 0.2);
      color: #fef08a;
      border: 1px solid rgba(255, 215, 0, 0.3);
    }

    .alert-info {
      background: rgba(59, 130, 246, 0.2);
      color: #93c5fd;
      border: 1px solid rgba(59, 130, 246, 0.3);
    }

    /* Dropdown */
    .dropdown-menu {
      background: rgba(16, 85, 201, 0.98);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .dropdown-item {
      color: #ffffff;
      padding: 10px 16px;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: rgba(255, 215, 0, 0.2);
      color: #FFD700;
    }

    /* Official Cards */
    .official-card {
      padding: 24px;
      text-align: center;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 16px;
      transition: all 0.3s ease;
    }

    .official-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 215, 0, 0.4);
    }

    .official-card .captain-badge {
      display: inline-block;
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      color: #1f2937;
      padding: 6px 16px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 16px;
    }

    .official-card .official-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid rgba(255, 215, 0, 0.3);
      margin-bottom: 16px;
    }

    .official-card .official-name {
      font-size: 1.1rem;
      font-weight: 600;
      color: #ffffff;
      margin-bottom: 4px;
    }

    .official-card .official-position {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 8px;
    }

    .official-card .official-contact {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.6);
    }

    /* Search Box */
    .search-box {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 12px;
      min-width: 250px;
    }

    .search-box:focus {
      outline: none;
      border-color: #FFD700;
      background: rgba(255, 255, 255, 0.18);
    }

    .search-box::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    /* Filter Button */
    .filter-btn {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .filter-btn:hover, .filter-btn.active {
      background: #FFD700;
      color: #1f2937;
      border-color: #FFD700;
    }

    /* FAQ Category Buttons */
    .category-btn {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 215, 0, 0.25);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .category-btn:hover, .category-btn.active {
      background: #FFD700;
      color: #1f2937;
      border-color: #FFD700;
    }

    /* News Section Styles */
    .filter-tabs {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .filter-tab {
      padding: 12px 20px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
      border: 2px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
    }
    .filter-tab:hover {
      background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
      transform: translateY(-2px);
    }

    .view-toggle {
      display: flex;
      gap: 10px;
    }
    .view-btn {
      padding: 12px 20px;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
      border: 2px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }
    .view-btn:hover {
      background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
      transform: translateY(-2px);
    }
    .view-btn.active {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
    }

    .announcement-card {
      padding: 20px;
      margin-bottom: 16px;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }
    .announcement-card:hover {
      transform: translateY(-3px);
      background: rgba(255, 255, 255, 0.25);
    }
    .announcement-badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    .badge-important {
      background: rgba(239,68,68,0.2);
      color: #fca5a5;
    }
    .badge-event {
      background: rgba(59,130,246,0.2);
      color: #93c5fd;
    }
    .badge-news {
      background: rgba(168,85,247,0.2);
      color: #d8b4fe;
    }
    .badge-general {
      background: rgba(40,167,69,0.2);
      color: #86efac;
    }
    .announcement-date {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.75);
      margin-bottom: 8px;
    }
    .announcement-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 8px;
    }
    .announcement-excerpt {
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      line-height: 1.5;
    }

    .event-card {
      padding: 16px;
      margin-bottom: 12px;
      display: flex;
      gap: 14px;
      align-items: flex-start;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 12px;
    }
    .event-card:hover {
      transform: translateY(-3px);
      background: rgba(255, 255, 255, 0.25);
    }
    .event-date-box {
      min-width: 50px;
      text-align: center;
      padding: 8px;
      border-radius: 10px;
      background: rgba(255,255,255,0.1);
    }
    .event-month {
      font-size: 0.65rem;
      text-transform: uppercase;
      color: #FFD700;
    }
    .event-day {
      font-size: 1.3rem;
      font-weight: 700;
    }
    .event-details {
      flex: 1;
    }
    .event-title {
      font-weight: 600;
      margin-bottom: 6px;
    }
    .event-meta {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.75);
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }
    .event-status {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 10px;
      font-size: 0.65rem;
      font-weight: 600;
    }
    .status-upcoming {
      background: rgba(59,130,246,0.2);
      color: #93c5fd;
    }
    .status-ongoing {
      background: rgba(34,197,94,0.2);
      color: #4ade80;
    }
    .status-past {
      background: rgba(255,255,255,0.1);
      color: rgba(255,255,255,0.5);
    }

    .page-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .page-title {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: #ffffff;
    }

    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      margin: 30px 0;
      flex-wrap: wrap;
    }
    .pagination .page-link {
      background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%);
      border: 2px solid rgba(255, 215, 0, 0.5);
      color: #ffffff;
      padding: 14px 20px;
      border-radius: 14px;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.3s;
      min-width: 55px;
      text-align: center;
    }
    .pagination .page-link:hover {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
      transform: translateY(-4px);
      color: #ffffff;
      text-decoration: none;
    }
    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      border-color: #FFD700;
      color: #ffffff;
      font-weight: 700;
    }

    /* Officials Styles */
    .official-card {
      padding: 32px;
      text-align: center;
      transition: all 0.3s;
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 16px;
    }
    .official-card:hover {
      transform: translateY(-8px);
      background: rgba(255, 255, 255, 0.25);
    }
    .official-avatar {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid rgba(255, 255, 255, 0.3);
      margin-bottom: 16px;
    }
    .official-name {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 4px;
    }
    .official-position {
      font-size: 0.85rem;
      opacity: 0.75;
      margin-bottom: 8px;
    }
    .official-contact {
      font-size: 0.8rem;
      opacity: 0.6;
    }
    .captain-badge {
      background: linear-gradient(135deg, #FEEE91 0%, #f59e0b 100%);
      color: #ffffff;
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 700;
      display: inline-block;
      margin-bottom: 16px;
    }
    .search-box {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 25px;
      width: 100%;
      max-width: 300px;
    }
    .search-box:focus {
      outline: none;
      border-color: #FFD700;
      background: rgba(255, 255, 255, 0.25);
    }
    .search-box::placeholder { color: #6b7280; }
    .filter-btn {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 215, 0, 0.4);
      color: #ffffff;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 500;
      transition: all 0.3s;
      cursor: pointer;
    }
    .filter-btn:hover, .filter-btn.active {
      background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
      color: #ffffff;
      border-color: #FFD700;
    }
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