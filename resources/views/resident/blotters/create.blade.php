@extends('layouts.resident', ['currentRoute' => 'resident.blotters.create'])

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">Online E-Blotter</h2>
      <p class="opacity-75 mb-0">Submit an incident report or complaint for Barangay review.</p>
    </div>
    <a href="{{ route('resident.dashboard') }}" class="btn btn-glass"><i class="bi bi-arrow-left me-2"></i>Dashboard</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success" role="status">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger" role="alert">
      <div class="fw-bold mb-1">Please check the information below.</div>
      <ul class="mb-0">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('resident.blotters.store') }}" id="residentBlotterForm">
    @csrf
    <div class="row g-4">
      <div class="col-lg-7">
        <section class="glass p-4 h-100">
          <h5 class="mb-4"><i class="bi bi-exclamation-triangle me-2"></i>Incident Information</h5>
          <div class="mb-3">
            <label for="incident_date" class="form-label">Incident Date &amp; Time <span class="text-warning">*</span></label>
            <input id="incident_date" type="datetime-local" name="incident_date" class="form-control" value="{{ old('incident_date', now()->format('Y-m-d\TH:i')) }}" required>
            @error('incident_date')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="incident_type" class="form-label">Incident Type <span class="text-warning">*</span></label>
            <select id="incident_type" name="incident_type" class="form-select incident-type-select" required>
              <option value="" disabled @selected(!old('incident_type'))>-- Select Incident Type --</option>
              @include('components.incident-type-options')
            </select>
            @error('incident_type')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="incident_location" class="form-label">Incident Location <span class="text-warning">*</span></label>
            <input id="incident_location" name="incident_location" class="form-control" value="{{ old('incident_location', 'Barangay Bucandala 1, Imus, Cavite') }}" maxlength="255" required>
            @error('incident_location')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="narrative" class="form-label">Statement <span class="text-warning">*</span></label>
            <textarea id="narrative" name="narrative" rows="7" class="form-control" placeholder="Describe what happened, including relevant details." required>{{ old('narrative') }}</textarea>
            @error('narrative')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
        </section>
      </div>

      <div class="col-lg-5">
        <section class="glass p-4 h-100">
          <h5 class="mb-2"><i class="bi bi-person-x me-2"></i>Respondent</h5>
          <p class="small opacity-75 mb-4">Enter any details you know. You may leave these fields blank if the respondent is unknown.</p>
          <div class="mb-3">
            <label for="respondent_name" class="form-label">Name, Partial Name, or Alias</label>
            <input id="respondent_name" name="respondent_name" class="form-control" value="{{ old('respondent_name') }}" maxlength="255">
            @error('respondent_name')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          {{--
          <div class="mb-3">
            <label for="respondent_contact" class="form-label">Contact Number</label>
            <input id="respondent_contact" name="respondent_contact" class="form-control" value="{{ old('respondent_contact') }}" maxlength="50">
            @error('respondent_contact')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="respondent_email" class="form-label">Email</label>
            <input id="respondent_email" type="email" name="respondent_email" class="form-control" value="{{ old('respondent_email') }}" maxlength="255">
            @error('respondent_email')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          --}}
          <div class="mb-3">
            <label for="respondent_address_line" class="form-label">Location</label>
            <textarea id="respondent_address_line" name="respondent_address_line" class="form-control" rows="4" maxlength="255" placeholder="Add a location guide, such as a street, landmark, nearby store, or recognizable building.">{{ old('respondent_address_line') }}</textarea>
            @error('respondent_address_line')<div class="small text-warning mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="glass p-3 small mb-0" style="background: rgba(13, 71, 161, 0.78); border-color: rgba(255, 215, 0, 0.3); color: #ffffff;">
            Your report is initially marked <strong style="color: #ffffff;">Filed</strong>. Barangay staff will review it and open a case if it qualifies for case tracking.
          </div>
        </section>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
      <a href="{{ route('resident.dashboard') }}" class="btn btn-glass">Cancel</a>
      <button id="submitBlotterButton" type="submit" class="btn btn-warning fw-semibold">
        <i class="bi bi-send me-2"></i><span>Submit Report</span>
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('residentBlotterForm').addEventListener('submit', function () {
    const button = document.getElementById('submitBlotterButton');
    button.disabled = true;
    button.querySelector('span').textContent = 'Submitting...';
    button.insertAdjacentHTML('afterbegin', '<span class="spinner-border spinner-border-sm me-2" role="status" aria-label="Submitting"></span>');
  });
</script>
@endpush

@push('styles')
<style>
  .incident-type-select {
    color-scheme: dark;
  }

  .incident-type-select option {
    color: #ffffff;
    background-color: #1055c9;
  }

  .incident-type-select optgroup {
    color: #ffe44d;
    background-color: #0d47a1;
  }
</style>
@endpush
