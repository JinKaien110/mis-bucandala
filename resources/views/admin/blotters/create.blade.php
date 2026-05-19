<div class="modal fade" id="createBlotterModal" tabindex="-1" aria-labelledby="createBlotterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-white border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold" id="createBlotterModalLabel">
          <i class="bi bi-journal-plus text-primary me-2"></i>Create New Blotter
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="{{ route('admin.blotters.store') }}" id="blotterModalForm">
        @csrf
        <div class="modal-body py-4">
          <style>
            .select2-container--bootstrap5 .select2-results__option {
              padding: 10px 12px;
              text-align: left !important;
            }
            .select2-container--bootstrap5 .select2-results__group {
              padding: 10px 12px;
              font-weight: 600;
              background-color: #e9ecef;
              text-align: left;
            }
            .select2-container--bootstrap5 .select2-selection__rendered {
              text-align: left;
            }
          </style>
          <div class="row g-4">
            
            <div class="col-md-7">
              <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                  <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle-fill me-2"></i>Incident Information</h6>
                </div>
                <div class="card-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label small text-muted fw-bold">Incident Date & Time <span class="text-danger">*</span></label>
                      <input type="datetime-local" name="incident_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small text-muted fw-bold">Incident Type <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                        <select name="incident_type" class="form-select border-start-0" required style="cursor: pointer;">
                          <option value="" selected disabled>-- Select Incident Type --</option>
                          <optgroup label="Personal Offenses">
                            <option value="Threats / Harassment">⚠️ Threats / Harassment</option>
                            <option value="Physical Injury / Assault">👊 Physical Injury / Assault</option>
                            <option value="Domestic Dispute">🏠 Domestic Dispute</option>
                            <option value="Verbal Altercation">🗣️ Verbal Altercation</option>
                            <option value="Child Abuse">👶 Child Abuse</option>
                            <option value="Violence Against Women">👩 Violence Against Women</option>
                          </optgroup>
                          <optgroup label="Property Crimes">
                            <option value="Theft / Robbery">💰 Theft / Robbery</option>
                            <option value="Property Damage">🏗️ Property Damage</option>
                            <option value="Trespassing">🚫 Trespassing</option>
                          </optgroup>
                          <optgroup label="Public Nuisance">
                            <option value="Noise Disturbance">🔊 Noise Disturbance</option>
                            <option value="Illegal Gambling">🎰 Illegal Gambling</option>
                            <option value="Drunk in Public">🍺 Drunk in Public</option>
                            <option value="Stray Animal">🐕 Stray Animal</option>
                            <option value="Public Hazard">⚡ Public Hazard</option>
                          </optgroup>
                          <optgroup label="Disputes">
                            <option value="Traffic Accident">🚗 Traffic Accident</option>
                            <option value="Land / Boundary Dispute">🏞️ Land / Boundary Dispute</option>
                            <option value="Scam / Estafa">💳 Scam / Estafa</option>
                          </optgroup>
                          <optgroup label="Environmental">
                            <option value="Illegal Logging">🌲 Illegal Logging</option>
                            <option value="Illegal Fishing">🎣 Illegal Fishing</option>
                            <option value="Water Pollution">💧 Water Pollution</option>
                            <option value="Illegal Dumping">🗑️ Illegal Dumping</option>
                            <option value="Unsanitary Premises">🧹 Unsanitary Premises</option>
                          </optgroup>
                          <optgroup label="Health & Safety">
                            <option value="Animal Bite">🦇 Animal Bite</option>
                            <option value="Health Code Violation">🏥 Health Code Violation</option>
                            <option value="Business Permit Violation">📜 Business Permit Violation</option>
                          </optgroup>
                          <optgroup label="Other">
                            <option value="Cybercrime">💻 Cybercrime</option>
                            <option value="Squatters / Illegal Settlers">🏚️ Squatters / Illegal Settlers</option>
                            <option value="Missing Person">🔍 Missing Person</option>
                            <option value="Found Property">📦 Found Property</option>
                            <option value="Other">❓ Other</option>
                          </optgroup>
                        </select>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted fw-bold">Incident Location <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                        <input name="incident_location" class="form-control border-start-0" value="Barangay Bucandala 1, Imus, Cavite" required>
                      </div>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted fw-bold">Narrative <span class="text-danger">*</span></label>
                      <textarea name="narrative" rows="6" class="form-control" placeholder="Describe the incident..." required></textarea>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted fw-bold">Remarks</label>
                      <textarea name="remarks" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-5">
              
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                  <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-fill me-2"></i>Complainant</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3 d-flex justify-content-between align-items-center">
                    <label class="form-label small text-muted fw-bold mb-0">Entry Mode</label>
                    <div class="d-flex gap-3">
                      <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="complainant_mode" id="c_mode_resident" value="resident" checked>
                        <label class="form-check-label small" for="c_mode_resident">Resident</label>
                      </div>
                      <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="complainant_mode" id="c_mode_manual" value="manual">
                        <label class="form-check-label small" for="c_mode_manual">Manual</label>
                      </div>
                    </div>
                  </div>

                  <div id="c_resident_wrap">
                    <div class="mb-3">
                      <select name="complainant_resident_id" class="form-select resident-select-modal" id="c_resident_select" data-placeholder="Search Registered Resident">
                        <option value=""></option>
                        @foreach(($residents ?? []) as $r)
                          <option value="{{ $r->id }}" data-email="{{ $r->email }}" data-contact="{{ $r->contact_no }}">
                            {{ $r->last_name }}, {{ $r->first_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label mini-label">Contact No.</label>
                        <input type="text" name="complainant_contact_res" id="c_res_contact" class="form-control form-control-sm" placeholder="Auto-filled">
                      </div>
                      <div class="col-6">
                        <label class="form-label mini-label">Email</label>
                        <input type="email" name="complainant_email_res" id="c_res_email" class="form-control form-control-sm" placeholder="Auto-filled">
                      </div>
                    </div>
                  </div>

                  <div id="c_manual_wrap" style="display:none;">
                    <div class="mb-3">
                      <input name="complainant_name" class="form-control" placeholder="Enter Full Name">
                    </div>
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label mini-label">Contact No.</label>
                        <input name="complainant_contact" class="form-control form-control-sm" placeholder="Contact No.">
                      </div>
                      <div class="col-6">
                        <label class="form-label mini-label">Email</label>
                        <input name="complainant_email" class="form-control form-control-sm" placeholder="Email Address">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                  <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-x-fill me-2"></i>Respondent</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3 d-flex justify-content-between align-items-center">
                    <label class="form-label small text-muted fw-bold mb-0">Entry Mode</label>
                    <div class="d-flex gap-3">
                      <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="respondent_mode" id="r_mode_resident" value="resident" checked>
                        <label class="form-check-label small" for="r_mode_resident">Resident</label>
                      </div>
                      <div class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="radio" name="respondent_mode" id="r_mode_manual" value="manual">
                        <label class="form-check-label small" for="r_mode_manual">Manual</label>
                      </div>
                    </div>
                  </div>

                  <div id="r_resident_wrap">
                    <div class="mb-3">
                      <select name="respondent_resident_id" class="form-select resident-select-modal" id="r_resident_select" data-placeholder="Search Registered Resident">
                        <option value=""></option>
                        @foreach(($residents ?? []) as $r)
                          <option value="{{ $r->id }}" data-email="{{ $r->email }}" data-contact="{{ $r->contact_no }}">
                            {{ $r->last_name }}, {{ $r->first_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label mini-label">Contact No.</label>
                        <input type="text" name="respondent_contact_res" id="r_res_contact" class="form-control form-control-sm" placeholder="Auto-filled">
                      </div>
                      <div class="col-6">
                        <label class="form-label mini-label">Email</label>
                        <input type="email" name="respondent_email_res" id="r_res_email" class="form-control form-control-sm" placeholder="Auto-filled">
                      </div>
                    </div>
                  </div>

                  <div id="r_manual_wrap" style="display:none;">
                    <div class="mb-3">
                      <input name="respondent_name" class="form-control" placeholder="Enter Full Name">
                    </div>
                    <div class="row g-2">
                      <div class="col-6">
                        <label class="form-label mini-label">Contact No.</label>
                        <input name="respondent_contact" class="form-control form-control-sm" placeholder="Contact No.">
                      </div>
                      <div class="col-6">
                        <label class="form-label mini-label">Email</label>
                        <input name="respondent_email" class="form-control form-control-sm" placeholder="Email Address">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="modal-footer bg-light border-top-0 py-3">
          <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
            <i class="bi bi-save me-2"></i>Save Blotter Record
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  /* Fix for scrollable modal footer */
  .modal-dialog-scrollable .modal-content {
    max-height: 90vh;
  }

  /* Force Left Alignment for the Incident Type Select and Optgroups */
  select[name="incident_type"], 
  select[name="incident_type"] optgroup, 
  select[name="incident_type"] option {
    text-align: left !important;
    padding-left: 10px;
  }

  /* Reset appearance for some browsers that center-align select text */
  select.form-select {
    text-align: left !important;
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
  }

  /* Label Styling */
  .mini-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 700;
    margin-bottom: 2px;
  }

  /* Select2 Vertical Centering Fix (Bootstrap 5) */
  .select2-container--bootstrap-5 .select2-selection--single {
    height: 38px !important; 
    display: flex !important;
    align-items: center !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 6px !important;
    text-align: left !important;
  }

  .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: normal !important; 
    padding-left: 12px !important;
    color: #495057 !important;
    display: block !important;
    margin-top: 0 !important;
    text-align: left !important;
  }

  /* Ensure dropdown options are also left-aligned */
  .select2-results__option {
    text-align: left !important;
  }
</style>

<script>
// Logic to handle Mode Switching & Auto-fill
document.addEventListener('DOMContentLoaded', function() {
  // Replace your existing toggleMode function with this:
function toggleMode(prefix) {
    const isResident = document.getElementById(`${prefix}_mode_resident`).checked;
    const resWrap = document.getElementById(`${prefix}_resident_wrap`);
    const manWrap = document.getElementById(`${prefix}_manual_wrap`);

    resWrap.style.display = isResident ? 'block' : 'none';
    manWrap.style.display = isResident ? 'none' : 'block';

    const resSelect = resWrap.querySelector('select');
    const manInput = manWrap.querySelector('input');
    
    // Get the auto-fill inputs
    const resContact = document.getElementById(`${prefix}_res_contact`);
    const resEmail = document.getElementById(`${prefix}_res_email`);

    if (isResident) {
        resSelect.setAttribute('required', 'required');
        manInput.removeAttribute('required');
        // Ensure auto-fill inputs are enabled so they submit
        if(resContact) resContact.disabled = false;
        if(resEmail) resEmail.disabled = false;
    } else {
        manInput.setAttribute('required', 'required');
        resSelect.removeAttribute('required');
        $(resSelect).val(null).trigger('change');
    }
}

  ['c', 'r'].forEach(p => {
    document.getElementsByName(`${p === 'c' ? 'complainant' : 'respondent'}_mode`).forEach(radio => {
      radio.addEventListener('change', () => toggleMode(p));
    });

    // Auto-fill logic
    $(`#${p}_resident_select`).on('select2:select', function(e) {
      const data = e.params.data.element.dataset;
      document.getElementById(`${p}_res_contact`).value = data.contact || '';
      document.getElementById(`${p}_res_email`).value = data.email || '';
    });
  });
});
</script>