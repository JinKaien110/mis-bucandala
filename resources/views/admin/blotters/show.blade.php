@extends('layouts.admin')

@section('title', 'Blotter Details - Barangay MIS')

@section('content')
<div class="page-surface admin-blotter-detail">
  
  <!-- Header -->
  <div class="blotter-detail-header d-flex justify-content-between align-items-center mb-4">
    <div>
      <div class="small text-uppercase fw-bold text-primary mb-1">Blotter Record</div>
      <h4 class="mb-2 fw-bold text-primary">#{{ $blotter->blotter_no }}</h4>
      <p class="text-muted mb-0">
        <i class="bi bi-calendar-event me-1"></i> {{ $blotter->incident_date->format('M d, Y h:i A') }} 
        <span class="mx-2">•</span> 
        <span class="badge bg-light text-dark border">{{ $blotter->incident_type }}</span>
      </p>
    </div>

    <div class="blotter-detail-actions d-flex gap-2">
      <a class="btn btn-outline-secondary blotter-page-button" href="{{ route('admin.blotters.index') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>

      @if(!$blotter->case)
        <button class="btn btn-warning text-dark blotter-page-button" data-bs-toggle="modal" data-bs-target="#openCaseModal">
          <i class="bi bi-folder-plus me-1"></i> File Case
        </button>
      @else
        <a class="btn btn-warning text-dark blotter-page-button" href="{{ route('admin.cases.show', $blotter->case) }}">
            <i class="bi bi-folder2-open me-1"></i> View Case
        </a>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif

  <div class="row g-4">
    <!-- Parties Involved -->
    <div class="col-md-12">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 blotter-detail-card">
                <div class="card-header bg-white py-3 blotter-detail-card-header">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-fill me-2"></i>Complainant</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{-- Always use the blotter's saved name --}}
                                {{ strtoupper($blotter->complainant_name) }}
                            </h5>
                            <span class="badge bg-light text-muted border mt-1">
                                {{ $blotter->complainantResident ? 'Resident' : 'Non-Resident' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <small class="text-muted text-uppercase fw-bold d-block">Contact</small>
                            <span>
                                {{-- Use the blotter's saved contact --}}
                                {{ $blotter->complainant_contact ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Email</small>
                            <span>
                                {{-- Use the blotter's saved email --}}
                                {{ $blotter->complainant_email ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 blotter-detail-card">
                <div class="card-header bg-white py-3 blotter-detail-card-header">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-person-x-fill me-2"></i>Respondent</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md bg-danger-subtle text-danger rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-x-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                {{-- Always use the blotter's saved name --}}
                                {{ strtoupper($blotter->respondent_name) }}
                            </h5>
                            <span class="badge bg-light text-muted border mt-1">
                                {{ $blotter->respondentResident ? 'Resident' : 'Non-Resident' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="mb-2">
                            <small class="text-muted text-uppercase fw-bold d-block">Contact</small>
                            <span>
                                {{-- Use the blotter's saved contact --}}
                                {{ $blotter->respondent_contact ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Email</small>
                            <span>
                                {{-- Use the blotter's saved email --}}
                                {{ $blotter->respondent_email ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="col-md-12">
            <div class="card border-0 shadow-sm blotter-detail-card">
                <div class="card-header bg-white py-3 blotter-detail-card-header">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Respondent &amp; Initial Meeting</h6>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.blotters.respondent-meeting.update', $blotter) }}" id="respondentMeetingForm">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 respondent-entry-section">
                                <div class="respondent-field-label mb-2">Respondent Entry <span class="text-muted fw-normal">Choose how to identify the respondent</span></div>
                                @php
                                    $respondentMode = old('respondent_mode', $blotter->respondent_resident_id ? 'resident' : 'manual');
                                @endphp
                                <div class="respondent-mode-options mb-1">
                                    <div class="form-check respondent-mode-option">
                                        <input class="form-check-input respondent-mode" type="radio" name="respondent_mode" id="respondentModeResident" value="resident" @checked($respondentMode === 'resident')>
                                        <label class="form-check-label" for="respondentModeResident">
                                            <i class="bi bi-person-check" aria-hidden="true"></i>
                                            <span><strong>Registered Resident</strong><small>Search the resident directory</small></span>
                                        </label>
                                    </div>
                                    <div class="form-check respondent-mode-option">
                                        <input class="form-check-input respondent-mode" type="radio" name="respondent_mode" id="respondentModeManual" value="manual" @checked($respondentMode === 'manual')>
                                        <label class="form-check-label" for="respondentModeManual">
                                            <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                            <span><strong>Enter Manually</strong><small>Provide respondent details directly</small></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 respondent-field" id="respondentResidentWrap">
                                <label for="respondentResidentSelect" class="form-label">Search Resident</label>
                                <select id="respondentResidentSelect" name="respondent_resident_id" class="form-select">
                                    <option value=""></option>
                                    @foreach($residents as $resident)
                                        <option
                                            value="{{ $resident->id }}"
                                            data-name="{{ trim($resident->last_name . ', ' . $resident->first_name) }}"
                                            data-contact="{{ $resident->contact_no }}"
                                            data-email="{{ $resident->email }}"
                                            data-address="{{ $resident->address_line }}"
                                            @selected((string) old('respondent_resident_id', $blotter->respondent_resident_id) === (string) $resident->id)
                                        >{{ $resident->last_name }}, {{ $resident->first_name }}{{ $resident->middle_name ? ' ' . $resident->middle_name : '' }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Selecting a resident fills in their saved contact details.</div>
                            </div>
                            <div class="col-md-6 respondent-field" id="respondentManualWrap">
                                <label for="respondentName" class="form-label">Respondent Name</label>
                                <input id="respondentName" name="respondent_name" class="form-control" maxlength="255" placeholder="Enter full name" value="{{ old('respondent_name', $blotter->respondent_name) }}">
                            </div>

                            <div class="col-md-4 respondent-field">
                                <label for="respondentContact" class="form-label">Contact Number</label>
                                <input id="respondentContact" name="respondent_contact" class="form-control" maxlength="50" placeholder="e.g. 09XX XXX XXXX" value="{{ old('respondent_contact', $blotter->respondent_contact) }}">
                            </div>
                            <div class="col-md-4 respondent-field">
                                <label for="respondentEmail" class="form-label">Email</label>
                                <input id="respondentEmail" name="respondent_email" type="email" class="form-control" maxlength="255" placeholder="name@example.com" value="{{ old('respondent_email', $blotter->respondent_email) }}">
                            </div>
                            <div class="col-md-4 respondent-field">
                                <label for="respondentAddress" class="form-label">Address / Known Location</label>
                                <input id="respondentAddress" name="respondent_address_line" class="form-control" maxlength="255" placeholder="Street, barangay, or landmark" value="{{ old('respondent_address_line', $blotter->respondent_address_line) }}">
                            </div>

                            <div class="col-md-6 respondent-meeting-field">
                                <label for="initialMeeting" class="form-label">Initial Meeting Date &amp; Time</label>
                                <input id="initialMeeting" name="initial_meeting" type="datetime-local" class="form-control" value="{{ old('initial_meeting', $blotter->initial_meeting?->format('Y-m-d\\TH:i')) }}">
                                <div class="form-text"><i class="bi bi-info-circle me-1" aria-hidden="true"></i>The Patawag Complainant document uses the Barangay Hall as its default location.</div>
                            </div>
                            <div class="col-12">
                                <div class="respondent-form-actions">
                                    <div class="respondent-action-note"><i class="bi bi-shield-check" aria-hidden="true"></i><span>Review the details before saving or generating the Patawag document.</span></div>
                                    <div class="d-flex flex-wrap justify-content-end gap-2 respondent-action-buttons">
                                        <button type="submit" name="_action" value="save" class="btn btn-primary blotter-page-button">
                                            <i class="bi bi-save me-1"></i>Save Details
                                        </button>
                                        <button type="submit" name="_action" value="download_patawag" class="btn btn-warning blotter-page-button">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>Save &amp; Download Patawag PDF
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Incident Details -->
        <div class="col-md-12">
        <div class="card border-0 shadow-sm blotter-detail-card">
            <div class="card-header bg-white py-3 blotter-detail-card-header">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-file-text-fill me-2"></i>Incident Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-4 incident-detail-group">
                    <label class="incident-detail-label"><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Incident Location</label>
                    <div class="incident-location-panel d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                        <span>{{ $blotter->incident_location }}</span>
                    </div>
                </div>

                <div class="mb-4 incident-detail-group">
                    <label class="incident-detail-label"><i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>Statement</label>
                    <div class="incident-statement-panel p-3 bg-light rounded border" role="region" aria-label="Incident statement">
                        {{ $blotter->narrative }}
                    </div>
                </div>

                @if($blotter->remarks)
                <div class="incident-detail-group">
                    <label class="incident-detail-label"><i class="bi bi-sticky me-1" aria-hidden="true"></i>Remarks</label>
                    <div class="incident-statement-panel incident-remarks-panel p-3 bg-light rounded border">
                        {{ $blotter->remarks }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
  </div>
@endsection

@push('modals')
@if(!$blotter->case)
  <div class="modal fade" id="openCaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-folder-plus me-2"></i>File Case
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-3">
            <div class="mb-3">
              <i class="bi bi-exclamation-circle-fill text-warning" style="font-size: 3rem;"></i>
            </div>
            <p class="mb-0">Are you sure you want to open a case for this blotter?</p>
          </div>
          <div class="detail-card">
            <div class="detail-item">
              <span class="detail-label">Blotter No</span>
              <span class="detail-value">{{ $blotter->blotter_no }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Incident</span>
              <span class="detail-value">{{ $blotter->incident_type }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Date</span>
              <span class="detail-value">{{ $blotter->incident_date->format('M d, Y') }}</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form method="POST" action="{{ route('admin.blotters.openCase', $blotter) }}">
            @csrf
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-folder-plus me-1"></i> File Case
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endif

<style>
.admin-blotter-detail .blotter-detail-header {
  gap: 1rem;
  padding: 1.1rem 1.25rem;
  background: linear-gradient(110deg, #f8fbff 0%, #ffffff 72%);
  border: 1px solid #e5edf7;
  border-left: 4px solid #0d47a1;
  border-radius: 14px;
}

.admin-blotter-detail .blotter-detail-header h4 {
  font-size: 1.35rem;
}

.admin-blotter-detail .blotter-detail-header .badge {
  padding: 0.45rem 0.65rem;
  color: #334155 !important;
  background: #f1f5f9 !important;
  border-color: #dbe3ed !important;
  font-weight: 600;
}

.admin-blotter-detail .blotter-detail-card {
  overflow: hidden;
  border: 1px solid #e8edf3 !important;
  border-radius: 14px;
  box-shadow: 0 5px 18px rgba(15, 23, 42, 0.055) !important;
  transition: box-shadow 160ms ease, border-color 160ms ease;
}

.admin-blotter-detail .blotter-detail-card:hover {
  border-color: #d6e2f1 !important;
  box-shadow: 0 9px 24px rgba(15, 23, 42, 0.09) !important;
}

.admin-blotter-detail .blotter-detail-card-header {
  border-bottom: 1px solid #edf1f5;
  background: linear-gradient(90deg, #f8fbff, #ffffff) !important;
}

.admin-blotter-detail .blotter-detail-card-header h6 {
  display: flex;
  align-items: center;
  min-height: 1.35rem;
}

.admin-blotter-detail .avatar {
  flex: 0 0 auto;
  box-shadow: inset 0 0 0 1px rgba(13, 71, 161, 0.08);
}

.admin-blotter-detail .blotter-detail-card .border-top {
  border-color: #edf1f5 !important;
}

.admin-blotter-detail .blotter-detail-card .border-top small {
  margin-bottom: 0.2rem;
  font-size: 0.7rem;
  letter-spacing: 0.045em;
}

.admin-blotter-detail .blotter-page-button {
  min-height: 40px;
  padding: 0.55rem 0.9rem;
  border-radius: 9px;
  font-weight: 600;
  transition: transform 140ms ease, box-shadow 140ms ease, background-color 140ms ease;
}

.admin-blotter-detail .blotter-page-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 12px rgba(15, 23, 42, 0.12);
}

.admin-blotter-detail .blotter-page-button:focus-visible {
  outline: 3px solid rgba(13, 71, 161, 0.24);
  outline-offset: 2px;
}

.admin-blotter-detail .form-label {
  margin-bottom: 0.4rem;
}

.admin-blotter-detail .form-control,
.admin-blotter-detail .form-select {
  min-height: 42px;
  border-color: #d8e0ea;
  border-radius: 9px;
}

.admin-blotter-detail .form-control:focus,
.admin-blotter-detail .form-select:focus {
  border-color: #7aa7e8;
  box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.12);
}

.admin-blotter-detail .form-check {
  padding: 0.55rem 0.75rem 0.55rem 2rem;
  background: #f8fafc;
  border: 1px solid #e6ebf1;
  border-radius: 9px;
}

.admin-blotter-detail #respondentMeetingForm {
  --respondent-label: #334155;
}

.admin-blotter-detail .respondent-entry-section {
  padding-bottom: 0.25rem;
}

.admin-blotter-detail .respondent-field-label,
.admin-blotter-detail #respondentMeetingForm .form-label,
.admin-blotter-detail .incident-detail-label {
  display: block;
  color: var(--respondent-label, #334155);
  font-size: 0.84rem;
  font-weight: 700;
}

.admin-blotter-detail .respondent-field-label > span {
  margin-left: 0.35rem;
  font-size: 0.78rem;
}

.admin-blotter-detail .respondent-mode-options {
  display: grid;
  max-width: 660px;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.admin-blotter-detail .respondent-mode-option {
  position: relative;
  padding: 0;
  overflow: hidden;
  transition: border-color 140ms ease, background-color 140ms ease, box-shadow 140ms ease;
}

.admin-blotter-detail .respondent-mode-option .form-check-input {
  position: absolute;
  margin: 1rem 0 0 0.9rem;
}

.admin-blotter-detail .respondent-mode-option .form-check-label {
  display: flex;
  min-height: 68px;
  align-items: center;
  gap: 0.75rem;
  padding: 0.7rem 0.8rem 0.7rem 2.6rem;
  cursor: pointer;
}

.admin-blotter-detail .respondent-mode-option .form-check-label > i {
  display: inline-flex;
  width: 2rem;
  height: 2rem;
  flex: 0 0 auto;
  align-items: center;
  justify-content: center;
  color: #0d47a1;
  background: #eaf2ff;
  border-radius: 9px;
  font-size: 1rem;
}

.admin-blotter-detail .respondent-mode-option .form-check-label span {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.admin-blotter-detail .respondent-mode-option .form-check-label strong {
  color: #334155;
  font-size: 0.86rem;
}

.admin-blotter-detail .respondent-mode-option .form-check-label small {
  color: #64748b;
  font-size: 0.75rem;
}

.admin-blotter-detail .respondent-mode-option:has(.form-check-input:checked) {
  background: #f1f6ff;
  border-color: #8db4ed;
  box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.08);
}

.admin-blotter-detail #respondentMeetingForm .form-label {
  margin-bottom: 0.45rem;
}

.admin-blotter-detail #respondentMeetingForm .form-control,
.admin-blotter-detail #respondentMeetingForm .form-select,
.admin-blotter-detail #respondentMeetingForm .select2-container--bootstrap-5 .select2-selection {
  min-height: 45px;
  padding: 0.62rem 0.8rem;
  color: #26364b;
  background-color: #ffffff;
  border: 1px solid #d6e0eb;
  border-radius: 9px;
  font-size: 0.92rem;
  transition: border-color 140ms ease, box-shadow 140ms ease;
}

.admin-blotter-detail #respondentMeetingForm .form-control::placeholder {
  color: #9aa8b8;
}

.admin-blotter-detail #respondentMeetingForm .form-control:focus,
.admin-blotter-detail #respondentMeetingForm .form-select:focus,
.admin-blotter-detail #respondentMeetingForm .select2-container--bootstrap-5.select2-container--focus .select2-selection {
  border-color: #7aa7e8;
  box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.12);
}

.admin-blotter-detail #respondentMeetingForm .select2-container--bootstrap-5 .select2-selection--single {
  display: flex;
  align-items: center;
}

.admin-blotter-detail #respondentMeetingForm .select2-container--bootstrap-5 .select2-selection__rendered {
  padding-left: 0;
  color: #26364b;
}

.admin-blotter-detail #respondentMeetingForm .form-text {
  margin-top: 0.45rem;
  color: #718096;
  font-size: 0.78rem;
  line-height: 1.45;
}

.admin-blotter-detail .respondent-form-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-top: 0.25rem;
  padding-top: 1rem;
  border-top: 1px solid #edf1f5;
}

.admin-blotter-detail .respondent-action-note {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #718096;
  font-size: 0.8rem;
}

.admin-blotter-detail .respondent-action-note i {
  color: #0d47a1;
  font-size: 1rem;
}

.admin-blotter-detail .respondent-action-buttons .btn-primary {
  box-shadow: 0 4px 10px rgba(13, 71, 161, 0.14);
}

.admin-blotter-detail .respondent-action-buttons .btn-warning {
  color: #3c2e00;
  box-shadow: 0 4px 10px rgba(255, 193, 7, 0.18);
}

.admin-blotter-detail .respondent-action-buttons .btn-warning:hover {
  background-color: #ffca2c;
}

.admin-blotter-detail .incident-detail-label {
  margin-bottom: 0.55rem;
  color: #526277;
  letter-spacing: 0.025em;
}

.admin-blotter-detail .incident-location-panel {
  min-height: 58px;
  padding: 0.9rem 1.05rem;
  background: linear-gradient(100deg, #f7faff, #fbfdff);
  border: 1px solid #e2eaf4;
  border-radius: 11px;
}

.admin-blotter-detail .incident-location-panel > span {
  color: #25364b;
  font-size: 0.98rem;
  font-weight: 600;
  line-height: 1.5;
  overflow-wrap: anywhere;
}

.admin-blotter-detail .incident-statement-panel {
  min-height: 125px;
  padding: 1.1rem 1.2rem !important;
  color: #334155;
  background: linear-gradient(145deg, #f8fafc, #fbfdff) !important;
  border: 1px solid #e2eaf4 !important;
  border-left: 3px solid #8db4ed !important;
  border-radius: 11px !important;
  font-size: 0.94rem;
  line-height: 1.8;
  overflow-wrap: anywhere;
  white-space: pre-line;
}

.admin-blotter-detail .incident-remarks-panel {
  min-height: auto;
  background: #fffaf2 !important;
  border-color: #f3e5c6 !important;
  border-left-color: #e5b94f !important;
}

#openCaseModal .modal-content {
  border: none;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

#openCaseModal .modal-header {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  color: white;
  border: none;
  padding: 20px 24px;
}

#openCaseModal .modal-header .btn-close {
  filter: invert(1);
  opacity: 0.8;
}

#openCaseModal .modal-header .btn-close:hover {
  opacity: 1;
}

#openCaseModal .modal-title {
  font-weight: 600;
  display: flex;
  align-items: center;
}

#openCaseModal .modal-body {
  padding: 24px;
}

#openCaseModal .detail-card {
  background: #f8fafc;
  border-radius: 12px;
  padding: 16px;
}

#openCaseModal .detail-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #e2e8f0;
}

#openCaseModal .detail-item:last-child {
  border-bottom: none;
}

#openCaseModal .detail-label {
  color: #64748b;
  font-size: 0.875rem;
}

#openCaseModal .detail-value {
  font-weight: 600;
  color: #1e293b;
}

#openCaseModal .modal-footer {
  border-top: 1px solid #e9ecef;
  padding: 16px 24px;
}

#openCaseModal .btn {
  border-radius: 12px;
  padding: 10px 20px;
  font-weight: 600;
}

#openCaseModal .btn-secondary {
  background: #e2e8f0;
  border: none;
  color: #475569;
}

#openCaseModal .btn-secondary:hover {
  background: #cbd5e1;
}

#openCaseModal .btn-primary {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  border: none;
  box-shadow: 0 4px 15px rgba(16, 85, 201, 0.3);
}

#openCaseModal .btn-primary:hover {
  background: linear-gradient(135deg, #0d47a1 0%, #0a3d8f 100%);
  box-shadow: 0 6px 20px rgba(16, 85, 201, 0.4);
}

@media (max-width: 767.98px) {
  .admin-blotter-detail .blotter-detail-header {
    align-items: stretch !important;
    flex-direction: column;
    padding: 1rem;
  }

  .admin-blotter-detail .blotter-detail-actions {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .admin-blotter-detail .blotter-detail-actions .blotter-page-button {
    width: 100%;
  }

  .admin-blotter-detail #respondentMeetingForm .respondent-form-actions {
    align-items: stretch;
    flex-direction: column;
  }

  .admin-blotter-detail #respondentMeetingForm .respondent-action-buttons {
    display: grid;
    grid-template-columns: 1fr;
  }

  .admin-blotter-detail #respondentMeetingForm .respondent-action-buttons .btn {
    width: 100%;
  }

  .admin-blotter-detail .respondent-mode-options {
    grid-template-columns: 1fr;
  }
}

@media (prefers-reduced-motion: reduce) {
  .admin-blotter-detail .blotter-detail-card,
  .admin-blotter-detail .blotter-page-button,
  .admin-blotter-detail .respondent-mode-option,
  .admin-blotter-detail #respondentMeetingForm .form-control,
  .admin-blotter-detail #respondentMeetingForm .form-select {
    transition: none;
  }

  .admin-blotter-detail .blotter-page-button:hover {
    transform: none;
  }
}
</style>
@endpush

@push('scripts')
<script>
  $(function () {
    const $residentSelect = $('#respondentResidentSelect');
    const $residentWrap = $('#respondentResidentWrap');
    const $manualWrap = $('#respondentManualWrap');
    const $name = $('#respondentName');
    const $modeInputs = $('.respondent-mode');

    $residentSelect.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Search Registered Resident',
      allowClear: true
    });

    const syncRespondentMode = () => {
      const isResident = $('#respondentModeResident').is(':checked');
      $residentWrap.toggle(isResident);
      $manualWrap.toggle(!isResident);
      $residentSelect.prop('disabled', !isResident).prop('required', isResident);
      $name.prop('required', !isResident);

      if (!isResident && $residentSelect.val()) {
        $residentSelect.val(null).trigger('change');
      }
    };

    $residentSelect.on('change', function () {
      const resident = this.selectedOptions[0];
      if (!resident || !resident.value) return;

      $name.val(resident.dataset.name || '');
      $('#respondentContact').val(resident.dataset.contact || '');
      $('#respondentEmail').val(resident.dataset.email || '');
      $('#respondentAddress').val(resident.dataset.address || '');
    });

    $modeInputs.on('change', syncRespondentMode);
    syncRespondentMode();
  });
</script>
@endpush
