@extends('layouts.resident', ['currentRoute' => 'resident.dashboard', 'residentName' => ($firstName ?: 'Resident')])

@section('content')
<div class="container">
  <!-- Welcome Section -->
  <div class="row">
          <div class="col-lg-4">
        <!-- Resident Info -->
        <div class="glass p-4 mb-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.06) 100%);">
          <h5 class="mb-4"><i class="bi bi-person-badge me-2"></i>My Profile</h5>
          
          <div class="text-center mb-4 pb-3 border-bottom border-light border-opacity-10">
            <div class="mb-3 position-relative d-inline-block">
              @if($resident->photo_path ?? false)
                <img src="{{ asset('storage/' . $resident->photo_path) }}" alt="Profile" class="rounded-circle shadow-lg" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid rgba(255,255,255,0.2);">
              @else
                <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle shadow" style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--mis-blue) 0%, var(--mis-blue-light) 100%); border: 4px solid rgba(255,255,255,0.2);">
                  <i class="bi bi-person text-white" style="font-size: 48px; opacity: 0.8;"></i>
                </div>
              @endif
            </div>
            <h5 class="mb-1 fw-bold">{{ $resident ? $resident->first_name . ' ' . $resident->last_name : ($user->name ?? 'Resident') }}</h5>
            <p class="small opacity-75 mb-0"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
          </div>

          <div class="profile-info-items">
            <div class="profile-info-item">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-telephone opacity-75"></i>
                <span class="opacity-75">Contact</span>
              </div>
              <span class="fw-medium">{{ $resident->contact_no ?? 'Not set' }}</span>
            </div>
            <div class="profile-info-item">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-credit-card opacity-75"></i>
                <span class="opacity-75">Account No</span>
              </div>
              <span class="fw-medium">{{ $resident->account_no ?? 'Not set' }}</span>
            </div>

            <div class="profile-info-item">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt opacity-75"></i>
                <span class="opacity-75">Address</span>
              </div>
              <span class="text-end" style="max-width: 150px;">{{ $resident->address_line ?? 'Not set' }}</span>
            </div>
            <div class="profile-info-item">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle opacity-75"></i>
                <span class="opacity-75">Status</span>
              </div>
            <span class="badge badge-glass-success py-1 px-3">Active</span>
            </div>
            <div class="profile-info-item">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar opacity-75"></i>
                <span class="opacity-75">Member Since</span>
              </div>
            <span>{{ $user->created_at->format('M Y') }}</span>
            </div>
          </div>

          <a href="{{ route('resident.profile') }}" class="btn btn-glass-primary w-100 mt-4">
            <i class="bi bi-pencil me-2"></i>Edit Profile
          </a>
        </div>
    </div>

    <div class="col-lg-8">
      <!-- Welcome Header -->
      <div class="glass p-4 mb-4" style="background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.06) 100%);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h2 class="mb-2">Welcome back, {{ $resident->first_name ?: 'Resident' }}!</h2>
            <p class="opacity-75 mb-0">Here's your resident dashboard overview.</p>
          </div>
          <div class="d-flex gap-3">
            <button class="btn btn-glass btn-sm" data-bs-toggle="modal" data-bs-target="#docsModal">
              <i class="bi bi-file-earmark-text me-2"></i>View Documents
            </button>
            <button class="btn btn-glass-primary btn-sm" data-bs-toggle="modal" data-bs-target="#profileModal">
              <i class="bi bi-pencil-square me-2"></i>Edit Profile
            </button>
          </div>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="row g-3 mb-4 align-items-stretch">
        <div class="col-md-4 d-flex align-items-stretch">
          <div class="stat-card w-100 h-100" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#docsModal">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa;">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="stat-value">{{ $documentRequests->count() }}</div>
            <div class="stat-label">Document Requests</div>
          </div>
        </div>

        <div class="col-md-4 d-flex align-items-stretch">
          <a href="{{ route('resident.household') }}" class="stat-card w-100 h-100 text-white text-decoration-none" style="display: block;">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.2); color: #4ade80;">
              <i class="bi bi-people"></i>
            </div>
            <div class="stat-value">{{ count($householdMembers) }}</div>
            <div class="stat-label">Household Members</div>
          </a>
        </div>

        <div class="col-md-4 d-flex align-items-stretch">
          <div class="stat-card w-100 h-100" style="background: linear-gradient(135deg, rgba(254, 238, 145, 0.12) 0%, rgba(254, 238, 145, 0.04) 100%); border: 1px solid rgba(254, 238, 145, 0.25);">
            <div class="stat-icon" style="background: rgba(254, 238, 145, 0.16); color: #FEEE91;">
              <i class="bi bi-info-circle"></i>
            </div>
            <div class="stat-label" style="font-size: 0.95rem; opacity: 0.95; color: rgba(255,255,255,0.95);">
              Notice
            </div>
            <p class="small opacity-75 mb-0" style="margin-top: 4px;">
              For blotter reports, please visit the Barangay Hall in person. Blotter filing is a walk-in process.
            </p>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="glass p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Document Requests</h5>
          <button class="btn btn-glass btn-sm" data-bs-toggle="modal" data-bs-target="#docsModal">View All</button>
        </div>

        <div class="activity-list">
          @if(count($documentRequests) > 0)
            @foreach($documentRequests->take(5) as $request)
            <div class="activity-item d-flex align-items-center justify-content-between py-3 border-bottom border-light border-opacity-10">
              <div class="d-flex align-items-center gap-3">
                <div class="icon-circle bg-white bg-opacity-10 d-flex align-items-center justify-content-center rounded-3" style="width: 40px; height: 40px;">
                  <i class="bi bi-file-earmark-text opacity-75"></i>
                </div>
                <div>
                  <div class="small fw-bold">{{ $request->document_type ?? 'Document Request' }}</div>
                  <small class="opacity-50" style="font-size: 0.75rem;">{{ $request->created_at->format('M d, Y') }}</small>
                </div>
              </div>
              <span class="badge-glass 
                @if($request->status === 'completed') badge-glass-success
                @elseif($request->status === 'pending') badge-glass-warning
                @elseif($request->status === 'processing') badge-glass-info
                @else badge-glass-secondary @endif">
                {{ ucfirst($request->status ?? 'pending') }}
              </span>
            </div>
            @endforeach
          @else
            <div class="text-center py-5">
              <i class="bi bi-inbox fs-1 opacity-25 mb-3"></i>
              <p class="opacity-75 mb-0">No document requests yet.</p>
              <div class="btn btn-glass-primary btn-sm mt-3" style="pointer-events: none; opacity: 0.8;">
                <i class="bi bi-geo-alt me-1"></i>Visit Barangay Hall to request documents
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.profile-info-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.profile-info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
}
.activity-item:last-child {
  border-bottom: none !important;
}
.stat-card:hover {
  transform: translateY(-5px);
  transition: all 0.3s ease;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  border-color: rgba(255,255,255,0.4) !important;
}
</style>
@endpush

 @push('scripts')
 <script>
 function filterDocs(status) {
   const search = document.getElementById('docSearch').value.toLowerCase();
   const statusFilter = status || document.getElementById('docStatusFilter').value;
   const rows = document.querySelectorAll('.doc-row');
   
   let visibleCount = 0;
   rows.forEach(row => {
     const type = row.dataset.type || '';
     const rowStatus = row.dataset.status || '';
     const matchesSearch = type.includes(search);
     const matchesStatus = !statusFilter || rowStatus === statusFilter;
     
     if (matchesSearch && matchesStatus) {
       row.style.display = '';
       visibleCount++;
     } else {
       row.style.display = 'none';
     }
   });
   
   document.getElementById('docCount').textContent = visibleCount + ' found';
 }



document.getElementById('saveProfileBtn').addEventListener('click', async function() {
  const form = document.getElementById('profileForm');
  const formData = new FormData(form);
  const btn = this;
  
  btn.disabled = true;
  btn.textContent = 'Saving...';
  
  try {
    const res = await fetch('{{ route("resident.profile.update") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(Object.fromEntries(formData))
    });
    
    const data = await res.json();
    
    if (res.ok) {
      alert('Profile updated successfully!');
      bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
      location.reload();
    } else {
      alert(data.message || 'Failed to update profile');
    }
  } catch (e) {
    alert('An error occurred. Please try again.');
  } finally {
    btn.disabled = false;
    btn.textContent = 'Save Changes';
  }
});
</script>
@endpush


<!-- Document Requests Modal -->
<div id="docsModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title text-white"><i class="bi bi-file-earmark-text me-2"></i>My Document Requests</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Search & Filter -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <input type="text" class="glass-input form-control" id="docSearch" placeholder="Search documents..." onkeyup="filterDocs()">
          </div>
          <div class="col-md-4">
            <select class="glass-select form-select" id="docStatusFilter" onchange="filterDocs()">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="completed">Completed</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="col-md-2">
            <span class="badge badge-glass-info" id="docCount">{{ count($documentRequests) }} total</span>
          </div>
        </div>

        <!-- Documents Table -->
        <div class="table-responsive">
          <table class="glass-table">
            <thead>
              <tr>
                <th>Document Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Control No.</th>
              </tr>
            </thead>
            <tbody id="docsTableBody">
              @forelse($documentRequests as $doc)
              <tr class="doc-row" data-status="{{ $doc->status }}" data-type="{{ strtolower($doc->document_type ?? '') }}">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-text text-primary"></i>
                    {{ $doc->document_type ?? 'Document Request' }}
                  </div>
                </td>
                <td>{{ $doc->created_at->format('M d, Y') }}</td>
                <td>
                  <span class="badge-glass 
                    @if($doc->status === 'completed') badge-glass-success
                    @elseif($doc->status === 'pending') badge-glass-warning
                    @elseif($doc->status === 'processing') badge-glass-info
                    @elseif($doc->status === 'rejected') badge-glass-danger
                    @else badge-glass-secondary @endif">
                    {{ ucfirst($doc->status ?? 'pending') }}
                  </span>
                </td>
                <td><code>{{ $doc->control_no ?? '-' }}</code></td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4">
                  <i class="bi bi-inbox fs-1 opacity-50 mb-2 d-block"></i>
                  No document requests found.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content glass-modal">
      <div class="modal-header">
        <h5 class="modal-title text-white">Update Profile</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="profileForm">
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
            <div class="col-md-6">
              <label class="form-label-glass">Contact Number</label>
              <input type="text" class="glass-input form-control" id="contact_no" name="contact_no" value="{{ $resident->contact_no ?? '' }}">
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
            <div class="col-12">
              <label class="form-label-glass">Address</label>
              <input type="text" class="glass-input form-control" id="address_line" name="address_line" value="{{ $resident->address_line ?? '' }}">
            </div>
            <div class="col-12">
              <label class="form-label-glass">Occupation</label>
              <input type="text" class="glass-input form-control" id="occupation" name="occupation" value="{{ $resident->occupation ?? '' }}">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-glass" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-glass-primary" id="saveProfileBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>