@extends('layouts.public', ['currentRoute' => 'public.services.blotter'])

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

.info-card {
  padding: 20px;
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.info-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.15);
  flex-shrink: 0;
}

.info-content h5 {
  font-weight: 600;
  margin-bottom: 4px;
}

.info-content p {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

.form-card {
  padding: 32px;
}

.form-label {
  font-weight: 600;
  margin-bottom: 8px;
}

.form-control, .form-select, .form-textarea {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  padding: 12px 16px;
  border-radius: 12px;
  width: 100%;
}

.form-control:focus, .form-select:focus, .form-textarea:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: var(--mis-blue-light);
  color: #fff;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
  outline: none;
}

.form-control::placeholder { color: rgba(255, 255, 255, 0.5); }
.form-select option { background: #1e3a8a; color: #fff; }
.form-textarea { min-height: 120px; resize: vertical; }

.req-badge {
  color: #fca5a5;
  font-weight: 700;
}

.incident-type-card {
  padding: 16px;
  border: 2px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
}

.incident-type-card:hover {
  border-color: rgba(255, 255, 255, 0.4);
  background: rgba(255, 255, 255, 0.08);
}

.incident-type-card.selected {
  border-color: var(--mis-blue-light);
  background: rgba(59, 130, 246, 0.2);
}

.incident-type-card i {
  font-size: 24px;
  margin-bottom: 8px;
}

.incident-type-card span {
  display: block;
  font-size: 0.85rem;
}

.party-card {
  padding: 16px;
  border: 1px dashed rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  margin-bottom: 12px;
  position: relative;
}

.remove-party {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(239, 68, 68, 0.3);
  border: none;
  color: #fca5a5;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  cursor: pointer;
}

.note-box {
  background: rgba(254, 238, 145, 0.1);
  border-left: 4px solid var(--mis-yellow);
  padding: 16px;
  border-radius: 0 12px 12px 0;
  margin-top: 20px;
}

.note-box h6 {
  color: var(--mis-yellow);
  margin-bottom: 8px;
}

.note-box p {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  margin: 0;
}

@media (max-width: 768px) {
  .page-title { font-size: 1.6rem; }
}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1 class="page-title">File a Blotter Report</h1>
  <p class="page-subtitle">Report an incident or complaint to the barangay</p>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="glass p-4 mb-4">
      <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Before You File</h5>
      
      <div class="info-card glass-light">
        <div class="info-icon"><i class="bi bi-clock"></i></div>
        <div class="info-content">
          <h5>Response Time</h5>
          <p>Blotter reports are reviewed within 24-48 hours.</p>
        </div>
      </div>

      <div class="info-card glass-light">
        <div class="info-icon"><i class="bi bi-shield-check"></i></div>
        <div class="info-content">
          <h5>Confidentiality</h5>
          <p>Your identity is kept confidential throughout the process.</p>
        </div>
      </div>

      <div class="info-card glass-light">
        <div class="info-icon"><i class="bi bi-telephone"></i></div>
        <div class="info-content">
          <h5>Emergency</h5>
          <p>For immediate emergencies, call 911 or visit the barangay hall.</p>
        </div>
      </div>
    </div>

    <div class="glass p-4">
      <h5 class="mb-3"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
      <div class="mb-2"><i class="bi bi-geo-alt me-2"></i>Barangay Hall, Bucandala 1</div>
      <div class="mb-2"><i class="bi bi-telephone me-2"></i>(046) 123-4567</div>
      <div><i class="bi bi-envelope me-2"></i>blotter@bucandala1.gov.ph</div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="glass form-card">
      <form id="blotterForm">
        <h5 class="mb-4">Incident Details</h5>
        
        <div class="row g-3 mb-4">
          <div class="col-12">
            <label class="form-label">Type of Incident <span class="req-badge">*</span></label>
            <div class="row g-2">
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('dispute')">
                  <i class="bi bi-people"></i>
                  <span>Dispute</span>
                </div>
              </div>
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('noise')">
                  <i class="bi bi-volume-up"></i>
                  <span>Noise</span>
                </div>
              </div>
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('theft')">
                  <i class="bi bi-bag-x"></i>
                  <span>Theft</span>
                </div>
              </div>
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('assault')">
                  <i class="bi bi-person-x"></i>
                  <span>Assault</span>
                </div>
              </div>
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('property')">
                  <i class="bi bi-house-x"></i>
                  <span>Property</span>
                </div>
              </div>
              <div class="col-4">
                <div class="incident-type-card" onclick="selectIncidentType('other')">
                  <i class="bi bi-three-dots"></i>
                  <span>Other</span>
                </div>
              </div>
            </div>
            <input type="hidden" id="incidentType" name="incident_type">
          </div>

          <div class="col-md-6">
            <label class="form-label">Date of Incident <span class="req-badge">*</span></label>
            <input type="date" class="form-control" name="incident_date" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Time of Incident <span class="req-badge">*</span></label>
            <input type="time" class="form-control" name="incident_time" required>
          </div>
          <div class="col-12">
            <label class="form-label">Location of Incident <span class="req-badge">*</span></label>
            <input type="text" class="form-control" name="incident_location" placeholder="e.g., Near the basketball court, Block 5" required>
          </div>
          <div class="col-12">
            <label class="form-label">Description of Incident <span class="req-badge">*</span></label>
            <textarea class="form-textarea" name="description" placeholder="Please describe what happened in detail..." required></textarea>
          </div>
        </div>

        <h5 class="mb-4">Complainant Information</h5>
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name <span class="req-badge">*</span></label>
            <input type="text" class="form-control" name="complainant_name" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Contact Number</label>
            <input type="tel" class="form-control" name="complainant_contact" placeholder="09xxxxxxxxx">
          </div>
          <div class="col-md-6">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" name="complainant_email">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" class="form-control" name="complainant_address" placeholder="Your address in the barangay">
          </div>
        </div>

        <h5 class="mb-4 mt-4">Parties Involved</h5>
        
        <div id="partiesContainer">
          <div class="party-card">
            <div class="row g-2">
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Full Name">
              </div>
              <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Address">
              </div>
              <div class="col-md-3">
                <select class="form-select">
                  <option value="">Role</option>
                  <option value="complainant">Complainant</option>
                  <option value="respondent">Respondent</option>
                  <option value="witness">Witness</option>
                </select>
              </div>
              <div class="col-md-1">
                <button type="button" class="remove-party" onclick="this.parentElement.parentElement.parentElement.remove()">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <button type="button" class="btn btn-glass btn-sm mb-4" onclick="addParty()">
          <i class="bi bi-plus me-1"></i> Add Party
        </button>

        <div class="note-box">
          <h6><i class="bi bi-exclamation-triangle me-2"></i>Important Notice</h6>
          <p>Submitting a false blotter report is a punishable offense. Please ensure all information provided is accurate and truthful. A barangay official will contact you within 2-3 business days to verify your report.</p>
        </div>

        <div class="form-check mb-4 mt-4">
          <input class="form-check-input" type="checkbox" id="agreeBlotter" required>
          <label class="form-check-label" for="agreeBlotter">
            I certify that the information provided is true and correct to the best of my knowledge
          </label>
        </div>

        <div class="d-flex gap-3">
          <a href="{{ route('public.home') }}" class="btn btn-glass">Cancel</a>
          <button type="submit" class="btn btn-primary-glass">
            <i class="bi bi-send me-2"></i>Submit Report
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function selectIncidentType(type) {
  document.getElementById('incidentType').value = type;
  document.querySelectorAll('.incident-type-card').forEach(card => card.classList.remove('selected'));
  event.currentTarget.classList.add('selected');
}

function addParty() {
  const container = document.getElementById('partiesContainer');
  const newParty = document.createElement('div');
  newParty.className = 'party-card';
  newParty.innerHTML = `
    <div class="row g-2">
      <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Full Name">
      </div>
      <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Address">
      </div>
      <div class="col-md-3">
        <select class="form-select">
          <option value="">Role</option>
          <option value="complainant">Complainant</option>
          <option value="respondent">Respondent</option>
          <option value="witness">Witness</option>
        </select>
      </div>
      <div class="col-md-1">
        <button type="button" class="remove-party" onclick="this.parentElement.parentElement.parentElement.remove()">
          <i class="bi bi-x"></i>
        </button>
      </div>
    </div>
  `;
  container.appendChild(newParty);
}

document.getElementById('blotterForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Your blotter report has been submitted successfully. A barangay official will contact you within 2-3 business days.');
  window.location.href = '{{ route("public.home") }}';
});
</script>
@endpush