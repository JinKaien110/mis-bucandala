<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Privacy Policy - Barangay Bucandala 1</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root {
      --mis-blue: #1055C9;
      --mis-blue-light: #3b82f6;
      --mis-blue-dark: #0d47a1;
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
    }
    
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
    }
    
    .orb-2 {
      width: 500px;
      height: 500px;
      background: rgba(254, 238, 145, 0.2);
      bottom: -150px;
      right: -150px;
    }
    
    @keyframes floatReg {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50% { transform: translate(-20px, 20px) scale(0.95); }
    }
    
    .wrap { 
      max-width: 900px; 
      margin: 28px auto; 
      padding: 0 16px 40px;
      position: relative;
      z-index: 1;
    }
    
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
    }
    
    .cardx { 
      background: rgba(255, 255, 255, 0.98);
      border: 1px solid rgba(255, 255, 255, 0.5);
      border-radius: 24px; 
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(20px);
    }
    
    .cardx-body { padding: 35px; }
    
    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .page-subtitle {
      color: #6b7280;
      font-size: 14px;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .section-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #1f2937;
      margin: 28px 0 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    .section-title:first-of-type {
      margin-top: 0;
    }
    
    .content-text {
      color: #374151;
      font-size: 14px;
      line-height: 1.7;
    }
    
    .content-text ul {
      padding-left: 20px;
    }
    
    .content-text li {
      margin-bottom: 8px;
    }
    
    .highlight-box {
      background: linear-gradient(135deg, rgba(16, 85, 201, 0.05) 0%, rgba(16, 85, 201, 0.1) 100%);
      border-left: 4px solid var(--mis-blue);
      border-radius: 0 12px 12px 0;
      padding: 16px 20px;
      margin: 20px 0;
    }
    
    .highlight-box.info {
      background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(59, 130, 246, 0.1) 100%);
      border-left-color: #3b82f6;
    }
    
    .highlight-box.warning {
      background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(245, 158, 11, 0.1) 100%);
      border-left-color: #f59e0b;
    }
    
    .highlight-box.success {
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.1) 100%);
      border-left-color: #10b981;
    }
    
    .contact-card {
      background: #f8fafc;
      border-radius: 16px;
      padding: 24px;
      margin-top: 24px;
    }
    
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      margin-bottom: 20px;
      transition: opacity 0.3s;
    }
    
    .back-link:hover {
      opacity: 0.85;
      color: #fff;
    }
    
    .law-ref {
      font-size: 12px;
      color: #9ca3af;
      font-style: italic;
      margin-top: 4px;
    }
    
    .accordion-item {
      border: 1px solid #e5e7eb;
      border-radius: 12px !important;
      margin-bottom: 8px;
      overflow: hidden;
    }
    
    .accordion-button {
      border-radius: 12px;
      font-weight: 600;
      color: #1f2937;
      background: #fff;
    }
    
    .accordion-button:not(.collapsed) {
      background: linear-gradient(135deg, rgba(16, 85, 201, 0.1) 0%, rgba(16, 85, 201, 0.05) 100%);
      color: var(--mis-blue);
      box-shadow: none;
    }
    
    .accordion-button:focus {
      box-shadow: none;
      border-color: #e5e7eb;
    }
    
    .accordion-body {
      background: #fafafa;
      font-size: 14px;
      line-height: 1.7;
      color: #374151;
    }
  </style>
</head>
<body>
  <div class="bg-animation">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
  </div>
  
  {{-- Navigation Bar --}}
  <nav class="navbar navbar-expand-lg navbar-glass fixed-top" style="position: relative; background: rgba(255,255,255,0.1); backdrop-filter: blur(20px);">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('public.home') }}">
        <img src="{{ asset('storage/branding/barangay-logo.jpg') }}" alt="Logo" width="40" height="40" class="rounded-circle">
        <span class="fw-bold">Barangay Bucandala 1</span>
      </a>
      <div class="ms-auto d-flex align-items-center gap-3">
        <a href="{{ route('public.home') }}" class="text-white opacity-75" style="text-decoration: none;">Home</a>
        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </a>
      </div>
    </div>
  </nav>

<div class="wrap" style="padding-top: 80px;">

  <a href="{{ route('public.resident.register') }}" class="back-link">
    <i class="bi bi-arrow-left"></i> Back to Registration
  </a>

  <div class="brand-header">
    <img 
      src="{{ asset('storage/branding/barangay-logo.jpg') }}" 
      alt="Barangay Logo" 
      class="brand-logo"
      onerror="this.src='https://via.placeholder.com/80?text=BRGY'"
    >
    <h1 class="brand-title">Barangay Bucandala 1</h1>
  </div>

  <div class="cardx">
    <div class="cardx-body">

      <div class="page-title">
        <i class="bi bi-shield-check text-primary"></i>
        Data Privacy Policy
      </div>
      <div class="page-subtitle">
        In compliance with the Philippines Data Privacy Act of 2012 (Republic Act 10173)
      </div>

      <div class="highlight-box info">
        <div class="d-flex gap-2">
          <i class="bi bi-info-circle mt-1"></i>
          <div>
            <strong>Effective Date:</strong> January 1, 2025<br>
            <strong>Version:</strong> 1.0
          </div>
        </div>
      </div>

      <div class="section-title">
        <i class="bi bi-building"></i>
        1. Introduction
      </div>
      <div class="content-text">
        <p>Barangay Bucandala 1 ("we," "our," or "the Barangay") is committed to protecting the privacy and personal information of all residents, stakeholders, and individuals who interact with our services. This Data Privacy Policy outlines how we collect, use, disclose, and safeguard your personal data in compliance with Republic Act 10173, also known as the <strong>Data Privacy Act of 2012</strong> of the Philippines.</p>
        <p>We encourage you to read this policy carefully to understand how we handle your personal information.</p>
      </div>

      <div class="section-title">
        <i class="bi bi-collection"></i>
        2. Why We Collect Personal Data
      </div>
      <div class="content-text">
        <p>We collect personal information for the following purposes:</p>
        <ul>
          <li><strong>Resident Registration and Identification</strong> - To formally register residents within the barangay jurisdiction and maintain accurate records.</li>
          <li><strong>Delivery of Government Services</strong> - To provide and deliver various barangay services, programs, and assistance.</li>
          <li><strong>Documentation and Record-Keeping</strong> - To maintain official records required by local government regulations.</li>
          <li><strong>Emergency Response</strong> - To facilitate disaster management, emergency response, and public safety initiatives.</li>
          <li><strong>Compliance</strong> - To comply with legal obligations and requirements from higher government authorities.</li>
          <li><strong>Communication</strong> - To communicate important announcements, advisories, and updates to residents.</li>
          <li><strong>Statistical Analysis</strong> - To generate reports and statistics for planning and development purposes.</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-card-list"></i>
        3. Types of Personal Data We Collect
      </div>
      <div class="content-text">
        <p>The personal data we may collect includes:</p>
        <ul>
          <li>Full name (first, middle, last)</li>
          <li>Date of birth and place of birth</li>
          <li>Sex/gender and civil status</li>
          <li>Residential address within the barangay</li>
          <li>Contact information (phone number, email address)</li>
          <li>Occupation and employment details</li>
          <li>Government-issued identification numbers (e.g., PhilSys ID, driver's license, passport)</li>
          <li>Photographs (for identification purposes)</li>
          <li>Guardian/parent information (for minors)</li>
          <li>Other information required by law or government regulations</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-hand-thumbs-up"></i>
        4. How We Collect Personal Data
      </div>
      <div class="content-text">
        <p>We collect personal data through:</p>
        <ul>
          <li><strong>Direct Submission</strong> - When you voluntarily fill out registration forms on our website or physical forms at our office.</li>
          <li><strong>Uploaded Documents</strong> - When you upload identification documents and photographs as part of the verification process.</li>
          <li><strong>Email Communications</strong> - When you correspond with us via email for inquiries or requests.</li>
          <li><strong>Verified Third Parties</strong> - From authorized government agencies or institutions as required for verification purposes.</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-lock"></i>
        5. How We Protect Your Data
      </div>
      <div class="content-text">
        <p>We implement robust security measures to protect your personal information:</p>
        <ul>
          <li><strong>Encryption</strong> - Data is encrypted both in transit (using SSL/TLS) and at rest using industry-standard encryption protocols.</li>
          <li><strong>Access Controls</strong> - Strict access controls ensure that only authorized personnel can access personal data.</li>
          <li><strong>Secure Storage</strong> - Personal data is stored in secure servers with physical and technical safeguards.</li>
          <li><strong>Regular Audits</strong> - We conduct regular security audits and assessments to identify and address vulnerabilities.</li>
          <li><strong>Data Minimization</strong> - We collect only the data necessary for the stated purposes.</li>
          <li><strong>Retention Policies</strong> - Data is retained only for as long as necessary or as required by law.</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-share"></i>
        6. Data Sharing and Disclosure
      </div>
      <div class="content-text">
        <p>We may share your personal data with:</p>
        <ul>
          <li><strong>Government Agencies</strong> - As required by law or for compliance with local government requirements (e.g., municipal hall, DILG, PSA).</li>
          <li><strong>Service Providers</strong> - Third parties who assist us in delivering services (with strict data processing agreements).</li>
          <li><strong>Emergency Services</strong> - In cases of emergency or public safety situations.</li>
        </ul>
        <p class="law-ref">Under the Data Privacy Act, we may disclose data without consent in cases provided by law (Section 13, R.A. 10173).</p>
        
        <div class="highlight-box warning">
          <div class="d-flex gap-2">
            <i class="bi bi-exclamation-triangle mt-1"></i>
            <div>
              <strong>We do NOT sell, trade, or rent your personal information</strong> to third parties for marketing purposes. Your data is used solely for official barangay purposes.
            </div>
          </div>
        </div>
      </div>

      <div class="section-title">
        <i class="bi bi-clock-history"></i>
        7. Data Retention
      </div>
      <div class="content-text">
        <p>Personal data will be retained for as long as necessary to fulfill the purposes for which it was collected, or as required by applicable laws and regulations. When data is no longer needed, we ensure its secure disposal in accordance with our data retention and disposal policies.</p>
      </div>

      <div class="section-title">
        <i class="bi bi-person-check"></i>
        8. Your Rights Under the Data Privacy Act
      </div>
      <div class="content-text">
        <p>As a data subject, you have the following rights under R.A. 10173:</p>
        <ul>
          <li><strong>Right to be Informed</strong> - You have the right to know what personal data we collect and how it is used.</li>
          <li><strong>Right to Access</strong> - You may request access to your personal data and obtain a copy.</li>
          <li><strong>Right to Correction</strong> - You may request correction of inaccurate or incomplete personal data.</li>
          <li><strong>Right to Erasure/Blocking</strong> - You may request the deletion or blocking of your personal data when it is no longer necessary.</li>
          <li><strong>Right to Object</strong> - You may object to the processing of your personal data.</li>
          <li><strong>Right to Data Portability</strong> - You may request a copy of your data in a structured, commonly used format.</li>
          <li><strong>Right to File a Complaint</strong> - You may file a complaint with the National Privacy Commission (NPC) if you believe your rights have been violated.</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-pencil-square"></i>
        9. How to Exercise Your Rights
      </div>
      <div class="content-text">
        <p>To exercise any of your rights, you may contact our Data Protection Officer (DPO) through:</p>
        
        <div class="contact-card">
          <h6 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>Data Protection Officer</h6>
          <p class="mb-2"><strong>Barangay Bucandala 1</strong></p>
          <p class="mb-2">Email: <a href="mailto:dpo@bucandala1.gov.ph">dpo@bucandala1.gov.ph</a></p>
          <p class="mb-0">Phone: (046) 123-4567</p>
        </div>
        
        <p class="mt-3">For complaints, you may also contact the <strong>National Privacy Commission (NPC)</strong>:</p>
        <ul>
          <li>Website: <a href="https://privacy.gov.ph" target="_blank">privacy.gov.ph</a></li>
          <li>Email: <a href="mailto:complaints@privacy.gov.ph">complaints@privacy.gov.ph</a></li>
          <li>Hotline: (02) 8234-2228</li>
        </ul>
      </div>

      <div class="section-title">
        <i class="bi bi-globe"></i>
        9. Updates to This Policy
      </div>
      <div class="content-text">
        <p>We may update this Data Privacy Policy from time to time to reflect changes in our practices or legal requirements. Any updates will be posted on this page, and we encourage you to review this policy periodically.</p>
      </div>

      <div class="highlight-box success mt-4">
        <div class="d-flex gap-2">
          <i class="bi bi-check-circle mt-1"></i>
          <div>
            <strong>Consent</strong><br>
            By registering with Barangay Bucandala 1, you acknowledge that you have read, understood, and agree to this Data Privacy Policy. You consent to the collection, use, and processing of your personal data as described herein.
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

</body>
</html>
