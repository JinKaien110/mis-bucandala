@extends('layouts.public', ['currentRoute' => 'public.services.documents'])

@push('styles')
<style>
.page-header {
  text-align: center;
  margin-bottom: 40px;
}

.page-title {
  font-size: 2.2rem;
  font-weight: 700;
  margin-bottom: 8px;
}

.page-subtitle {
  color: rgba(255, 255, 255, 0.75);
}

.step-indicator {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin-bottom: 40px;
}

.step {
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0.5;
}

.step.active, .step.completed {
  opacity: 1;
}

.step-number {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.step.active .step-number {
  background: var(--mis-blue);
  border-color: var(--mis-blue);
}

.step.completed .step-number {
  background: var(--mis-green);
  border-color: var(--mis-green);
}

.step-label {
  font-size: 0.9rem;
}

.doc-type-card {
  padding: 16px;
  margin-bottom: 12px;
  cursor: pointer;
  transition: all 0.3s;
}

.doc-type-card:hover {
  transform: translateX(8px);
  background: rgba(255, 255, 255, 0.18);
}

.doc-type-card.selected {
  border-color: var(--mis-blue-light);
  background: rgba(59, 130, 246, 0.2);
}

.doc-icon {
  font-size: 24px;
  margin-bottom: 8px;
}

.doc-title {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 4px;
}

.doc-desc {
  font-size: 0.8rem;
  opacity: 0.7;
  margin-bottom: 8px;
}

.doc-price {
  font-weight: 700;
  color: var(--mis-yellow);
}

.form-card {
  padding: 32px;
}

.form-label {
  font-weight: 600;
  margin-bottom: 8px;
}

.form-control, .form-textarea {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 12px 16px;
  border-radius: 12px;
  width: 100%;
}

.form-control:focus, .form-textarea:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: var(--mis-blue-light);
  color: #fff;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
  outline: none;
}

.form-control::placeholder { color: rgba(255, 255, 255, 0.5); }

.req-badge {
  color: #fca5a5;
  font-weight: 700;
}

@media (max-width: 768px) {
  .page-title { font-size: 1.6rem; }
  .step-label { display: none; }
}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1 class="page-title">Request Document</h1>
  <p class="page-subtitle">Select a document type and fill out the form</p>
</div>

<div class="step-indicator">
  <div class="step active" id="step1Indicator">
    <div class="step-number">1</div>
    <span class="step-label">Select Type</span>
  </div>
  <div class="step" id="step2Indicator">
    <div class="step-number">2</div>
    <span class="step-label">Fill Details</span>
  </div>
  <div class="step" id="step3Indicator">
    <div class="step-number">3</div>
    <span class="step-label">Confirm</span>
  </div>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="glass p-4 mb-4">
      <h5 class="mb-3"><i class="bi bi-file-earmark-text me-2"></i>Document Types</h5>
      
      <div class="doc-type-card glass" onclick="selectDocType('clearance')">
        <div class="doc-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="doc-title">Barangay Clearance</div>
        <div class="doc-desc">For employment, travel, and other purposes</div>
        <div class="doc-price">₱50.00</div>
      </div>

      <div class="doc-type-card glass" onclick="selectDocType('indigency')">
        <div class="doc-icon"><i class="bi bi-award"></i></div>
        <div class="doc-title">Certificate of Indigency</div>
        <div class="doc-desc">For medical, educational, and social assistance</div>
        <div class="doc-price">₱30.00</div>
      </div>

      <div class="doc-type-card glass" onclick="selectDocType('residency')">
        <div class="doc-icon"><i class="bi bi-house"></i></div>
        <div class="doc-title">Certificate of Residency</div>
        <div class="doc-desc">Proof of residency within the barangay</div>
        <div class="doc-price">₱40.00</div>
      </div>

      <div class="doc-type-card glass" onclick="selectDocType('business')">
        <div class="doc-icon"><i class="bi bi-shop"></i></div>
        <div class="doc-title">Business Permit</div>
        <div class="doc-desc">New or renewal of business permit</div>
        <div class="doc-price">₱200.00</div>
      </div>

      <div class="doc-type-card glass" onclick="selectDocType('goods')">
        <div class="doc-icon"><i class="bi bi-box-seam"></i></div>
        <div class="doc-title">Certificate of No Objection</div>
        <div class="doc-desc">For selling goods in the barangay</div>
        <div class="doc-price">₱50.00</div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="glass form-card">
      <form id="docRequestForm">
        <input type="hidden" id="docType" name="docType">

        <div id="step1" class="step-content">
          <div class="text-center py-5">
            <i class="bi bi-arrow-left-circle fs-1 mb-3 text-white-50"></i>
            <p class="text-white-50">Please select a document type from the left panel</p>
          </div>
        </div>

        <div id="step2" class="step-content" style="display:none;">
          <h5 class="mb-4">Applicant Information</h5>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="req-badge">*</span></label>
              <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="tel" class="form-control" name="contact_no" placeholder="09xxxxxxxxx">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address <span class="req-badge">*</span></label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date of Birth <span class="req-badge">*</span></label>
              <input type="date" class="form-control" name="birth_date" required>
            </div>
            <div class="col-12">
              <label class="form-label">Address <span class="req-badge">*</span></label>
              <input type="text" class="form-control" name="address" placeholder="Block/Lot, Street, Barangay Bucandala 1" required>
            </div>
            <div class="col-12">
              <label class="form-label">Purpose <span class="req-badge">*</span></label>
              <textarea class="form-control" name="purpose" rows="3" placeholder="Please state the purpose of your request..." required></textarea>
            </div>
          </div>

          <div class="d-flex gap-3 mt-4">
            <button type="button" class="btn btn-glass" onclick="goToStep(1)">Back</button>
            <button type="button" class="btn btn-primary-glass" onclick="goToStep(3)">Continue</button>
          </div>
        </div>

        <div id="step3" class="step-content" style="display:none;">
          <div class="text-center py-4">
            <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
            <h4>Review Your Request</h4>
            <p class="text-white-50">Please review your information before submitting</p>
          </div>
          
          <div class="glass p-4 mb-4">
            <div class="row">
              <div class="col-md-6 mb-3">
                <small class="text-white-50">Document Type</small>
                <p class="mb-0 fw-bold" id="summaryDocType">-</p>
              </div>
              <div class="col-md-6 mb-3">
                <small class="text-white-50">Processing Fee</small>
                <p class="mb-0 fw-bold" id="summaryPrice">-</p>
              </div>
              <div class="col-md-6 mb-3">
                <small class="text-white-50">Applicant Name</small>
                <p class="mb-0" id="summaryName">-</p>
              </div>
              <div class="col-md-6 mb-3">
                <small class="text-white-50">Contact</small>
                <p class="mb-0" id="summaryContact">-</p>
              </div>
              <div class="col-12">
                <small class="text-white-50">Purpose</small>
                <p class="mb-0" id="summaryPurpose">-</p>
              </div>
            </div>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="agree" required>
            <label class="form-check-label" for="agree">
              I certify that the information provided is true and correct
            </label>
          </div>

          <div class="d-flex gap-3">
            <button type="button" class="btn btn-glass" onclick="goToStep(2)">Back</button>
            <button type="submit" class="btn btn-primary-glass">
              <i class="bi bi-send me-2"></i>Submit Request
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const docPrices = {
  clearance: '₱50.00',
  indigency: '₱30.00',
  residency: '₱40.00',
  business: '₱200.00',
  goods: '₱50.00'
};

const docNames = {
  clearance: 'Barangay Clearance',
  indigency: 'Certificate of Indigency',
  residency: 'Certificate of Residency',
  business: 'Business Permit',
  goods: 'Certificate of No Objection'
};

let selectedDoc = null;

function selectDocType(type) {
  selectedDoc = type;
  document.getElementById('docType').value = type;
  
  document.querySelectorAll('.doc-type-card').forEach(card => card.classList.remove('selected'));
  event.currentTarget.classList.add('selected');
  
  goToStep(2);
}

function goToStep(step) {
  document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
  document.getElementById('step' + step).style.display = 'block';
  
  document.querySelectorAll('.step').forEach((el, i) => {
    el.classList.remove('active', 'completed');
    if (i + 1 < step) el.classList.add('completed');
    if (i + 1 === step) el.classList.add('active');
  });

  if (step === 3) {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    document.getElementById('summaryDocType').textContent = docNames[selectedDoc] || '-';
    document.getElementById('summaryPrice').textContent = docPrices[selectedDoc] || '-';
    document.getElementById('summaryName').textContent = formData.get('full_name') || '-';
    document.getElementById('summaryContact').textContent = formData.get('email') || '-';
    document.getElementById('summaryPurpose').textContent = formData.get('purpose') || '-';
  }
}

document.getElementById('docRequestForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Your document request has been submitted! You will receive an email confirmation shortly.');
  window.location.href = '{{ route("public.home") }}';
});
</script>
@endpush