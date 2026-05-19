@extends('layouts.public', ['currentRoute' => 'public.about'])

@push('styles')
<style>
.page-header {
  text-align: center;
  margin-bottom: 50px;
}

.page-header h1 {
  color: #1f2937;
}

.page-header p {
  color: #4b5563;
}

.mission-vision-card {
  padding: 40px;
  text-align: center;
  height: 100%;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 215, 0, 0.2);
  border-radius: 16px;
  color: #1f2937;
}

.mission-vision-card i {
  font-size: 3rem;
  margin-bottom: 20px;
}

.mission-vision-card h3 {
  margin-bottom: 16px;
  color: #1f2937;
}

.mission-vision-card p {
  color: #4b5563;
}

.about-card {
  padding: 32px;
  margin-bottom: 24px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 215, 0, 0.2);
  border-radius: 16px;
  color: #1f2937;
}

.about-card h2, .about-card h5, .about-card p {
  color: #1f2937;
}

.timeline-item {
  position: relative;
  padding-left: 40px;
  padding-bottom: 30px;
  border-left: 3px solid rgba(255, 215, 0, 0.3);
}

.timeline-item::before {
  content: '';
  position: absolute;
  left: -10px;
  top: 0;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #FFD700;
}

.timeline-item:last-child {
  padding-bottom: 0;
  border-left: none;
}

.timeline-item h5 {
  color: #1f2937;
}

.timeline-item p {
  color: #4b5563;
}

.stat-card {
  padding: 24px;
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: #FFC107;
}

.stat-label {
  font-size: 0.9rem;
  color: #4b5563;
}

.program-card {
  padding: 24px;
  transition: all 0.3s;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(255, 215, 0, 0.15);
  border-radius: 12px;
  color: #1f2937;
}

.program-card:hover {
  transform: translateY(-5px);
  background: rgba(255, 215, 0, 0.15);
}

.program-card h5, .program-card p {
  color: #1f2937;
}

.program-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  margin-bottom: 16px;
  background: rgba(255, 215, 0, 0.2);
  border: 1px solid rgba(255, 215, 0, 0.3);
  color: #1f2937;
}

.section-title {
  color: #1f2937;
}
</style>
@endpush

@section('content')
<div class="page-header text-center mb-5">
  <h1 class="page-title">About Barangay Bucandala 1</h1>
  <p class="opacity-75" style="color: #4b5563;">Serving our community with dedication and integrity since 1994</p>
</div>

<!-- Mission & Vision -->
<div class="row g-4 mb-5">
  <div class="col-md-6">
    <div class="mission-vision-card">
      <i class="bi bi-bullseye" style="color: #FFC107;"></i>
      <h3>Our Mission</h3>
      <p>To provide efficient, transparent, and accessible public services to all residents of Barangay Bucandala 1. We are committed to promoting community welfare, maintaining peace and order, and fostering sustainable development for a better quality of life.</p>
    </div>
  </div>
  <div class="col-md-6">
    <div class="mission-vision-card">
      <i class="bi bi-eye" style="color: #059669;"></i>
      <h3>Our Vision</h3>
      <p>To be a model barangay in the City of Imus, recognized for excellent public service, active community participation, and continuous improvement in governance that ensures the safety, health, and prosperity of every resident.</p>
    </div>
  </div>
</div>

<!-- Statistics -->
<div class="p-4 mb-5" style="background: rgba(255,255,255,0.8); border-radius: 16px; border: 1px solid rgba(255,215,0,0.2);">
  <div class="row g-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-number">5,200+</div>
        <div class="stat-label">Registered Residents</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-number">1,200+</div>
        <div class="stat-label">Households</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-number">15+</div>
        <div class="stat-label">Barangay Officials</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-number">30+</div>
        <div class="stat-label">Years of Service</div>
      </div>
    </div>
  </div>
</div>

<!-- History -->
<div class="about-card mb-5">
  <h2 class="section-title"><i class="bi bi-clock-history"></i> Our History</h2>
  <div class="row">
    <div class="col-lg-6">
      <div class="timeline-item">
        <h5>1994</h5>
        <p>Barangay Bucandala 1 was officially established as a separate barangay from the larger Bucandala district.</p>
      </div>
      <div class="timeline-item">
        <h5>2001</h5>
        <p>Construction of the new Barangay Hall, providing better facilities for residents and officials.</p>
      </div>
      <div class="timeline-item">
        <h5>2010</h5>
        <p>Launch of computerization programs and digital record-keeping for efficient service.</p>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="timeline-item">
        <h5>2018</h5>
        <p>Implementation of the first online services for document requests and resident registration.</p>
      </div>
      <div class="timeline-item">
        <h5>2023</h5>
        <p>Launch of the new Management Information System with enhanced features and security.</p>
      </div>
      <div class="timeline-item">
        <h5>Present</h5>
        <p>Continuing our commitment to serve the community with modern technology and dedicated personnel.</p>
      </div>
    </div>
  </div>
</div>

<!-- Community Programs -->
<div class="about-card mb-5">
  <h2 class="section-title"><i class="bi bi-people"></i> Community Programs</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-heart-pulse"></i></div>
        <h5>Health Programs</h5>
        <p class="small">Monthly medical missions, vaccination drives, health seminars, and free check-ups for residents.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-book"></i></div>
        <h5>Education Support</h5>
        <p class="small">Scholar programs, educational assistance, and learning materials distribution for students.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-shield-check"></i></div>
        <h5>Peace & Order</h5>
        <p class="small">Tanod watch program, community patrol, and disaster preparedness training.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-tree"></i></div>
        <h5>Environment</h5>
        <p class="small">Clean and green programs, tree planting, and waste management initiatives.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-person-heart"></i></div>
        <h5>Senior Citizens</h5>
        <p class="small">Monthly benefits, medical assistance, and social activities for senior residents.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="program-card glass">
        <div class="program-icon"><i class="bi bi-person-hearts"></i></div>
        <h5>Youth Programs</h5>
        <p class="small">Sports activities, skills training, and leadership development for the youth.</p>
      </div>
    </div>
  </div>
</div>

<!-- Why Our System -->
<div class="glass about-card">
  <h2 class="section-title"><i class="bi bi-laptop"></i> Why Our Online System?</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <h5><i class="bi bi-clock me-2" style="color: #FEEE91;"></i>Save Time</h5>
      <p class="small opacity-75">Skip the long queues. Register, request documents, and file reports from anywhere, anytime.</p>
    </div>
    <div class="col-md-4">
      <h5><i class="bi bi-shield-lock me-2" style="color: #86efac;"></i>Secure & Private</h5>
      <p class="small opacity-75">Your data is protected under the Data Privacy Act. We use secure encryption for all transactions.</p>
    </div>
    <div class="col-md-4">
      <h5><i class="bi bi-graph-up me-2" style="color: #93c5fd;"></i>Transparent</h5>
      <p class="small opacity-75">Track your requests in real-time. Know the status of your documents, blotters, and applications.</p>
    </div>
  </div>
</div>
@endsection