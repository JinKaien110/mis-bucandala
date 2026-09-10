@extends('layouts.resident', ['currentRoute' => 'resident.profile', 'residentName' => ($resident->first_name ?? 'Resident')])

@section('content')
<div class="container">
  <!-- Page Header -->
  <div class="glass p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h2 class="mb-1"><i class="bi bi-person-circle me-2"></i>My Profile</h2>
        <p class="opacity-75 mb-0">Manage your personal information and account settings.</p>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Profile Form -->
    <div class="col-lg-8">
      <div class="glass p-4">
        <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Personal Information</h5>

        <form id="profileForm">
          <div class="row g-4">
            <div class="col-12">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-person-fill me-2"></i>Basic Information</h6>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label-glass">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="glass-input form-control" id="first_name" name="first_name" value="{{ $resident->first_name ?? '' }}" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label-glass">Middle Name</label>
                    <input type="text" class="glass-input form-control" id="middle_name" name="middle_name" value="{{ $resident->middle_name ?? '' }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label-glass">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="glass-input form-control" id="last_name" name="last_name" value="{{ $resident->last_name ?? '' }}" required>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label-glass">Sex</label>
                    <input type="text" class="glass-input form-control" value="{{ ucfirst($resident->sex ?? '-') }}" disabled>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label-glass">Birth Date</label>
                    <input type="text" class="glass-input form-control" value="{{ $resident->birth_date ? $resident->birth_date->format('M d, Y') : '-' }}" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-glass">Email Address</label>
                    <input type="email" class="glass-input form-control" value="{{ $user->email }}" disabled>
                    <small class="opacity-50">Email cannot be changed</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-glass">Contact Number</label>
                    <input type="text" class="glass-input form-control" id="contact_no" name="contact_no" value="{{ $resident->contact_no ?? '' }}">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label-glass">Civil Status</label>
                    <select class="glass-select form-select" id="civil_status" name="civil_status">
                      <option value="">Select</option>
                      <option value="single" {{ ($resident->civil_status ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                      <option value="married" {{ ($resident->civil_status ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                      <option value="widowed" {{ ($resident->civil_status ?? '') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                      <option value="separated" {{ ($resident->civil_status ?? '') === 'separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Address Details</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label-glass">Phase</label>
              <select class="glass-select form-select" id="phase" name="phase">
                <option value="">Select</option>
                <option value="Phase 1" {{ ($resident->phase ?? '') === 'Phase 1' ? 'selected' : '' }}>Phase 1</option>
                <option value="Phase 2" {{ ($resident->phase ?? '') === 'Phase 2' ? 'selected' : '' }}>Phase 2</option>
                <option value="Phase 3" {{ ($resident->phase ?? '') === 'Phase 3' ? 'selected' : '' }}>Phase 3</option>
                <option value="Phase 4" {{ ($resident->phase ?? '') === 'Phase 4' ? 'selected' : '' }}>Phase 4</option>
                <option value="Phase 5" {{ ($resident->phase ?? '') === 'Phase 5' ? 'selected' : '' }}>Phase 5</option>
                <option value="Other" {{ ($resident->phase ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Address Line</label>
              <input type="text" class="glass-input form-control" id="address_line" name="address_line" value="{{ $resident->address_line ?? '' }}">
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="glass p-4">
          <h6 class="mb-3"><i class="bi bi-briefcase-fill me-2"></i>Socioeconomic Details</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-glass">Occupation</label>
              <select class="glass-select form-select" id="occupation" name="occupation">
                <option value="">Select</option>
                <option value="Student" {{ ($resident->occupation ?? '') === 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Employed" {{ ($resident->occupation ?? '') === 'Employed' ? 'selected' : '' }}>Employed</option>
                <option value="Self-employed" {{ ($resident->occupation ?? '') === 'Self-employed' ? 'selected' : '' }}>Self-employed</option>
                <option value="Unemployed" {{ ($resident->occupation ?? '') === 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                <option value="OFW" {{ ($resident->occupation ?? '') === 'OFW' ? 'selected' : '' }}>OFW</option>
                <option value="Retired" {{ ($resident->occupation ?? '') === 'Retired' ? 'selected' : '' }}>Retired</option>
                <option value="Other" {{ ($resident->occupation ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Monthly Income</label>
              <select class="glass-select form-select" id="monthly_income" name="monthly_income">
                <option value="">Select</option>
                <option value="No Income" {{ ($resident->monthly_income ?? '') === 'No Income' ? 'selected' : '' }}>No Income</option>
                <option value="Below 10,000" {{ ($resident->monthly_income ?? '') === 'Below 10,000' ? 'selected' : '' }}>Below ₱10,000</option>
                <option value="10,000 - 20,000" {{ ($resident->monthly_income ?? '') === '10,000 - 20,000' ? 'selected' : '' }}>₱10,000 - ₱20,000</option>
                <option value="20,001 - 30,000" {{ ($resident->monthly_income ?? '') === '20,001 - 30,000' ? 'selected' : '' }}>₱20,001 - ₱30,000</option>
                <option value="30,001 - 50,000" {{ ($resident->monthly_income ?? '') === '30,001 - 50,000' ? 'selected' : '' }}>₱30,001 - ₱50,000</option>
                <option value="Above 50,000" {{ ($resident->monthly_income ?? '') === 'Above 50,000' ? 'selected' : '' }}>Above ₱50,000</option>
                <option value="Other" {{ ($resident->monthly_income ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Educational Attainment</label>
              <select class="glass-select form-select" id="educational_attainment" name="educational_attainment">
                <option value="">Select</option>
                <option value="no_formal" {{ ($resident->educational_attainment ?? '') === 'no_formal' ? 'selected' : '' }}>No Formal Education</option>
                <option value="elementary" {{ ($resident->educational_attainment ?? '') === 'elementary' ? 'selected' : '' }}>Elementary</option>
                <option value="junior_high" {{ ($resident->educational_attainment ?? '') === 'junior_high' ? 'selected' : '' }}>Junior High School</option>
                <option value="senior_high" {{ ($resident->educational_attainment ?? '') === 'senior_high' ? 'selected' : '' }}>Senior High School (SHS)</option>
                <option value="vocational" {{ ($resident->educational_attainment ?? '') === 'vocational' ? 'selected' : '' }}>Vocational/Technical</option>
                <option value="college" {{ ($resident->educational_attainment ?? '') === 'college' ? 'selected' : '' }}>College/University</option>
                <option value="postgraduate" {{ ($resident->educational_attainment ?? '') === 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">PWD</label>
              <select class="glass-select form-select" id="pwd_status" name="pwd_status">
                <option value="">Select</option>
                <option value="1" {{ ($resident->pwd_status ?? false) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (($resident->pwd_status ?? false) === false && $resident->pwd_status !== null) ? 'selected' : '' }}>No</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Solo Parent</label>
              <select class="glass-select form-select" id="solo_parent" name="solo_parent">
                <option value="">Select</option>
                <option value="1" {{ ($resident->solo_parent ?? false) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (($resident->solo_parent ?? false) === false && $resident->solo_parent !== null) ? 'selected' : '' }}>No</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Indigent</label>
              <select class="glass-select form-select" id="indigent_status" name="indigent_status">
                <option value="">Select</option>
                <option value="1" {{ ($resident->indigent_status ?? false) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (($resident->indigent_status ?? false) === false && $resident->indigent_status !== null) ? 'selected' : '' }}>No</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">4Ps Beneficiary</label>
              <select class="glass-select form-select" id="four_ps_beneficiary" name="four_ps_beneficiary">
                <option value="">Select</option>
                <option value="1" {{ ($resident->four_ps_beneficiary ?? false) ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (($resident->four_ps_beneficiary ?? false) === false && $resident->four_ps_beneficiary !== null) ? 'selected' : '' }}>No</option>
              </select>
            </div>
          </div>
        </div>
      </div>

            <div class="col-12">
              <div class="glass p-4">
                <h6 class="mb-3"><i class="bi bi-people-fill me-2"></i>Guardian Information</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label-glass">Guardian Name</label>
                    <input type="text" class="glass-input form-control" id="guardian_full_name" name="guardian_full_name" value="{{ $resident->guardian_full_name ?? '' }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-glass">Guardian Email</label>
                    <input type="email" class="glass-input form-control" id="guardian_email" name="guardian_email" value="{{ $resident->guardian_email ?? '' }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-glass">Guardian Contact No.</label>
                    <input type="text" class="glass-input form-control" id="guardian_contact_no" name="guardian_contact_no" value="{{ $resident->guardian_contact_no ?? '' }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-glass">Guardian Relationship</label>
                    <select class="glass-select form-select" id="guardian_relationship" name="guardian_relationship">
                      <option value="">Select relationship</option>
                      <option value="Father" {{ ($resident->guardian_relationship ?? '') === 'Father' ? 'selected' : '' }}>Father</option>
                      <option value="Mother" {{ ($resident->guardian_relationship ?? '') === 'Mother' ? 'selected' : '' }}>Mother</option>
                      <option value="Grandfather" {{ ($resident->guardian_relationship ?? '') === 'Grandfather' ? 'selected' : '' }}>Grandfather</option>
                      <option value="Grandmother" {{ ($resident->guardian_relationship ?? '') === 'Grandmother' ? 'selected' : '' }}>Grandmother</option>
                      <option value="Uncle" {{ ($resident->guardian_relationship ?? '') === 'Uncle' ? 'selected' : '' }}>Uncle</option>
                      <option value="Aunt" {{ ($resident->guardian_relationship ?? '') === 'Aunt' ? 'selected' : '' }}>Aunt</option>
                      <option value="Sibling" {{ ($resident->guardian_relationship ?? '') === 'Sibling' ? 'selected' : '' }}>Sibling</option>
                      <option value="Legal Guardian" {{ ($resident->guardian_relationship ?? '') === 'Legal Guardian' ? 'selected' : '' }}>Legal Guardian</option>
                      <option value="Other" {{ ($resident->guardian_relationship ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 text-end">
              <button type="submit" class="btn btn-glass-primary" id="saveBtn">
                <i class="bi bi-check-lg me-2"></i>Save Changes
              </button>
            </div>
          </div>
        </form>
      </div>

      <div class="glass p-4 mt-4">
        <h5 class="mb-4"><i class="bi bi-card-checklist me-2"></i>Resident Summary</h5>
        <div class="row g-3">
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Account Number</p>
              <p class="mb-0 fw-semibold">{{ $resident->account_no ?? '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Verification Status</p>
              <p class="mb-0 fw-semibold text-capitalize">{{ $resident->verification_status ?? '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Registered Via</p>
              <p class="mb-0 fw-semibold text-capitalize">{{ str_replace('_', ' ', $resident->registered_via ?? '-') }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Verification Type</p>
              <p class="mb-0 fw-semibold text-capitalize">{{ str_replace('_', ' ', $resident->verification_type ?? '-') }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Verification ID</p>
              <p class="mb-0 fw-semibold">{{ $resident->verification_id ?? '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Verified At</p>
              <p class="mb-0 fw-semibold">{{ $resident->verified_at ? $resident->verified_at->format('M d, Y h:i A') : '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Status</p>
              <p class="mb-0 fw-semibold text-capitalize">{{ $resident->status ?? '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Household ID</p>
              <p class="mb-0 fw-semibold">{{ $resident->household_id ?? '-' }}</p>
            </div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.08);">
              <p class="mb-1 text-uppercase text-muted small">Verified By</p>
              <p class="mb-0 fw-semibold">{{ $resident->verified_by ?? '-' }}</p>
            </div>
          </div>
        </div>
      </div>

      @php use Illuminate\Support\Str; @endphp
      <div class="glass p-4 mt-4">
        <h5 class="mb-4"><i class="bi bi-folder-plus me-2"></i>Additional Resident Details</h5>
        <div class="row g-3">
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">ID Image Path</p>
            <div class="mb-2">
              @if($resident->id_image_path)
                @php $idUrl = asset('storage/' . $resident->id_image_path); @endphp
                @if(Str::endsWith(strtolower($resident->id_image_path), ['.pdf']))
                  <a href="{{ $idUrl }}" target="_blank" class="link-primary">View ID document</a>
                @else
                  <a href="{{ $idUrl }}" target="_blank">
                    <img src="{{ $idUrl }}" alt="ID Image" class="img-fluid rounded" style="max-height: 180px; object-fit: contain; border: 1px solid rgba(255,255,255,0.12);">
                  </a>
                @endif
              @else
                <span class="fw-semibold">-</span>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Selfie Image Path</p>
            <div class="mb-2">
              @if($resident->selfie_image_path)
                @php $selfieUrl = asset('storage/' . $resident->selfie_image_path); @endphp
                <a href="{{ $selfieUrl }}" target="_blank">
                  <img src="{{ $selfieUrl }}" alt="Selfie Image" class="img-fluid rounded" style="max-height: 180px; object-fit: contain; border: 1px solid rgba(255,255,255,0.12);">
                </a>
              @else
                <span class="fw-semibold">-</span>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Photo Path</p>
            <div class="mb-2">
              @if($resident->photo_path)
                @php $photoUrl = asset('storage/' . $resident->photo_path); @endphp
                <a href="{{ $photoUrl }}" target="_blank">
                  <img src="{{ $photoUrl }}" alt="Stored Photo" class="img-fluid rounded" style="max-height: 180px; object-fit: contain; border: 1px solid rgba(255,255,255,0.12);">
                </a>
              @else
                <span class="fw-semibold">-</span>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Proof of Billing</p>
            <div class="mb-2">
              @if($resident->proof_of_billing_path)
                @php $proofUrl = asset('storage/' . $resident->proof_of_billing_path); @endphp
                @if(Str::endsWith(strtolower($resident->proof_of_billing_path), ['.pdf']))
                  <a href="{{ $proofUrl }}" target="_blank" class="link-primary">View proof of billing</a>
                @else
                  <a href="{{ $proofUrl }}" target="_blank">
                    <img src="{{ $proofUrl }}" alt="Proof of Billing" class="img-fluid rounded" style="max-height: 180px; object-fit: contain; border: 1px solid rgba(255,255,255,0.12);">
                  </a>
                @endif
              @else
                <span class="fw-semibold">-</span>
              @endif
            </div>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Child Document</p>
            <p class="mb-0 fw-semibold">{{ $resident->child_doc_path ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">OTP Email</p>
            <p class="mb-0 fw-semibold">{{ $resident->otp_email ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">OTP Verified At</p>
            <p class="mb-0 fw-semibold">{{ $resident->otp_verified_at ? $resident->otp_verified_at->format('M d, Y h:i A') : '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Guardian Name</p>
            <p class="mb-0 fw-semibold">{{ $resident->guardian_full_name ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Guardian Email</p>
            <p class="mb-0 fw-semibold">{{ $resident->guardian_email ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Guardian Contact</p>
            <p class="mb-0 fw-semibold">{{ $resident->guardian_contact_no ?? '-' }}</p>
          </div>
          <div class="col-md-6">
            <p class="mb-1 text-uppercase text-muted small">Guardian Relationship</p>
            <p class="mb-0 fw-semibold">{{ $resident->guardian_relationship ?? '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Picture -->
    <div class="col-lg-4">
      <div class="glass p-4 text-center">
        <h5 class="mb-4">Profile Picture</h5>
        
        <div class="profile-pic-container mb-3">
          @if($resident->photo_path ?? false)
            <img src="{{ asset('storage/' . $resident->photo_path) }}" alt="Profile" class="profile-pic" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
          @else
            <div class="profile-pic-placeholder" style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--mis-blue) 0%, var(--mis-blue-light) 100%); border: 4px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 8px 24px rgba(16, 85, 201, 0.4);">
              <i class="bi bi-person" style="font-size: 48px; opacity: 0.8;"></i>
            </div>
          @endif
        </div>
        
        <form id="photoForm" enctype="multipart/form-data">
          <input type="file" class="glass-input form-control" id="profile_picture" name="profile_picture" accept="image/*" style="max-width: 250px; margin: 0 auto;">
          <small class="opacity-50 d-block mt-2">Max 2MB. JPG, PNG, GIF</small>
        </form>
      </div>

      <!-- Account Info -->
      <div class="glass p-4 mt-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.06) 100%);">
        <h5 class="mb-4"><i class="bi bi-info-circle me-2"></i>Account Info</h5>
        <div class="profile-info-items">
          <div class="profile-info-item">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-calendar opacity-75"></i>
              <span class="opacity-75">Member Since</span>
            </div>
            <span>{{ $user->created_at->format('M d, Y') }}</span>
          </div>
          <div class="profile-info-item">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-check-circle opacity-75"></i>
              <span class="opacity-75">Account Status</span>
            </div>
            <span class="badge badge-glass-success">Active</span>
          </div>
          <div class="profile-info-item">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-badge opacity-75"></i>
              <span class="opacity-75">Role</span>
            </div>
            <span class="text-capitalize">{{ $user->role ?? 'Resident' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="glass p-4 text-center">
      <div class="spinner-border text-light mb-3" role="status"></div>
      <p class="mb-0">Saving changes...</p>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-modal">
        <div class="modal-body text-center py-4">
          <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
          <h4 class="mt-3">Profile Updated!</h4>
          <p class="opacity-75">Your changes have been saved successfully.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.modal {
  z-index: 99999 !important;
}
.modal-backdrop {
  z-index: 99998 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('profileForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const photoFile = document.getElementById('profile_picture').files[0];
  if (photoFile) {
    formData.append('profile_picture', photoFile);
  }
  
  document.getElementById('loadingOverlay').style.display = 'flex';
  
  try {
    const res = await fetch('{{ route("resident.profile.update") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    });
    
    const data = await res.json();
    
    document.getElementById('loadingOverlay').style.display = 'none';
    
    if (res.ok) {
      new bootstrap.Modal(document.getElementById('successModal')).show();
      setTimeout(() => window.location.assign(window.location.href.split('#')[0]), 2000);
    } else {
      alert(data.message || 'Failed to update profile');
    }
  } catch (e) {
    document.getElementById('loadingOverlay').style.display = 'none';
    alert('An error occurred. Please try again.');
  }
});
</script>
@endpush