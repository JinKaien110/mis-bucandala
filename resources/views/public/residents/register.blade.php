{{-- resources/views/public/residents/register.blade.php --}}
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Resident Registration</title>



  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --mis-blue: #1055C9;
      --mis-blue-light: #3b82f6;
      --mis-blue-dark: #0d47a1;
      --mis-yellow: #FEEE91;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 50%, #3b82f6 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      overflow-x: hidden;
    }
    
    /* Form control focus/error states */
    .form-control:focus {
      border-color: #dc2626 !important;
      box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25) !important;
    }
    .form-control:invalid:focus {
      border-color: #dc2626 !important;
    }
    
    /* Navbar styles matching home page */
    .navbar-glass {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
      z-index: 1000;
      transition: background 0.3s ease;
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
      text-decoration: none;
    }
    .navbar-glass .nav-link:hover {
      background: rgba(255, 215, 0, 0.15);
      color: #ffffff !important;
    }
    .navbar-glass .dropdown-menu {
      background: rgba(16, 85, 201, 0.98);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 215, 0, 0.3);
      border-radius: 12px;
      padding: 8px;
    }
    .navbar-glass .btn-outline-light {
      border-color: #ffffff;
      color: #ffffff;
      padding: 6px 14px;
      font-size: 0.875rem;
      border-radius: 10px;
      text-decoration: none;
    }
    .navbar-glass .btn-outline-light:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }
    .navbar-glass .btn-warning {
      background: #FFD700;
      color: #1f2937;
      border: none;
      padding: 6px 14px;
      font-size: 0.875rem;
      font-weight: 600;
      border-radius: 10px;
      text-decoration: none;
    }
    .navbar-glass .btn-warning:hover {
      background: #FFEB3B;
    }
    
    .wrap {
      padding-top: 100px;
      min-height: 100vh;
    }
    
    /* Animated floating orbs */
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
    
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      animation: floatReg 20s ease-in-out infinite;
    }
    
    .orb-1 {
      width: 400px;
      height: 400px;
      background: rgba(255, 255, 255, 0.15);
      top: -100px;
      left: -100px;
      animation-delay: 0s;
    }
    
    .orb-2 {
      width: 500px;
      height: 500px;
      background: rgba(254, 238, 145, 0.2);
      bottom: -150px;
      right: -150px;
      animation-delay: -7s;
    }
    
    .orb-3 {
      width: 300px;
      height: 300px;
      background: rgba(16, 85, 201, 0.25);
      top: 40%;
      left: 30%;
      animation-delay: -14s;
    }
    
    @keyframes floatReg {
      0%, 100% { 
        transform: translate(0, 0) scale(1) rotate(0deg); 
      }
      25% { 
        transform: translate(30px, -30px) scale(1.05) rotate(5deg); 
      }
      50% { 
        transform: translate(-20px, 20px) scale(0.95) rotate(-5deg); 
      }
      75% { 
        transform: translate(20px, 30px) scale(1.02) rotate(3deg); 
      }
    }
    
    .wrap { 
      max-width: 1400px; 
      margin: 28px auto; 
      padding: 0 16px;
      position: relative;
      z-index: 1;
    }
    
    /* Header brand section */
    .brand-header {
      text-align: center;
      margin-bottom: 24px;
    }
    
    .brand-logo {
      width: 80px;
      height: 80px;
      border-radius: 20px;
      object-fit: cover;
      border: 4px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      margin-bottom: 16px;
    }
    
    .brand-title {
      color: #fff;
      font-size: 1.8rem;
      font-weight: 700;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      margin-bottom: 4px;
    }
    
    .brand-subtitle {
      color: rgba(255, 255, 255, 0.85);
      font-size: 1rem;
    }
    
    .cardx { 
      background: rgba(255, 255, 255, 0.98);
      border: 1px solid rgba(255, 255, 255, 0.5);
      border-radius: 24px; 
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(20px);
      overflow: hidden;
    }
    
    .cardx-header { 
      padding: 28px 28px 0; 
      background: linear-gradient(135deg, rgba(16, 85, 201, 0.03) 0%, rgba(16, 85, 201, 0.08) 100%);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .cardx-body { padding: 28px; }
    
    .main-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 4px;
    }
    
    .sub { 
      color: #6b7280; 
      font-size: 14px; 
    }

    .stepper { 
      display: flex; 
      gap: 12px; 
      flex-wrap: wrap; 
      margin-top: 20px; 
    }
    .step { 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      padding: 14px 18px; 
      border: 2px solid #e5e7eb; 
      border-radius: 16px; 
      background: #fff;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      flex: 1;
      min-width: 180px;
    }
    .step:hover {
      border-color: #1055C9;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(16, 85, 201, 0.15);
    }
    .dot { 
      width: 36px; 
      height: 36px; 
      border-radius: 50%;
      display: flex; 
      align-items: center; 
      justify-content: center; 
      font-weight: 700; 
      font-size: 15px; 
      border: 2px solid #d1d5db; 
      background: #f9fafb; 
      color: #111;
      transition: all 0.3s ease;
      flex-shrink: 0;
    }
    .step.active { 
      border-color: #1055C9; 
      background: linear-gradient(135deg, rgba(16, 85, 201, 0.08) 0%, rgba(16, 85, 201, 0.15) 100%);
    }
    .step.active .dot { 
      border-color: #1055C9; 
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%); 
      color: #fff;
      box-shadow: 0 4px 15px rgba(16, 85, 201, 0.5);
    }
    .step.done { border-color: #10b981; background: #f0fdf4; }
    .step.done .dot { 
      border-color: #10b981; 
      background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
      color: #fff;
    }
    .step-title {
      font-weight: 600;
      color: #1f2937;
      font-size: 0.95rem;
    }
    .step-subtitle {
      font-size: 0.8rem;
      color: #9ca3af;
    }

    .pill { 
      font-size: 12px; 
      padding: 8px 16px; 
      border-radius: 999px; 
      border: 1px solid #e5e7eb; 
      background: #fff;
      font-weight: 500;
    }
    .pill.muted { 
      color: #6b7280; 
      background: #f9fafb;
    }

    .section-title { 
      font-weight: 700; 
      font-size: 1.15rem;
      color: #1f2937;
    }
    .hint { color: #9ca3af; font-size: 12px; margin-top: 4px; }
    .req { color: #ef4444; font-weight: 700; }

    .alertbox { display: none; white-space: pre-wrap; }
    
    .dropzone { 
      border: 2px dashed #cbd5e1; 
      border-radius: 16px; 
      padding: 20px; 
      background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
      transition: all 0.3s ease;
      text-align: center;
      min-height: 200px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .dropzone.has-file {
      padding: 10px;
    }
    .dropzone:hover { 
      border-color: #1055C9; 
      background: linear-gradient(135deg, #f0f5ff 0%, #e8efff 100%);
      transform: scale(1.01);
    }
    .dropzone.dragover { 
      border-color: #1055C9; 
      background: rgba(16, 85, 201, 0.1);
      transform: scale(1.02);
    }
    .dropzone-icon {
      font-size: 32px;
      color: #1055C9;
      margin-bottom: 8px;
    }
    .dropzone-text {
      color: #6b7280;
      font-size: 0.9rem;
    }
    .dropzone-hint {
      color: #9ca3af;
      font-size: 0.75rem;
      margin-top: 2px;
    }
    .dropzone-content {
      display: block;
    }
    .dropzone.has-file .dropzone-content {
      display: none;
    }
    .dropzone.has-file .sub {
      display: none;
    }
    .dropzone-preview .remove-file {
      display: inline-block;
      margin-top: 8px;
      font-size: 12px;
      color: #ef4444;
      cursor: pointer;
      text-decoration: underline;
    }
    .dropzone-preview {
      width: 100%;
      height: 100%;
    }
    .dropzone-preview img {
      width: 100%;
      height: 100px;
      max-height: 100px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #1055C9;
    }

    /* Enhanced Buttons */
    .btnx { 
      border-radius: 14px; 
      padding: 14px 28px; 
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .btnx::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    .btnx:hover::before {
      left: 100%;
    }
    .btnx:active {
      transform: scale(0.98);
    }
    .btnx-primary { 
      background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%); 
      border: none; 
      color: #fff;
      box-shadow: 0 8px 25px rgba(16, 85, 201, 0.4);
    }
    .btnx-primary:hover { 
      background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%); 
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(16, 85, 201, 0.5);
      color: #fff;
    }
    
    /* Form controls enhanced */
    .form-control, .form-select {
      border-radius: 14px;
      padding: 14px 18px;
      border: 2px solid #e5e7eb;
      transition: all 0.3s ease;
      font-size: 1rem;
    }
    .form-control:focus, .form-select:focus {
      border-color: #1055C9;
      box-shadow: 0 0 0 5px rgba(16, 85, 201, 0.1);
    }
    .form-label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
    }
    
    /* Section divider */
    .section-divider {
      display: flex;
      align-items: center;
      margin: 28px 0;
    }
    .section-divider::before,
    .section-divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }
    .section-divider span {
      padding: 0 16px;
      color: #9ca3af;
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .home-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      font-size: 0.9rem;
      padding: 8px 16px;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }
    .home-link:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>
  
  {{-- Navigation Bar --}}
  @include('components.navigation-bar', ['currentRoute' => 'register'])

<div class="wrap" style="padding-top: 100px;">

  {{-- Brand Header --}}
  <div class="brand-header">
    <img 
      src="{{ asset('storage/branding/barangay-logo.jpg') }}" 
      alt="Barangay Logo" 
      class="brand-logo"
      onerror="this.src='https://via.placeholder.com/80?text=BRGY'"
    >
    <h1 class="brand-title">Barangay Bucandala 1</h1>
    <p class="brand-subtitle">Resident Registration Portal</p>
  </div>

  {{-- Header + Stepper --}}
  <div class="cardx mb-3">
    <div class="cardx-header">
      <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
          <h3 class="mb-1">Resident Registration</h3>
          <div class="sub">Public form • ID + selfie with ID • Email OTP verification</div>
        </div>
        <span class="pill muted">Barangay Bucandala 1 • Imus, Cavite</span>
      </div>

      <div class="stepper">
        <div class="step" id="step0">
          <div class="dot">0</div>
          <div>
            <div class="step-title">Privacy Consent</div>
            <div class="step-subtitle">Agree to terms</div>
          </div>
        </div>

        <div class="step active" id="step1">
          <div class="dot">1</div>
          <div>
            <div class="step-title">Fill out form</div>
            <div class="step-subtitle">Enter details</div>
          </div>
        </div>

        <div class="step" id="step2">
          <div class="dot">2</div>
          <div>
            <div class="step-title">Send OTP</div>
            <div class="step-subtitle">Use your email</div>
          </div>
        </div>

        <div class="step" id="step3">
          <div class="dot">3</div>
          <div>
            <div class="step-title">Confirm OTP</div>
            <div class="step-subtitle">Unlock submit</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- STEP 0: Privacy Consent --}}
  <div class="cardx mb-3" id="step0Section">
    <div class="cardx-body">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="main-title">Data Privacy Agreement</div>
          <div class="sub">Please read and agree to the data privacy terms before proceeding.</div>
        </div>
        <span class="pill" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;">Step 0</span>
      </div>

      <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0;">
        <h5 class="fw-bold text-dark mb-3">
          <i class="bi bi-shield-check me-2 text-primary"></i>
          Privacy Notice for Residents
        </h5>
        
        <div class="small text-secondary mb-3">
          <p class="mb-2">Your personal information is collected, used, stored, and protected in accordance with the <strong>Data Privacy Act of 2012 (Republic Act No. 10173)</strong> of the Philippines.</p>
          
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <div class="p-3 rounded-3 bg-white border">
                <strong><i class="bi bi-collection me-1"></i> Collection</strong>
                <p class="mb-0 small text-muted">We collect personal details such as name, birth date, address, contact info, ID documents, and photos for official barangay records.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded-3 bg-white border">
                <strong><i class="bi bi-gear me-1"></i> Purpose</strong>
                <p class="mb-0 small text-muted">Your data is used for resident verification, service delivery, document issuance, emergency contacts, and compliance reporting.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded-3 bg-white border">
                <strong><i class="bi bi-lock me-1"></i> Protection</strong>
                <p class="mb-0 small text-muted">We implement security measures to protect your data from unauthorized access, disclosure, or modification.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded-3 bg-white border">
                <strong><i class="bi bi-person-badge me-1"></i> Your Rights</strong>
                <p class="mb-0 small text-muted">You have the right to access, correct, or request deletion of your personal data. Contact the barangay for such requests.</p>
              </div>
            </div>
          </div>
          
          <p class="mb-2">By clicking "I Agree" below, you acknowledge that you have read, understood, and agree to the processing of your personal information as described.</p>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="agreeTerms" style="width:20px;height:20px;">
          <label class="form-check-label fw-semibold" for="agreeTerms">
            I have read and agree to the <a href="{{ route('public.data-privacy') }}" target="_blank" class="text-decoration-underline">Data Privacy Policy</a> and Terms of Service
          </label>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="agreeProcess" style="width:20px;height:20px;">
          <label class="form-check-label fw-semibold" for="agreeProcess">
            I consent to the collection and processing of my personal information for resident registration purposes
          </label>
        </div>

        <button class="btn btnx btnx-primary w-100" type="button" id="btnAcceptTerms" disabled>
          <i class="bi bi-check-circle me-2"></i>I Agree & Continue to Registration
        </button>

        <div class="text-center mt-2">
          <small class="text-muted">By proceeding, you agree to our data privacy terms</small>
        </div>
      </div>
    </div>
  </div>

  {{-- FORM LAYOUT (no wrapper background) --}}
  <div id="formSection" style="display:none;">
    <div class="row g-4 align-items-stretch">
      {{-- LEFT: Registration Details --}}
      <div class="col-xl-7">
        <div class="cardx h-100">
          <div class="cardx-body h-100">
            <div class="section-title mb-3">
              <i class="bi bi-person-badge me-2"></i>Registration Details
            </div>

            <form id="regForm" enctype="multipart/form-data" class="d-flex flex-column flex-grow-1">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label mb-1">Full Name <span class="req">*</span></label>
                  <div class="row g-2">
                    <div class="col-4">
                      <input class="form-control" name="first_name" placeholder="First name" required />
                    </div>
                    <div class="col-4">
                      <input class="form-control" name="middle_name" placeholder="Middle name" />
                    </div>
                    <div class="col-4">
                      <input class="form-control" name="last_name" placeholder="Last name" required />
                    </div>
                  </div>
                </div>

                  <div class="col-6">
                    <label class="form-label mb-1">Birth date <span class="req">*</span></label>
                    <input class="form-control" name="birth_date" id="birth_date" type="date" required />
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1">Sex <span class="req">*</span></label>
                    <select class="form-select" name="sex" required>
                      <option value="">Select</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1">Civil status</label>
                    <select class="form-select" name="civil_status">
                      <option value="">Select (optional)</option>
                      <option value="single">Single</option>
                      <option value="married">Married</option>
                      <option value="widowed">Widowed</option>
                      <option value="separated">Separated</option>
                      <option value="divorced">Divorced</option>
                    </select>
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1">Occupation</label>
                    <select class="form-select" name="occupation">
                      <option value="">Select (optional)</option>
                      <option value="student">Student</option>
                      <option value="employed">Employed</option>
                      <option value="self_employed">Self-employed</option>
                      <option value="unemployed">Unemployed</option>
                      <option value="ofw">OFW</option>
                      <option value="retired">Retired</option>
                      <option value="other">Other</option>
                    </select>
                  </div>

                   <div class="col-md-6">
                     <label class="form-label mb-1">Phase <span class="req">*</span></label>
                    <input type="number" class="form-control" name="phase" required placeholder="e.g. 1" min="1" />
                   </div>

                   <div class="col-md-6">
                     <label class="form-label mb-1">Address Line <span class="req">*</span></label>
                     <input class="form-control" name="address_line" required placeholder="Block/Lot, Purok, Barangay..." />
                   </div>

                  <div class="col-md-6">
                    <label class="form-label mb-1">Contact no</label>
                    <div class="input-group">
                      <span class="input-group-text" style="background: white; border-color: #ced4da;">+63</span>
                      <input type="text" class="form-control" name="contact_no" id="contact_no" placeholder="917 123 4567" maxlength="12" required style="border-left: 0;" />
                    </div>
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1" id="email_label">Email <span class="req">*</span></label>
                    <input class="form-control" name="email" id="email" type="email" placeholder="Used for OTP" />
                    <div class="hint" id="email_hint">OTP will be sent to this email.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label mb-1">Password <span class="req">*</span></label>
                    <input class="form-control" name="password" id="password" type="password" placeholder="Create a password" style="max-width: 100%;" />
                    <div class="hint">Minimum 8 characters.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label mb-1">Confirm Password <span class="req">*</span></label>
                    <input class="form-control" name="password_confirmation" id="password_confirmation" type="password" placeholder="Confirm password" style="max-width: 100%;" />
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1">Valid ID Type <span class="req">*</span></label>
                    <select class="form-select" name="verification_type" required>
                      <option value="">Select</option>
                      <option value="philid">PhilSys ID</option>
                      <option value="drivers_license">Driver's License</option>
                      <option value="passport">Passport</option>
                      <option value="postal">Postal ID</option>
                      <option value="voters">Voter's ID</option>
                      <option value="umid">UMID</option>
                      <option value="tin">TIN ID</option>
                      <option value="pagibig">Pag-IBIG ID</option>
                      <option value="schoolid">School ID</option>
                    </select>
                  </div>

                  <div class="col-6">
                    <label class="form-label mb-1">ID Number</label>
                    <input class="form-control" name="verification_id" placeholder="Optional" />
                  </div>

                  <div class="col-12" id="guardianSection" style="display:none;">
                    <div class="p-3 rounded-3" style="background:#fef3c7;border:1px solid #fcd34d;">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fw-bold">Guardian Details (Minor)</div>
                        <span class="badge bg-warning text-dark">Required</span>
                      </div>
                      <div class="row g-2">
                        <div class="col-4">
                          <input class="form-control" name="guardian_full_name" id="guardian_full_name" placeholder="Full name" />
                        </div>
                        <div class="col-4">
                          <select class="form-select" name="guardian_relationship" id="guardian_relationship">
                            <option value="">Relationship</option>
                            <option value="mother">Mother</option>
                            <option value="father">Father</option>
                            <option value="guardian">Guardian</option>
                          </select>
                        </div>
                        <div class="col-4">
                          <input class="form-control" name="guardian_contact_no" id="guardian_contact_no" placeholder="Contact no" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- RIGHT: Upload Requirements + Email Verification --}}
        <div class="col-xl-5 d-flex flex-column">
          {{-- Upload Requirements --}}
          <div class="cardx mb-3">
            <div class="cardx-body">
              <div class="section-title mb-3">
                <i class="bi bi-cloud-upload me-2"></i>Upload Requirements
              </div>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="photo_path" class="dropzone" id="dz_photo" style="cursor:pointer;">
                    <input type="file" name="photo_path" id="photo_path" accept=".jpg,.jpeg,.png" style="display:none;" />
                    <div class="dropzone-content" id="dz_photo_content">
                      <i class="bi bi-person dropzone-icon"></i>
                      <div class="dropzone-text fw-semibold">Profile Photo</div>
                      <div class="dropzone-hint">Click or drag to upload</div>
                    </div>
                    <div class="sub mt-1">JPG / PNG (max 5MB)</div>
                  </label>
                </div>

                <div class="col-md-6">
                  <label for="id_image_path" class="dropzone" id="dz_id" style="cursor:pointer;">
                    <input type="file" name="id_image_path" id="id_image_path" accept=".jpg,.jpeg,.png,.pdf" required style="display:none;" />
                    <div class="dropzone-content" id="dz_id_content">
                      <i class="bi bi-person-badge dropzone-icon"></i>
                      <div class="dropzone-text fw-semibold">Government ID Photo</div>
                      <div class="dropzone-hint">Click or drag to upload <span class="req">*</span></div>
                    </div>
                    <div class="sub mt-1">JPG / PNG / PDF (max 5MB)</div>
                  </label>
                </div>

                <div class="col-12">
                  <label for="selfie_image_path" class="dropzone" id="dz_selfie" style="cursor:pointer;">
                    <input type="file" name="selfie_image_path" id="selfie_image_path" accept=".jpg,.jpeg,.png" required style="display:none;" />
                    <div class="dropzone-content" id="dz_selfie_content">
                      <i class="bi bi-camera dropzone-icon"></i>
                      <div class="dropzone-text fw-semibold">Selfie Holding ID</div>
                      <div class="dropzone-hint">Click or drag to upload <span class="req">*</span></div>
                    </div>
                    <div class="sub mt-1">JPG / PNG (max 5MB)</div>
                  </label>
                </div>
              </div>
            </div>
          </div>

          {{-- Email Verification (OTP) --}}
          <div class="cardx flex-grow-1" style="min-height: 200px;">
            <div class="cardx-body d-flex flex-column justify-content-between h-100" style="padding: 24px;">
              <div class="flex-grow-1">
                <div class="section-title mb-3" style="font-size: 1.1rem;">
                  <i class="bi bi-envelope-check me-2"></i>Email Verification (OTP)
                  <span style="font-size: 0.75rem; font-weight: 400; color: #9ca3af; margin-left: 6px;">(OTP expires in 5 minutes)</span>
                </div>

                <div id="message" class="alert alertbox mb-3" role="alert" style="display:none;font-size: 0.85rem;padding: 10px;"></div>

                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label mb-2" style="font-size: 0.9rem;">OTP Code <span class="req">*</span></label>
                    <input id="otp_code" class="form-control" inputmode="numeric" placeholder="Enter 6-digit OTP" style="padding: 12px 16px;font-size: 0.95rem;" />
                  </div>

                  <div class="col-6">
                    <button class="btn btn-outline-secondary btnx w-100" type="button" id="btnSendOtp" style="padding: 12px 16px;font-size: 0.9rem;">
                      <i class="bi bi-send me-2"></i>Send OTP
                    </button>
                  </div>
                  <div class="col-6">
                    <button class="btn btn-dark btnx btnx-primary w-100" type="button" id="btnVerifyOtp" disabled style="padding: 12px 16px;font-size: 0.9rem;">
                      Verify
                    </button>
                  </div>
                </div>
              </div>

              <div class="mt-3">
                <button class="btn btn-dark btnx btnx-primary w-100" type="button" id="btnSubmit" disabled style="padding: 14px 20px;font-size: 1rem;">
                  Submit Registration
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const msgBox = document.getElementById('message');

  let otpId = null;
  let verificationToken = null;

  const step0 = document.getElementById('step0');
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const step3 = document.getElementById('step3');

  const step0Section = document.getElementById('step0Section');
  const formSection = document.getElementById('formSection');
  const otpSection = document.getElementById('otpSection');

  const agreeTerms = document.getElementById('agreeTerms');
  const agreeProcess = document.getElementById('agreeProcess');
  const btnAcceptTerms = document.getElementById('btnAcceptTerms');

  // Contact number - format with spaces as user types
  const contactInput = document.getElementById('contact_no');
  if (contactInput) {
    contactInput.addEventListener('input', function(e) {
      let v = e.target.value.replace(/\D/g, '');
      if (v.length > 10) v = v.substring(0, 10);
      let result = '';
      if (v.length > 0) result += v.substring(0, 3);
      if (v.length > 3) result += ' ' + v.substring(3, 6);
      if (v.length > 6) result += ' ' + v.substring(6);
      e.target.value = result;
    });
  }

  // Show red border on invalid fields on blur
  document.querySelectorAll('.form-control[required]').forEach(function(input) {
    input.addEventListener('blur', function(e) {
      if (!e.target.value) {
        e.target.classList.add('is-invalid');
      }
    });
    input.addEventListener('input', function(e) {
      e.target.classList.remove('is-invalid');
    });
  });

  const btnSendOtp = document.getElementById('btnSendOtp');
  const btnVerifyOtp = document.getElementById('btnVerifyOtp');
  const btnSubmit = document.getElementById('btnSubmit');

  const dobInput = document.getElementById('birth_date');

  const emailInput = document.getElementById('email');
  const emailLabel = document.getElementById('email_label');
  const emailHint  = document.getElementById('email_hint');

  const guardianSection = document.getElementById('guardianSection');
  const guardianNameInput = document.getElementById('guardian_full_name');
  const guardianRelInput = document.getElementById('guardian_relationship');
  const guardianContactInput = document.getElementById('guardian_contact_no');

  let isMinor = false;

  // -------------------------
  // Helpers
  // -------------------------
  function setStep(state) {
    const reset = (el) => { el.classList.remove('active', 'done'); };
    [step0, step1, step2, step3].forEach(reset);

    if (state === 0) {
      step0.classList.add('active');
    }
    if (state === 1) {
      step0.classList.add('done');
      step1.classList.add('active');
      btnVerifyOtp.disabled = true;
      btnSubmit.disabled = true;
    }
    if (state === 2) {
      step0.classList.add('done');
      step1.classList.add('done');
      step2.classList.add('active');
      btnVerifyOtp.disabled = false;
      btnSubmit.disabled = true;
    }
    if (state === 3) {
      step0.classList.add('done');
      step1.classList.add('done');
      step2.classList.add('done');
      step3.classList.add('active');
      btnVerifyOtp.disabled = false;
      btnSubmit.disabled = false;
    }
  }

  function showMsg(text, type = 'info') {
    let alertClass = 'alert-success';
    if (type === 'error') alertClass = 'alert-danger';
    else if (type === 'info') alertClass = 'alert-secondary';
    else if (type === 'warning') alertClass = 'alert-warning';
    msgBox.className = 'alert ' + alertClass + ' mb-3';
    msgBox.style.display = 'block';
    msgBox.style.fontSize = '0.9rem';
    msgBox.style.padding = '10px 15px';

    let message = 'An unexpected error occurred. Please try again.';

    if (typeof text === 'string') {
      message = text;
    } else if (typeof text === 'object' && text !== null) {
      if (text.errors) {
        // Collect and display all validation errors from Laravel
        message = Object.values(text.errors).flat().join('\n');
      } else {
        message = text.message || text.error?.message || message;
      }
    }

    msgBox.textContent = message;
    msgBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function hideMsg() {
    msgBox.style.display = 'none';
    msgBox.textContent = '';
  }

  async function apiJson(url, method, payload) {
    const res = await fetch(url, {
      method,
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
      },
      body: payload ? JSON.stringify(payload) : null,
    });
    const data = await res.json().catch(() => ({}));
    return { res, data };
  }

  async function apiForm(url, method, formData) {
    const res = await fetch(url, {
      method,
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
      },
      body: formData,
    });
    const data = await res.json().catch(() => ({}));
    return { res, data };
  }

  function calcAge(dateStr) {
    console.log('calcAge input:', dateStr, 'type:', typeof dateStr);
    
    if (!dateStr) return null;
    
    // HTML date input always returns YYYY-MM-DD format
    let dob;
    if (dateStr.includes('/')) {
      // MM/DD/YYYY format
      const parts = dateStr.split('/');
      if (parts.length === 3) {
        const month = parseInt(parts[0], 10);
        const day = parseInt(parts[1], 10);
        const year = parseInt(parts[2], 10);
        dob = new Date(year, month - 1, day);
      } else {
        return null;
      }
    } else {
      // YYYY-MM-DD format
      dob = new Date(dateStr + 'T00:00:00');
    }
    console.log('dob:', dob, 'valid:', !isNaN(dob.getTime()));
    if (isNaN(dob.getTime())) return null;

    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    console.log('calculated age:', age);
    return age;
  }

  function getOtpEmail() {
    return (emailInput.value || '').trim();
  }

  function setGuardianRequired(required) {
    if (guardianNameInput) guardianNameInput.required = required;
    if (guardianRelInput) guardianRelInput.required = required;
    if (guardianContactInput) guardianContactInput.required = required;
  }

  function resetOtpState() {
    otpId = null;
    verificationToken = null;
    document.getElementById('otp_code').value = '';
    btnVerifyOtp.disabled = true;
    btnSubmit.disabled = true;
    setStep(1);
    refreshSendOtpEnabled();
  }

  function updateOtpPreview() {
    // Email is retrieved from the form input directly
  }

  function onDobChange() {
    console.log('dobInput value:', dobInput.value);
    const age = calcAge(dobInput.value);
    console.log('calculated age:', age);
    const minorNow = (age !== null && age < 18);
    console.log('is minor:', minorNow);

    if (minorNow !== isMinor) {
      isMinor = minorNow;

      guardianSection.style.display = isMinor ? 'block' : 'none';
      setGuardianRequired(isMinor);

      if (isMinor) {
        emailLabel.innerHTML = 'Guardian Email <span class="req">*</span>';
        emailHint.textContent = 'For minors, enter the guardian email here. OTP will be sent to this email.';
        emailInput.placeholder = 'Guardian email (used for OTP)';
      } else {
        emailLabel.innerHTML = 'Email <span class="req">*</span>';
        emailHint.textContent = 'OTP will be sent to this email address.';
        emailInput.placeholder = 'Used for OTP';
      }

      resetOtpState();
    }

    updateOtpPreview();
  }

  // -------------------------
  // OTP resend cooldown
  // -------------------------
  let otpCooldownUntil = 0;

  function setCooldown(seconds) {
    otpCooldownUntil = Date.now() + (seconds * 1000);
    updateCooldownUI();
  }

  function updateCooldownUI() {
    const remainMs = otpCooldownUntil - Date.now();

    if (remainMs <= 0) {
      btnSendOtp.disabled = !getOtpEmail();
      btnSendOtp.textContent = 'Send OTP';
      return;
    }

    const remain = Math.ceil(remainMs / 1000);
    btnSendOtp.disabled = true;
    btnSendOtp.textContent = `Send OTP (${remain}s)`;
    setTimeout(updateCooldownUI, 250);
  }

  function refreshSendOtpEnabled() {
    if (Date.now() < otpCooldownUntil) return;
    btnSendOtp.disabled = !getOtpEmail();
  }

  function bytes(n) {
  if (!n && n !== 0) return '';
  const units = ['B','KB','MB','GB'];
  let i = 0;
  let x = n;
  while (x >= 1024 && i < units.length - 1) { x /= 1024; i++; }
  return `${x.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
}

function previewFile(inputEl, dzId) {
  const file = inputEl.files && inputEl.files[0];
  const dz = document.getElementById(dzId);
  const contentId = dzId + '_content';
  const content = document.getElementById(contentId);
  const subText = dz ? dz.querySelector('.sub') : null;
  
  if (!dz) return;

  if (!file) {
    dz.classList.remove('has-file');
    return;
  }

  dz.classList.add('has-file');
  
  // Remove existing preview if any
  const existingPreview = dz.querySelector('.dropzone-preview');
  if (existingPreview) existingPreview.remove();

  // Create preview container
  const preview = document.createElement('div');
  preview.className = 'dropzone-preview';
  
  if (file.type === 'application/pdf') {
    preview.innerHTML = `
      <i class="bi bi-file-pdf" style="font-size:48px;color:#ef4444;"></i>
      <div class="filemeta">${file.name} • ${bytes(file.size)} • PDF selected</div>
      <span class="remove-file">Remove</span>
    `;
  } else {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.alt = 'Preview';
    preview.appendChild(img);
    preview.innerHTML += `
      <div class="filemeta">${file.name} • ${bytes(file.size)}</div>
      <span class="remove-file">Remove</span>
    `;
  }

  dz.appendChild(preview);

  // Remove file handler
  preview.querySelector('.remove-file').addEventListener('click', function(e) {
    e.stopPropagation();
    inputEl.value = '';
    dz.classList.remove('has-file');
    preview.remove();
  });
}

function setupDropzone(dzId, inputId) {
  const dz = document.getElementById(dzId);
  const input = document.getElementById(inputId);

  if (!dz || !input) return;

  dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dragover'); });
  dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
  dz.addEventListener('drop', (e) => {
    e.preventDefault();
    dz.classList.remove('dragover');
    if (!e.dataTransfer.files?.length) return;

    input.files = e.dataTransfer.files;
    input.dispatchEvent(new Event('change'));
  });
}


  // -------------------------
  // Init listeners
  // -------------------------
  setStep(0);
  onDobChange();

  // Step 0: Privacy consent checkboxes
  function updateAcceptTermsBtn() {
    btnAcceptTerms.disabled = !(agreeTerms.checked && agreeProcess.checked);
  }

  agreeTerms.addEventListener('change', updateAcceptTermsBtn);
  agreeProcess.addEventListener('change', updateAcceptTermsBtn);

  btnAcceptTerms.addEventListener('click', () => {
    if (!agreeTerms.checked || !agreeProcess.checked) return;
    
    console.log('Agree clicked, showing form and setting step 1');
    step0Section.style.display = 'none';
    formSection.style.display = 'block';
    setStep(1);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Enable drag&drop zones
setupDropzone('dz_photo', 'photo_path');
setupDropzone('dz_id', 'id_image_path');
setupDropzone('dz_selfie', 'selfie_image_path');

// Preview on file select
document.getElementById('photo_path').addEventListener('change', (e) => {
  previewFile(e.target, 'dz_photo');
});

document.getElementById('id_image_path').addEventListener('change', (e) => {
  previewFile(e.target, 'dz_id');
});

document.getElementById('selfie_image_path').addEventListener('change', (e) => {
  previewFile(e.target, 'dz_selfie');
});


  dobInput.addEventListener('change', onDobChange);
  dobInput.addEventListener('input', onDobChange);

  emailInput.addEventListener('input', () => {
    updateOtpPreview();
    resetOtpState();
  });

  // -------------------------
  // Send OTP
  // -------------------------
  btnSendOtp.addEventListener('click', async () => {
    hideMsg();

    const email = getOtpEmail();
    if (!email) {
      return showMsg(
        isMinor
          ? 'Minor detected. Please enter Guardian Email in the Email field.'
          : 'Please enter your email first.',
        'error'
      );
    }

    if (Date.now() < otpCooldownUntil) {
      const remain = Math.ceil((otpCooldownUntil - Date.now()) / 1000);
      return showMsg(`Please wait ${remain}s before requesting another OTP.`, 'error');
    }

    btnSendOtp.disabled = true;
    btnSendOtp.textContent = 'Sending...';

    const { res, data } = await apiJson('{{ url("/api/v1/public/otp/send") }}', 'POST', {
      purpose: 'resident_registration',
      email
    });

    if (!res.ok) {
      if (res.status === 429 && data.retry_after_seconds) {
        setCooldown(Number(data.retry_after_seconds));
        showMsg(`Please wait ${data.retry_after_seconds}s before requesting another OTP.`, 'error');
        return;
      }

      btnSendOtp.textContent = 'Send OTP';
      refreshSendOtpEnabled();
      return showMsg(data, 'error');
    }

    otpId = data.otp_id;
    setCooldown(Number(data.retry_after_seconds ?? 60));

    showMsg('OTP sent. Check your email.', 'success');
    setStep(2);
  });

  // -------------------------
  // Verify OTP
  // -------------------------
  btnVerifyOtp.addEventListener('click', async () => {
    hideMsg();

    const otp = document.getElementById('otp_code').value.trim();
    if (!otpId) return showMsg('Send OTP first.', 'error');
    if (!otp) return showMsg('Enter OTP.', 'error');

    btnVerifyOtp.disabled = true;
    btnVerifyOtp.textContent = 'Verifying...';

    const { res, data } = await apiJson('{{ url("/api/v1/public/otp/verify") }}', 'POST', {
      otp_id: otpId,
      otp
    });

    btnVerifyOtp.textContent = 'Confirm OTP';

    if (!res.ok) {
      btnVerifyOtp.disabled = false;
      console.log('OTP verify error:', res, data);
      let errorMsg = data.message || 'Verification failed. Please try again.';
      return showMsg(errorMsg, 'error');
    }

    verificationToken = data.verification_token;

    showMsg('OTP verified. You can now submit registration.', 'success');
    setStep(3);
  });

  // -------------------------
  // Submit Registration
  // -------------------------
  btnSubmit.addEventListener('click', async () => {
    hideMsg();

    if (!verificationToken) return showMsg('Confirm OTP first.', 'error');

    // Validate password match
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    if (password.value !== confirmPassword.value) {
      return showMsg('Passwords do not match.', 'error');
    }

    const photoPathEl = document.getElementById('photo_path');
    const idImagePathEl = document.getElementById('id_image_path');
    const selfiePathEl = document.getElementById('selfie_image_path');
    
    console.log('photo_path files:', photoPathEl.files.length);
    console.log('id_image_path files:', idImagePathEl.files.length);
    console.log('selfie_image_path files:', selfiePathEl.files.length);
    console.log('isMinor:', isMinor);

    if (!idImagePathEl.files.length) return showMsg('Please upload your Government ID.', 'error');
    if (!selfiePathEl.files.length) return showMsg('Please upload your selfie holding ID.', 'error');

    const form = document.getElementById('regForm');
    const fd = new FormData(form);
    
    // Prepend +63 to contact number
    const contactNo = fd.get('contact_no');
    if (contactNo && !contactNo.startsWith('+63')) {
      fd.set('contact_no', '+63 ' + contactNo);
    }
    
    // Append files manually since they are outside the form
    if (photoPathEl.files.length > 0) {
      fd.append('photo_path', photoPathEl.files[0]);
    }
    if (idImagePathEl.files.length > 0) {
      fd.append('id_image_path', idImagePathEl.files[0]);
    }
    if (selfiePathEl.files.length > 0) {
      fd.append('selfie_image_path', selfiePathEl.files[0]);
    }
    
    fd.append('verification_token', verificationToken);
    fd.append('household_mode', 'new');

    // Debug: Log email and password
    console.log('=== Registration Debug ===');
    console.log('Email:', fd.get('email'));
    console.log('Password:', fd.get('password') ? 'Password provided (length: ' + fd.get('password').length + ')' : 'No password');
    console.log('Registered Via: public_form');

    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Submitting...';

    const { res, data } = await apiForm('{{ url("/api/v1/public/residents/register") }}', 'POST', fd);

    if (res.status === 405) {
      btnSubmit.disabled = false;
      btnSubmit.textContent = 'Submit Registration';
      return showMsg('Error 405: Method Not Allowed. This is usually caused by a server redirect. Please ensure you are accessing the site via the correct protocol (HTTP vs HTTPS).', 'error');
    }

    console.log(data)
    console.log(res)
    btnSubmit.disabled = false;
    btnSubmit.textContent = 'Submit Registration';

    if (!res.ok) return showMsg(data, 'error');

    // Show success message
    form.reset();
    document.getElementById('dz_id').classList.remove('has-file');
    document.getElementById('dz_selfie').classList.remove('has-file');
    document.getElementById('dz_id').querySelectorAll('.dropzone-preview').forEach(el => el.remove());
    document.getElementById('dz_selfie').querySelectorAll('.dropzone-preview').forEach(el => el.remove());

    otpId = null;
    verificationToken = null;
    otpCooldownUntil = 0;
    updateCooldownUI();

    // Success - redirect to login with success message
    const params = new URLSearchParams();
    params.set('registered', 'true');
    window.location.href = '/auth/login?' + params.toString();
  });
  
  // Navbar scroll effect
  window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar-glass');
    if (navbar) {
      if (window.scrollY > 50) {
        navbar.style.background = 'linear-gradient(135deg, #1055C9 0%, #0d47a1 100%)';
      } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.15)';
      }
    }
  });
</script>


</body>
</html>
