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
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-glass">First Name <span class="text-danger">*</span></label>
              <input type="text" class="glass-input form-control" id="first_name" name="first_name" 
                     value="{{ $resident->first_name ?? '' }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label-glass">Middle Name</label>
              <input type="text" class="glass-input form-control" id="middle_name" name="middle_name" 
                     value="{{ $resident->middle_name ?? '' }}">
            </div>
            <div class="col-md-4">
              <label class="form-label-glass">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="glass-input form-control" id="last_name" name="last_name" 
                     value="{{ $resident->last_name ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Email Address</label>
              <input type="email" class="glass-input form-control" value="{{ $user->email }}" disabled>
              <small class="opacity-50">Email cannot be changed</small>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Contact Number</label>
              <input type="text" class="glass-input form-control" id="contact_no" name="contact_no" 
                     value="{{ $resident->contact_no ?? '' }}">
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Civil Status</label>
              <select class="glass-select form-select" id="civil_status" name="civil_status">
                <option value="">Select</option>
                <option value="single" {{ ($resident->civil_status ?? '') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="married" {{ ($resident->civil_status ?? '') === 'married' ? 'selected' : '' }}>Married</option>
                <option value="widowed" {{ ($resident->civil_status ?? '') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="separated" {{ ($resident->civil_status ?? '') === 'separated' ? 'selected' : '' }}>Separated</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-glass">Occupation</label>
              <select class="glass-select form-select" id="occupation" name="occupation">
                <option value="">Select</option>
                <option value="student" {{ ($resident->occupation ?? '') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="employed" {{ ($resident->occupation ?? '') === 'employed' ? 'selected' : '' }}>Employed</option>
                <option value="self_employed" {{ ($resident->occupation ?? '') === 'self_employed' ? 'selected' : '' }}>Self-employed</option>
                <option value="unemployed" {{ ($resident->occupation ?? '') === 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                <option value="ofw" {{ ($resident->occupation ?? '') === 'ofw' ? 'selected' : '' }}>OFW</option>
                <option value="retired" {{ ($resident->occupation ?? '') === 'retired' ? 'selected' : '' }}>Retired</option>
                <option value="other" {{ ($resident->occupation ?? '') === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-glass">Address</label>
              <input type="text" class="glass-input form-control" id="address_line" name="address_line" 
                     value="{{ $resident->address_line ?? '' }}">
            </div>
          </div>
          
          <div class="mt-4">
            <button type="submit" class="btn btn-glass-primary" id="saveBtn">
              <i class="bi bi-check-lg me-2"></i>Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Profile Picture -->
    <div class="col-lg-4">
      <div class="glass p-4 text-center">
        <h5 class="mb-4">Profile Picture</h5>
        
        <div class="profile-pic-container mb-3">
          @if($resident->profile_picture ?? false)
            <img src="{{ asset('storage/' . $resident->profile_picture) }}" alt="Profile" class="profile-pic" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
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
@endsection

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
      setTimeout(() => location.reload(), 2000);
    } else {
      alert(data.message || 'Failed to update profile');
    }
  } catch (e) {
    document.getElementById('loadingOverlay').style.display = 'none';
    alert('An error occurred. Please try again.');
  }
});

document.getElementById('profile_picture').addEventListener('change', function() {
  if (this.files && this.files[0]) {
    document.getElementById('profileForm').dispatchEvent(new Event('submit'));
  }
});
</script>
@endpush