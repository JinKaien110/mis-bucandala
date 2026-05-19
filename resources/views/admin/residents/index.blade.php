@extends('layouts.admin')

@section('title', 'Residents - Barangay MIS')

@section('content')
@php
  // RBAC: change this based on your real roles
  $role = auth()->user()->role ?? 'staff';

  $canToggle = $role !== 'staff'; // staff cannot activate/deactivate
  $canEdit   = true;             // staff can edit
  $canCreate = true;             // keep allowed
@endphp

<div class="page-surface">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Residents</h4>
      <p class="text-muted mb-0">Masterlist of barangay residents</p>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" id="btnReload" type="button">
        <i class="bi bi-arrow-clockwise me-1"></i> Reload
      </button>
      <button class="btn btn-primary" id="btnOpenCreate" type="button" @disabled(!$canCreate)>
        <i class="bi bi-plus-lg me-1"></i> Add Resident
      </button>
    </div>
  </div>

  {{-- Flash Messages --}}
  <div id="message" class="alert" style="display:none;"></div>

  @if (session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Error</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Search & Filter -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input id="search" type="text" class="form-control border-start-0 ps-0" placeholder="Search by name, address, contact, or email...">
                </div>
            </div>
            <div class="col-md-4 text-md-end text-muted small">
                <span class="badge bg-light text-dark border me-2">Role: {{ strtoupper($role) }}</span>
                <span>Press <strong>Enter</strong> to search</span>
            </div>
        </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-uppercase small text-muted">
            <tr>
              <th class="px-4 py-3 border-0">ID</th>
              <th class="py-3 border-0">Resident</th>
              <th class="py-3 border-0">Address</th>
              <th class="py-3 border-0">Contact</th>
              <th class="py-3 border-0">Verification</th>
              <th class="py-3 border-0">Status</th>
              <th class="py-3 border-0">ID Photo</th>
              <th class="py-3 border-0 text-end pe-4">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody">
            <tr><td colspan="8" class="text-center py-5 text-muted">Loading residents...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top-0 py-3">
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Photos are stored in <code>storage/app/public</code>
        </div>
    </div>
  </div>

</div>

{{-- PHOTO EXPAND MODAL --}}
<div class="modal fade" id="photoExpandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center p-0">
        <img id="expandedPhoto" src="" alt="Expanded Photo" class="img-fluid" style="max-height: 70vh; object-fit: contain;">
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <a id="downloadPhotoLink" href="" download class="btn btn-outline-light">
          <i class="bi bi-download me-1"></i> Download
        </a>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- CREATE MODAL --}}
<style>
.photo-expand-clickable { cursor: pointer; transition: transform 0.2s; }
.photo-expand-clickable:hover { transform: scale(1.1); }
.create-modal .dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
    transition: all 0.3s ease;
    text-align: center;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.create-modal .dropzone:hover {
    border-color: #1055C9;
    background: linear-gradient(135deg, #f0f5ff 0%, #e8efff 100%);
}
.create-modal .dropzone.has-file {
    padding: 10px;
    border-color: #1055C9;
    background: #f0f5ff;
}
.create-modal .dropzone-icon {
    font-size: 28px;
    color: #1055C9;
    margin-bottom: 6px;
}
.create-modal .dropzone-text {
    color: #6b7280;
    font-size: 0.85rem;
}
.create-modal .dropzone-hint {
    color: #9ca3af;
    font-size: 0.7rem;
    margin-top: 2px;
}
.create-modal .form-control:focus {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25) !important;
}
.create-modal .form-control:invalid:focus {
    border-color: #dc2626 !important;
}
</style>
<div class="modal fade create-modal" id="createModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">Add Resident</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('admin.residents.store') }}" enctype="multipart/form-data" id="createForm">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small text-muted fw-bold">Full Name <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input name="first_name" class="form-control" placeholder="First name" value="{{ old('first_name') }}" required @disabled(!$canCreate)>
                        </div>
                        <div class="col-4">
                            <input name="middle_name" class="form-control" placeholder="Middle name" value="{{ old('middle_name') }}" @disabled(!$canCreate)>
                        </div>
                        <div class="col-4">
                            <input name="last_name" class="form-control" placeholder="Last name" value="{{ old('last_name') }}" required @disabled(!$canCreate)>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Sex <span class="text-danger">*</span></label>
                    <select name="sex" class="form-select" required @disabled(!$canCreate)>
                        <option value="">-- Select --</option>
                        <option value="male" {{ old('sex')==='male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex')==='female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Birth Date</label>
                    <input name="birth_date" type="date" class="form-control" value="{{ old('birth_date') }}" @disabled(!$canCreate)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Civil Status</label>
                    <select name="civil_status" class="form-select" @disabled(!$canCreate)>
                        <option value="">-- Select --</option>
                        <option value="single" {{ old('civil_status')==='single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('civil_status')==='married' ? 'selected' : '' }}>Married</option>
                        <option value="widowed" {{ old('civil_status')==='widowed' ? 'selected' : '' }}>Widowed</option>
                        <option value="separated" {{ old('civil_status')==='separated' ? 'selected' : '' }}>Separated</option>
                        <option value="divorced" {{ old('civil_status')==='divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Street <span class="text-danger">*</span></label>
                    <input name="street" class="form-control" value="{{ old('street') }}" placeholder="Street name" required @disabled(!$canCreate)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Phase <span class="text-danger">*</span></label>
                    <input name="phase" class="form-control" value="{{ old('phase') }}" placeholder="Phase 1, 2, 3..." required @disabled(!$canCreate)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Address Line <span class="text-danger">*</span></label>
                    <input name="address_line" class="form-control" value="{{ old('address_line') }}" placeholder="Block/Lot, Purok, Barangay..." required @disabled(!$canCreate)>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Contact No.</label>
                    <div class="input-group">
                        <span class="input-group-text" style="background: white;">+63</span>
                        <input type="text" name="contact_no" id="c_contact_no" class="form-control" value="{{ old('contact_no') }}" placeholder="917 123 4567" maxlength="12" style="border-left: 0;" @disabled(!$canCreate)>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email') }}" @disabled(!$canCreate)>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Occupation</label>
                    <select name="occupation" class="form-select" @disabled(!$canCreate)>
                        <option value="">-- Select --</option>
                        <option value="Student" {{ old('occupation')==='Student' ? 'selected' : '' }}>Student</option>
                        <option value="Employed" {{ old('occupation')==='Employed' ? 'selected' : '' }}>Employed</option>
                        <option value="Self-employed" {{ old('occupation')==='Self-employed' ? 'selected' : '' }}>Self-employed</option>
                        <option value="Unemployed" {{ old('occupation')==='Unemployed' ? 'selected' : '' }}>Unemployed</option>
                        <option value="OFW" {{ old('occupation')==='OFW' ? 'selected' : '' }}>OFW</option>
                        <option value="Retired" {{ old('occupation')==='Retired' ? 'selected' : '' }}>Retired</option>
                        <option value="Other" {{ old('occupation')==='Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <input type="hidden" name="password" id="password">

                <div class="col-12"><hr class="my-2"></div>
                <h6 class="text-primary fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Verification Details</h6>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">ID Type</label>
                    <select name="verification_type" class="form-select" @disabled(!$canCreate)>
                        <option value="">-- Select --</option>
                        <option value="philid">PhilSys ID</option>
                        <option value="drivers_license">Driver's License</option>
                        <option value="passport">Passport</option>
                        <option value="postal">Postal ID</option>
                        <option value="voters">Voter's ID / Voter's Cert</option>
                        <option value="umid">UMID</option>
                        <option value="tin">TIN ID</option>
                        <option value="pagibig">Pag-IBIG ID</option>
                        <option value="schoolid">School ID</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">ID Number</label>
                    <input name="verification_id" class="form-control" value="{{ old('verification_id') }}" @disabled(!$canCreate)>
                </div>

                <div class="col-12"><hr class="my-2"></div>
                <h6 class="text-primary fw-bold mb-0"><i class="bi bi-camera me-2"></i>Photo Uploads</h6>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Profile Photo</label>
                    <label for="c_photo_path" class="dropzone" id="dz_c_photo">
                        <input type="file" name="photo_path" id="c_photo_path" accept=".jpg,.jpeg,.png" style="display:none;" @disabled(!$canCreate)>
                        <div class="dropzone-content" id="dz_c_photo_content">
                            <i class="bi bi-person dropzone-icon"></i>
                            <div class="dropzone-text fw-semibold">Profile Photo</div>
                            <div class="dropzone-hint">Click or drag to upload</div>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 4px;">JPG / PNG (max 5MB)</div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Government ID Photo</label>
                    <label for="c_id_image_path" class="dropzone" id="dz_c_id_image">
                        <input type="file" name="id_image_path" id="c_id_image_path" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" @disabled(!$canCreate)>
                        <div class="dropzone-content" id="dz_c_id_image_content">
                            <i class="bi bi-person-badge dropzone-icon"></i>
                            <div class="dropzone-text fw-semibold">Government ID Photo</div>
                            <div class="dropzone-hint">Click or drag to upload</div>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 4px;">JPG / PNG / PDF (max 5MB)</div>
                    </label>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Selfie with ID</label>
                    <label for="c_selfie_path" class="dropzone" id="dz_c_selfie">
                        <input type="file" name="selfie_image_path" id="c_selfie_path" accept=".jpg,.jpeg,.png" style="display:none;" @disabled(!$canCreate)>
                        <div class="dropzone-content" id="dz_c_selfie_content">
                            <i class="bi bi-camera dropzone-icon"></i>
                            <div class="dropzone-text fw-semibold">Selfie Holding ID</div>
                            <div class="dropzone-hint">Click or drag to upload</div>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 4px;">JPG / PNG (max 5MB)</div>
                    </label>
                </div>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" @disabled(!$canCreate)>Save Resident</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 pb-0">
        <h5 class="modal-title fw-bold text-primary">Edit Resident</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">First Name</label>
                    <input id="e_first_name" name="first_name" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Middle Name</label>
                    <input id="e_middle_name" name="middle_name" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Last Name</label>
                    <input id="e_last_name" name="last_name" class="form-control" @disabled(!$canEdit)>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Sex</label>
                    <select id="e_sex" name="sex" class="form-select" @disabled(!$canEdit)>
                        <option value="">-- Select --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Birth Date</label>
                    <input id="e_birth_date" name="birth_date" type="date" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Civil Status</label>
                    <select id="e_civil_status" name="civil_status" class="form-select" @disabled(!$canEdit)>
                        <option value="">-- Select --</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Street</label>
                    <input id="e_street" name="street" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Phase</label>
                    <input id="e_phase" name="phase" class="form-control" placeholder="Phase 1, 2, 3..." @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Address Line</label>
                    <input id="e_address_line" name="address_line" class="form-control" @disabled(!$canEdit)>
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Contact No.</label>
                    <input id="e_contact_no" name="contact_no" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Email</label>
                    <input id="e_email" name="email" class="form-control" @disabled(!$canEdit)>
                </div>

                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Occupation</label>
                    <input id="e_occupation" name="occupation" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted fw-bold">Replace Photo</label>
                    <input type="file" name="selfie_image_path" accept="image/*" class="form-control" @disabled(!$canEdit)>
                </div>

                <div class="col-12"><hr class="my-2"></div>
                <h6 class="text-primary fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Verification & Status</h6>

                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">ID Type</label>
                    <select id="e_verification_type" name="verification_type" class="form-select" @disabled(!$canEdit)>
                        <option value="philid">PhilSys ID</option>
                        <option value="drivers_license">Driver's License</option>
                        <option value="passport">Passport</option>
                        <option value="postal">Postal ID</option>
                        <option value="voters">Voter's ID / Voter's Cert</option>
                        <option value="umid">UMID</option>
                        <option value="tin">TIN ID</option>
                        <option value="pagibig">Pag-IBIG ID</option>
                        <option value="schoolid">School ID</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">ID Number</label>
                    <input id="e_verification_id" name="verification_id" class="form-control" @disabled(!$canEdit)>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-bold">Verification</label>
                    <select id="e_verification_status" name="verification_status" class="form-select" @disabled(!$canEdit)>
                        <option value="unverified">Unverified</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label small text-muted fw-bold">Account Status</label>
                    <select id="e_status" name="status" class="form-select" @disabled(!$canEdit)>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @if(!$canToggle)
                      <div class="form-text text-warning"><i class="bi bi-lock-fill"></i> Staff cannot change status.</div>
                    @endif
                </div>
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4" @disabled(!$canEdit)>Update Resident</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const tbody = document.getElementById('tbody');
  const msgBox = document.getElementById('message');
  const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

  const CAN_TOGGLE = @json($canToggle);
  const CAN_EDIT   = @json($canEdit);
  const CAN_CREATE = @json($canCreate);

  let createModal, editModal;

  document.addEventListener('DOMContentLoaded', () => {
      createModal = new bootstrap.Modal(document.getElementById('createModal'));
      editModal = new bootstrap.Modal(document.getElementById('editModal'));
      
      loadResidents();
  });

  function showMsg(obj, type='info') {
    msgBox.style.display = 'block';
    msgBox.className = 'alert ' + (type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-secondary')) + ' shadow-sm border-0';
    
    let content = '';
    if (typeof obj === 'string') {
        content = obj;
    } else {
        content = JSON.stringify(obj, null, 2);
    }
    
    msgBox.innerHTML = `<i class="bi bi-info-circle-fill me-2"></i> ${content}`;
    msgBox.scrollIntoView({ behavior:'smooth', block:'start' });
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        msgBox.style.display = 'none';
    }, 5000);
  }
  
  function hideMsg(){ msgBox.style.display='none'; msgBox.textContent=''; }

   async function api(url, options = {}) {
     const method = (options.method || 'GET').toUpperCase();

     const res = await fetch(url, {
       credentials: 'same-origin',
       method,
       headers: {
         'Accept': 'application/json',
         ...(method !== 'GET' ? { 'X-CSRF-TOKEN': csrf() } : {}),
         ...(options.headers || {})
       },
       body: options.body
     });

     let data;
     try {
       data = await res.json();
     } catch (e) {
       const text = await res.text();
       console.error('API JSON parse error:', e, 'Response text:', text);
       data = { _raw: text };
     }

     return { res, data };
   }

  function escapeHtml(s) {
    return String(s ?? '')
      .replaceAll('&','&')
      .replaceAll('<','<')
      .replaceAll('>','>')
      .replaceAll('"','"')
      .replaceAll("'","&#039;");
  }

  function middleInitial(m) {
    const s = (m || '').trim();
    return s ? (s[0].toUpperCase() + '.') : '';
  }

  function formatName(r) {
    const ln = (r.last_name || '').toUpperCase();
    const fn = (r.first_name || '').toUpperCase();
    const mi = middleInitial(r.middle_name || '');
    return `${ln}, ${fn}${mi ? ' ' + mi : ''}`.trim();
  }

  function pillStatus(status) {
    const s = (status || 'inactive').toLowerCase();
    if (s === 'active') {
      return `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">ACTIVE</span>`;
    }
    return `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">INACTIVE</span>`;
  }

  function pillVerify(v) {
    const s = (v || 'unverified').toLowerCase();
    if (s === 'verified')  return `<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">VERIFIED</span>`;
    if (s === 'pending')   return `<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">PENDING</span>`;
    if (s === 'rejected')  return `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">REJECTED</span>`;
    return `<span class="badge bg-light text-muted border rounded-pill">UNVERIFIED</span>`;
  }

  function generatePassword(length = 10) {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%^&*()';
    let password = '';
    for (let i = 0; i < length; i += 1) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
  }

  function fillCreatePassword() {
    document.getElementById('password').value = generatePassword(12);
  }

  document.getElementById('btnOpenCreate').addEventListener('click', () => {
    if (!CAN_CREATE) return showMsg('You do not have permission to add residents.', 'error');
    fillCreatePassword();
    createModal.show();
  });

  // Contact number format with spaces for create modal
  const cContactInput = document.getElementById('c_contact_no');
  if (cContactInput) {
    cContactInput.addEventListener('input', function(e) {
      let v = e.target.value.replace(/\D/g, '');
      if (v.length > 10) v = v.substring(0, 10);
      let result = '';
      if (v.length > 0) result += v.substring(0, 3);
      if (v.length > 3) result += ' ' + v.substring(3, 6);
      if (v.length > 6) result += ' ' + v.substring(6);
      e.target.value = result;
    });
  }
  // Show red border on invalid fields on blur in create modal
  document.querySelectorAll('#createModal .form-control[required]').forEach(function(input) {
    input.addEventListener('blur', function(e) {
      if (!e.target.value) {
        e.target.classList.add('is-invalid');
      }
    });
    input.addEventListener('input', function(e) {
      e.target.classList.remove('is-invalid');
    });
  });
  // Dropzone preview functions for create modal
  function bytes(n) {
    if (!n && n !== 0) return '';
    const units = ['B','KB','MB','GB'];
    let i = 0, x = n;
    while (x >= 1024 && i < units.length - 1) { x /= 1024; i++; }
    return x.toFixed(i === 0 ? 0 : 1) + ' ' + units[i];
  }
  function previewFile(inputEl, dzId) {
    const file = inputEl.files && inputEl.files[0];
    const dz = document.getElementById(dzId);
    if (!dz) return;
    if (!file) {
      dz.classList.remove('has-file');
      return;
    }
    dz.classList.add('has-file');
    const existingPreview = dz.querySelector('.dropzone-preview');
    if (existingPreview) existingPreview.remove();
    const preview = document.createElement('div');
    preview.className = 'dropzone-preview';
    if (file.type === 'application/pdf') {
      preview.innerHTML = '<i class="bi bi-file-pdf" style="font-size:36px;color:#ef4444;"></i><div class="text-muted" style="font-size:0.75rem;">' + file.name + ' - ' + bytes(file.size) + '</div>';
    } else {
      const img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.style.width = '100%';
      img.style.height = '80px';
      img.style.objectFit = 'cover';
      img.style.borderRadius = '8px';
      img.style.border = '2px solid #1055C9';
      preview.appendChild(img);
      preview.innerHTML += '<div class="text-muted" style="font-size:0.75rem; margin-top:4px;">' + file.name + ' - ' + bytes(file.size) + '</div>';
    }
    const removeLink = document.createElement('span');
    removeLink.className = 'text-danger';
    removeLink.style.fontSize = '0.75rem';
    removeLink.style.cursor = 'pointer';
    removeLink.style.textDecoration = 'underline';
    removeLink.style.display = 'block';
    removeLink.style.marginTop = '4px';
    removeLink.textContent = 'Remove';
    removeLink.addEventListener('click', function(e) {
      e.stopPropagation();
      inputEl.value = '';
      dz.classList.remove('has-file');
      preview.remove();
    });
    preview.appendChild(removeLink);
    dz.appendChild(preview);
  }
  function setupDropzone(dzId, inputId) {
    const dz = document.getElementById(dzId);
    const input = document.getElementById(inputId);
    if (!dz || !input) return;
    dz.addEventListener('dragover', function(e) { e.preventDefault(); dz.classList.add('dragover'); });
    dz.addEventListener('dragleave', function() { dz.classList.remove('dragover'); });
    dz.addEventListener('drop', function(e) {
      e.preventDefault();
      dz.classList.remove('dragover');
      if (!e.dataTransfer.files?.length) return;
      input.files = e.dataTransfer.files;
      input.dispatchEvent(new Event('change'));
    });
  }
  setupDropzone('dz_c_photo', 'c_photo_path');
  setupDropzone('dz_c_id_image', 'c_id_image_path');
  setupDropzone('dz_c_selfie', 'c_selfie_path');
  document.getElementById('c_photo_path').addEventListener('change', function(e) { previewFile(e.target, 'dz_c_photo'); });
  document.getElementById('c_id_image_path').addEventListener('change', function(e) { previewFile(e.target, 'dz_c_id_image'); });
  document.getElementById('c_selfie_path').addEventListener('change', function(e) { previewFile(e.target, 'dz_c_selfie'); });

   async function loadResidents() {
     console.log('loadResidents called');
     hideMsg();
     tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-2" role="status"></div><div class="small">Loading residents...</div></td></tr>`;

     const q = document.getElementById('search').value.trim();
     const url = q ? `/api/v1/residents?q=${encodeURIComponent(q)}` : `/api/v1/residents`;
     console.log('Fetching:', url);

     const { res, data } = await api(url);
     console.log('API response:', { status: res.status, data });

     // DEBUG
     console.log('URL:', url);
     console.log('Response status:', res.status);
     console.log('Response data:', data);

     if (!res.ok) {
       showMsg({ status: res.status, data }, 'error');
       tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-danger">Failed to load residents</td></tr>`;
       return;
     }

     const residents = data.residents || [];
    if (!residents.length) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>No residents found.</td></tr>`;
      return;
    }

    tbody.innerHTML = residents.map(r => {
      const id = r.id;
      const name = formatName(r);

      const address = escapeHtml(r.address_line || '');
      const contactNo = escapeHtml(r.contact_no || '');
      const email = escapeHtml(r.email || '');

      const contact = `
        <div class="small">${contactNo || '<span class="text-muted">-</span>'}</div>
        ${email ? `<div class="small text-muted">${email}</div>` : ``}
      `;

      const ver = pillVerify(r.verification_status);
      const st  = pillStatus(r.status);

      // Photo fallback hierarchy: selfie_image_path -> photo_path -> child_doc_path
      const photoPath = r.selfie_image_path || r.photo_path || r.child_doc_path;
      const residentName = `${r.first_name} ${r.last_name}`.trim();
      
      // Build correct storage URL
      const storageBase = window.location.origin + '/storage';
      const fullPhotoUrl = photoPath ? `${storageBase}/${escapeHtml(photoPath)}` : '';
      const photo = photoPath
        ? `<div class="img-wrap d-inline-block position-relative photo-expand-clickable" style="width:40px;height:40px;vertical-align:middle;" data-photo-url="${fullPhotoUrl}" data-resident-name="${escapeHtml(residentName)}" onclick="expandPhoto(this)" title="Click to expand"><img src="${storageBase}/${escapeHtml(photoPath)}" class="rounded-circle border" style="width:40px;height:40px;object-fit:cover;" alt="${escapeHtml(residentName)}" onload="this.classList.remove('d-none');this.nextElementSibling.classList.add('d-none')" onerror="this.classList.add('d-none');this.nextElementSibling.classList.remove('d-none')" /><div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle border d-none" style="width:40px;height:40px;border-radius:50%;"><i class="bi bi-person-fill"></i></div></div>`
        : `<div class="d-flex align-items-center justify-content-center bg-light text-muted rounded-circle border" style="width:40px;height:40px;border-radius:50%;"><i class="bi bi-person-fill"></i></div>`;

      const editBtn = CAN_EDIT
        ? `<button class="btn btn-sm btn-warning rounded-circle" style="width: 32px; height: 32px;" data-action="edit" data-id="${id}" title="Edit"><i class="bi bi-pencil-fill"></i></button>`
        : ``;

      const actionBtn = `<button class="btn btn-sm ${r.archived_at ? 'btn-outline-success' : 'btn-outline-danger'} rounded-circle" style="width: 32px; height: 32px;" data-action="${r.archived_at ? 'restore' : 'archive'}" data-id="${id}" title="${r.archived_at ? 'Restore' : 'Archive'}"><i class="bi bi-${r.archived_at ? 'arrow-counterclockwise' : 'archive-fill'}"></i></button>`;

      return `
        <tr>
          <td class="px-4 text-muted small">${id}</td>

          <td>
            <div class="fw-bold text-dark">${escapeHtml(name)}</div>
            <div class="small text-muted">
              ${r.birth_date ? `Born: ${escapeHtml(String(r.birth_date).slice(0,10))}` : ``}
              ${r.sex ? ` • ${escapeHtml(String(r.sex).toUpperCase())}` : ``}
            </div>
          </td>

          <td><span class="small">${address || '<span class="text-muted">-</span>'}</span></td>
          <td>${contact}</td>
          <td>${ver}</td>
          <td>${st}</td>
          <td>${photo}</td>

          <td class="text-end pe-4">
            <div class="d-flex justify-content-end gap-2">
              ${editBtn}
              ${actionBtn}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Expand photo modal function
  window.expandPhoto = function(el) {
    const photoUrl = el.getAttribute('data-photo-url');
    const residentName = el.getAttribute('data-resident-name');
    
    if (!photoUrl) return;
    
    const modal = new bootstrap.Modal(document.getElementById('photoExpandModal'));
    const img = document.getElementById('expandedPhoto');
    const downloadLink = document.getElementById('downloadPhotoLink');
    
    img.src = photoUrl;
    img.alt = residentName || 'Photo';
    downloadLink.href = photoUrl;
    downloadLink.download = (residentName || 'photo') + '.jpg';
    
    modal.show();
  };

   tbody.addEventListener('click', async (e) => {
     const btn = e.target.closest('button');
     if (!btn) return;

     const action = btn.getAttribute('data-action');
     const id = btn.getAttribute('data-id');

      if (action === 'archive') {
        if (!CAN_TOGGLE) return showMsg('Staff cannot archive residents.', 'error');
        if (!confirm('Are you sure you want to archive this resident?')) return;

        btn.disabled = true;
        const { res, data } = await api(`/api/v1/residents/${id}/archive`, { method: 'POST' });
        btn.disabled = false;

        if (!res.ok) return showMsg({ status: res.status, data }, 'error');

        await loadResidents();
        showMsg(data.message || 'Resident archived successfully', 'success');
      }

      if (action === 'restore') {
        if (!CAN_TOGGLE) return showMsg('Staff cannot restore residents.', 'error');
        if (!confirm('Are you sure you want to restore this resident?')) return;

        btn.disabled = true;
        const { res, data } = await api(`/api/v1/residents/${id}/restore`, { method: 'POST' });
        btn.disabled = false;

        if (!res.ok) return showMsg({ status: res.status, data }, 'error');

        await loadResidents();
        showMsg(data.message || 'Resident restored successfully', 'success');
      }

      if (action === 'edit') {
       if (!CAN_EDIT) return showMsg('You do not have permission to edit residents.', 'error');

       const { res, data } = await api(`/api/v1/residents/${id}`);
       if (!res.ok) return showMsg({ status: res.status, data }, 'error');

      const r = data.resident;

      const form = document.getElementById('editForm');
      form.action = `/admin/residents/${id}`;

      document.getElementById('e_first_name').value = r.first_name ?? '';
      document.getElementById('e_middle_name').value = r.middle_name ?? '';
      document.getElementById('e_last_name').value = r.last_name ?? '';
      document.getElementById('e_sex').value = r.sex ?? '';

      document.getElementById('e_birth_date').value = r.birth_date ? String(r.birth_date).slice(0,10) : '';
      document.getElementById('e_street').value = r.street ?? '';
      document.getElementById('e_phase').value = r.phase ?? '';
      document.getElementById('e_address_line').value = r.address_line ?? '';

      document.getElementById('e_contact_no').value = r.contact_no ?? '';
      document.getElementById('e_email').value = r.email ?? '';
      document.getElementById('e_civil_status').value = r.civil_status ?? '';
      document.getElementById('e_occupation').value = r.occupation ?? '';

      document.getElementById('e_verification_status').value = r.verification_status ?? 'unverified';
      document.getElementById('e_verification_id').value = r.verification_id ?? '';
      document.getElementById('e_verification_type').value = r.verification_type ?? '';

      document.getElementById('e_status').value = r.status ?? 'active';
      document.getElementById('e_status').disabled = !CAN_TOGGLE;

      editModal.show();
    }
  });

  document.getElementById('btnReload').addEventListener('click', loadResidents);
  document.getElementById('search').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') loadResidents();
  });

  @if ($errors->any())
    createModal.show();
  @endif

</script>
@endpush
@endsection
