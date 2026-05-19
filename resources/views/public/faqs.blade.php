@extends('layouts.public', ['currentRoute' => 'public.faqs'])

@push('styles')
<style>
.search-box {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 14px 20px;
  border-radius: 12px;
  font-size: 1rem;
}

.search-box:focus {
  outline: none;
  border-color: var(--mis-yellow);
  background: rgba(255, 255, 255, 0.15);
}

.search-box::placeholder { color: rgba(255, 255, 255, 0.5); }

.category-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 10px 20px;
  border-radius: 50px;
  transition: all 0.3s;
  cursor: pointer;
}

.category-btn:hover, .category-btn.active {
  background: var(--mis-yellow);
  color: #1f2937;
  border-color: var(--mis-yellow);
}

.faq-item {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  margin-bottom: 12px;
  overflow: hidden;
}

.faq-question {
  padding: 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 600;
  transition: background 0.3s;
}

.faq-question:hover {
  background: rgba(255, 255, 255, 0.05);
}

.faq-question i {
  transition: transform 0.3s;
}

.faq-item.open .faq-question i {
  transform: rotate(180deg);
}

.faq-answer {
  padding: 0 20px;
  max-height: 0;
  overflow: hidden;
  transition: all 0.3s ease;
}

.faq-item.open .faq-answer {
  padding: 0 20px 20px;
  max-height: 500px;
}

.tag {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
  background: rgba(255, 255, 255, 0.1);
  margin-right: 8px;
}
</style>
@endpush

@section('content')
<div class="page-header text-center mb-5">
  <h1 class="page-title">Frequently Asked Questions</h1>
  <p class="opacity-75">Find answers to common questions about our services</p>
</div>

<!-- Search -->
<div class="glass p-4 mb-4">
  <input type="text" class="form-control search-box" id="faqSearch" placeholder="Search for a question...">
</div>

<!-- Categories -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <button class="category-btn active" data-category="all">All</button>
  <button class="category-btn" data-category="registration">Registration</button>
  <button class="category-btn" data-category="documents">Documents</button>
  <button class="category-btn" data-category="blotter">Blotter</button>
  <button class="category-btn" data-category="services">Services</button>
  <button class="category-btn" data-category="account">Account</button>
</div>

<!-- FAQs -->
<div class="row">
  <div class="col-lg-8">
    <div id="faqList">
      <!-- Registration FAQs -->
      <div class="faq-item" data-category="registration">
        <div class="faq-question">
          <span>How do I register as a new resident?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Click the "Register" button on the homepage or navigate to the registration page. Fill out the form with your personal details, upload a valid ID and a selfie holding your ID, then verify your email with the OTP sent to your inbox.</p>
          <span class="tag">Registration</span>
        </div>
      </div>

      <div class="faq-item" data-category="account">
        <div class="faq-question">
          <span>How do I reset my password?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Currently, password reset is handled by barangay staff. Please visit the barangay hall with a valid ID to reset your password. For security purposes, we verify your identity in person.</p>
          <span class="tag">Account</span>
        </div>
      </div>

      <!-- Document FAQs -->
      <div class="faq-item" data-category="documents">
        <div class="faq-question">
          <span>What documents do I need for barangay clearance?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>You need: (1) Valid government-issued ID, (2) Recent 1x1 photo, (3) Proof of residency (if not a registered resident). The fee is ₱50.00.</p>
          <span class="tag">Documents</span>
        </div>
      </div>

      <div class="faq-item" data-category="documents">
        <div class="faq-question">
          <span>How long does it take to process a document request?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Processing times vary: Barangay Clearance (1-2 business days), Certificate of Indigency (1-2 business days), Business Permit (2-3 business days), Certificate of Residency (1-2 business days).</p>
          <span class="tag">Documents</span>
        </div>
      </div>

      <div class="faq-item" data-category="documents">
        <div class="faq-question">
          <span>Can I pay for documents online?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Yes! We accept online payment via GCash. You can also pay in cash at the barangay hall when you claim your document.</p>
          <span class="tag">Documents</span>
        </div>
      </div>

      <!-- Blotter FAQs -->
      <div class="faq-item" data-category="blotter">
        <div class="faq-question">
          <span>How do I file a blotter/report an incident?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>You can file a blotter online through our website or visit the barangay hall in person. Provide details about the incident including date, time, location, and parties involved. Your identity will be kept confidential.</p>
          <span class="tag">Blotter</span>
        </div>
      </div>

      <div class="faq-item" data-category="blotter">
        <div class="faq-question">
          <span>What happens after I file a blotter?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Once filed, a barangay official will contact you within 2-3 business days to verify your report. A hearing may be scheduled if necessary. Both parties will be invited to a mediation session.</p>
          <span class="tag">Blotter</span>
        </div>
      </div>

      <!-- Services FAQs -->
      <div class="faq-item" data-category="services">
        <div class="faq-question">
          <span>What are the barangay office hours?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Monday - Friday: 8:00 AM - 5:00 PM<br>Saturday: 8:00 AM - 12:00 PM<br>Sunday: Closed</p>
          <span class="tag">Services</span>
        </div>
      </div>

      <div class="faq-item" data-category="services">
        <div class="faq-question">
          <span>Where is the barangay hall located?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Barangay Bucandala 1 is located in the City of Imus, Cavite. The main hall is near the Bucandala Plaza. You can find directions on our Contact page.</p>
          <span class="tag">Services</span>
        </div>
      </div>

      <div class="faq-item" data-category="services">
        <div class="faq-question">
          <span>How do I schedule an appointment?</span>
          <i class="bi bi-chevron-down"></i>
        </div>
        <div class="faq-answer">
          <p>Call (046) 123-4567 or email info@bucandala1.gov.ph to schedule an appointment. Walk-ins are also accepted but appointments are given priority.</p>
          <span class="tag">Services</span>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="glass p-4">
      <h5 class="mb-3">Can't find what you're looking for?</h5>
      <p class="small mb-3 opacity-75">Contact us directly and we'll help you with your question.</p>
      <a href="{{ route('public.contact') }}" class="btn btn-glass w-100">
        <i class="bi bi-envelope me-2"></i>Contact Us
      </a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// FAQ Toggle
document.querySelectorAll('.faq-question').forEach(q => {
  q.addEventListener('click', () => {
    q.parentElement.classList.toggle('open');
  });
});

// Category Filter
document.querySelectorAll('.category-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    const cat = btn.dataset.category;
    document.querySelectorAll('.faq-item').forEach(item => {
      if (cat === 'all' || item.dataset.category === cat) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });
});

// Search
document.getElementById('faqSearch').addEventListener('input', (e) => {
  const search = e.target.value.toLowerCase();
  document.querySelectorAll('.faq-item').forEach(item => {
    const text = item.textContent.toLowerCase();
    item.style.display = text.includes(search) ? 'block' : 'none';
  });
});
</script>
@endpush