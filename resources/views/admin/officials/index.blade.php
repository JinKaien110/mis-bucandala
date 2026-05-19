@extends('layouts.admin')

@section('title', 'Barangay Officials - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Barangay Officials</h4>
      <p class="text-muted mb-0">Manage barangay officials by term</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.officials.terms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-calendar-range me-1"></i> Manage Terms
      </a>
      <button type="button" class="btn btn-primary btn-lg px-4 fw-semibold shadow-sm" onclick="openCreateModal()" {{ $terms->isEmpty() ? 'disabled' : '' }}>
        <i class="bi bi-person-plus me-2"></i> Add Official
      </button>
    </div>
  </div>

  <!-- Term Selector -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <label class="text-muted fw-semibold mb-0">Select Term:</label>
          <select class="form-select form-select-sm" style="width: 250px;" onchange="window.location.href = '{{ route('admin.officials.index') }}?term=' + this.value">
            @forelse($terms as $term)
              <option value="{{ $term->id }}" {{ $currentTerm && $currentTerm->id == $term->id ? 'selected' : '' }}>
                {{ $term->term_label }}
                @if($term->is_current)
                  (Current)
                @endif
              </option>
            @empty
              <option value="">No terms available</option>
            @endforelse
          </select>
        </div>
        @if($currentTerm)
          <div class="d-flex gap-2">
            <span class="badge {{ $currentTerm->is_current ? 'bg-success' : 'bg-secondary' }}">
              {{ $currentTerm->is_current ? 'Active' : 'Inactive' }}
            </span>
            @if($currentTerm->is_archived)
              <span class="badge bg-warning">Archived</span>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>

  @if($terms->isEmpty())
    <div class="text-center py-5">
      <div class="mb-3">
        <i class="bi bi-calendar-range fs-1 text-muted opacity-25"></i>
      </div>
      <h5 class="fw-normal text-muted">No terms created yet</h5>
      <p class="text-muted">Create a term first before adding officials</p>
      <a href="{{ route('admin.officials.terms.index') }}" class="btn btn-primary mt-2">
        <i class="bi bi-plus-lg me-1"></i> Create First Term
      </a>
    </div>
  @elseif($currentTerm)
    <!-- Officials Grid -->
    @if($officials->count() > 0)
      <div class="row g-4">
        @foreach($officials as $official)
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 official-card">
              <div class="card-body text-center py-4">
                @if($official->photo_path)
                  <img src="{{ asset('storage/' . $official->photo_path) }}" alt="{{ $official->name }}" class="rounded-circle mx-auto mb-3 border border-3 border-primary" style="width: 90px; height: 90px; object-fit: cover;">
                @else
                  <div class="avatar avatar-xl bg-gradient text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 90px; height: 90px;">
                    <i class="bi bi-person-badge-fill fs-2"></i>
                  </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $official->name }}</h5>
                <span class="badge bg-warning bg-opacity-25 text-warning border rounded-pill mb-2 px-3">{{ $official->position }}</span>
                
                @if($official->committee)
                  <div class="small text-muted mb-3">{{ $official->committee }}</div>
                @else
                  <div class="mb-3"></div>
                @endif

                <div class="d-flex justify-content-center gap-2 mb-3">
                  @if($official->contact_no)
                      <a href="tel:{{ $official->contact_no }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Call">
                          <i class="bi bi-telephone-fill"></i>
                      </a>
                  @endif
                  @if($official->email)
                      <a href="mailto:{{ $official->email }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Email">
                          <i class="bi bi-envelope-fill"></i>
                      </a>
                  @endif
                </div>
              </div>
              <div class="card-footer bg-white border-top-0 pb-3 pt-0">
                <div class="d-grid gap-2">
                  <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-info btn-sm rounded-circle" style="width: 32px; height: 32px;" onclick="openShowModal({{ $official->id }})" title="View">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm rounded-circle" style="width: 32px; height: 32px;" onclick="openEditModal({{ $official->id }})" title="Edit">
                      <i class="bi bi-pencil-fill"></i>
                    </button>
                    <form method="POST" action="{{ route('admin.officials.destroy', $official) }}" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm rounded-circle" style="width: 32px; height: 32px;" title="Archive" onclick="return confirm('Are you sure you want to archive this official?')">
                        <i class="bi bi-archive-fill"></i>
                      </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm rounded-circle" style="width: 32px; height: 32px;" onclick="deleteOfficial({{ $official->id }})" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-5">
        <div class="mb-3">
          <i class="bi bi-people fs-1 text-muted opacity-25"></i>
        </div>
        <h5 class="fw-normal text-muted">No officials in this term</h5>
        <button type="button" class="btn btn-primary btn-lg mt-2" onclick="openCreateModal()">
            <i class="bi bi-person-plus me-2"></i> Add Official
        </button>
      </div>
    @endif

    <div class="mt-4">
      {{ $officials->links() }}
    </div>
  @else
    <div class="text-center py-5">
      <div class="mb-3">
        <i class="bi bi-calendar-check fs-1 text-muted opacity-25"></i>
      </div>
      <h5 class="fw-normal text-muted">Select a term to view officials</h5>
    </div>
  @endif
</div>

<!-- Create Official Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New Official</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createForm" action="{{ route('admin.officials.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control border-0 shadow-sm" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
              <select name="position" class="form-select border-0 shadow-sm" required>
                <option value="">-- Select Position --</option>
                <option value="Barangay Captain">Barangay Captain</option>
                <option value="Barangay Councilor">Barangay Councilor</option>
                <option value="SK Chairman">SK Chairman</option>
                <option value="Secretary">Secretary</option>
                <option value="Treasurer">Treasurer</option>
                <option value="Kagawad">Kagawad</option>
                <option value="Sangguniang Kabataan">Sangguniang Kabataan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
              <select name="barangay_term_id" id="create_barangay_term_id" class="form-select border-0 shadow-sm" required>
                <option value="">-- Select Term --</option>
                @foreach($terms as $term)
                  <option value="{{ $term->id }}">{{ $term->term_label }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Committee Assignment</label>
              <input type="text" name="committee" class="form-control border-0 shadow-sm" placeholder="e.g., Committee on Peace and Order">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number</label>
              <input type="text" name="contact_no" class="form-control border-0 shadow-sm" placeholder="e.g., 0912 345 6789">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" class="form-control border-0 shadow-sm" placeholder="official@email.com">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Photo</label>
              <input type="file" name="photo_path" class="form-control border-0 shadow-sm" accept="image/jpeg,image/png,image/jpg,image/gif">
              <small class="text-muted">Max 2MB. JPEG, PNG, JPG, GIF only.</small>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control border-0 shadow-sm" rows="3" placeholder="Additional notes..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Official</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Official Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Official</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="edit_name" class="form-control border-0 shadow-sm" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Position <span class="text-danger">*</span></label>
              <select name="position" id="edit_position" class="form-select border-0 shadow-sm" required>
                <option value="">-- Select Position --</option>
                <option value="Barangay Captain">Barangay Captain</option>
                <option value="Barangay Councilor">Barangay Councilor</option>
                <option value="SK Chairman">SK Chairman</option>
                <option value="Secretary">Secretary</option>
                <option value="Treasurer">Treasurer</option>
                <option value="Kagawad">Kagawad</option>
                <option value="Sangguniang Kabataan">Sangguniang Kabataan</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Term <span class="text-danger">*</span></label>
              <select name="barangay_term_id" id="edit_barangay_term_id" class="form-select border-0 shadow-sm" required>
                @foreach($terms as $term)
                  <option value="{{ $term->id }}">{{ $term->term_label }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Committee Assignment</label>
              <input type="text" name="committee" id="edit_committee" class="form-control border-0 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number</label>
              <input type="text" name="contact_no" id="edit_contact_no" class="form-control border-0 shadow-sm">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" id="edit_email" class="form-control border-0 shadow-sm">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Photo</label>
              <input type="file" name="photo_path" class="form-control border-0 shadow-sm" accept="image/jpeg,image/png,image/jpg,image/gif">
              <small class="text-muted">Max 2MB. JPEG, PNG, JPG, GIF only. Leave empty to keep current photo.</small>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" id="edit_notes" class="form-control border-0 shadow-sm" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Official</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Show Official Modal -->
<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Official Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-4">
          <div class="col-md-12 text-center">
              <div id="show_photo_container">
                  <div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                      <i class="bi bi-person-badge-fill fs-1"></i>
                  </div>
              </div>
              <h3 id="show_name" class="mb-2"></h3>
              <span id="show_position" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-6 px-3 py-2"></span>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
              <label class="small text-muted text-uppercase fw-bold d-block">Term</label>
              <span id="show_term" class="badge bg-secondary"></span>
          </div>
          
          <div class="col-md-6">
              <label class="small text-muted text-uppercase fw-bold d-block">Committee</label>
              <span id="show_committee" class="text-muted">-</span>
          </div>

          <div class="col-12"><hr class="my-2"></div>

          <div class="col-md-6">
              <label class="small text-muted text-uppercase fw-bold d-block">Contact Number</label>
              <span id="show_contact"></span>
          </div>

          <div class="col-md-6">
              <label class="small text-muted text-uppercase fw-bold d-block">Email</label>
              <span id="show_email"></span>
          </div>

          <div class="col-12 mt-3" id="show_notes_section" style="display: none;">
              <label class="small text-muted text-uppercase fw-bold d-block mb-2">Notes</label>
              <div id="show_notes" class="p-3 bg-light rounded border text-muted"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="editFromShowBtn"><i class="bi bi-pencil me-1"></i>Edit</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
/* Official Card Hover Effect */
.official-card {
  transition: all 0.3s ease;
}
.official-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

/* Modern Modal Styles */
.modal-content {
  border: none;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.modal-header {
  background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%);
  color: white;
  border: none;
  padding: 20px 24px;
}

.modal-header .btn-close {
  filter: invert(1);
  opacity: 0.8;
}

.modal-header .btn-close:hover {
  opacity: 1;
}

.modal-title {
  font-weight: 600;
  display: flex;
  align-items: center;
}

.modal-body {
  padding: 24px;
}

.modal-footer {
  border-top: 1px solid #e9ecef;
  padding: 16px 24px;
}

/* Form Controls */
.form-control:focus, .form-select:focus {
  border-color: #1055C9;
  box-shadow: 0 0 0 0.2rem rgba(16, 85, 201, 0.15);
}
</style>

<script>
// Create Modal
let createModal;
document.addEventListener('DOMContentLoaded', function() {
  createModal = new bootstrap.Modal(document.getElementById('createModal'));
});

function openCreateModal() {
  document.getElementById('createForm').reset();
  @if($currentTerm)
  document.getElementById('create_barangay_term_id').value = '{{ $currentTerm->id }}';
  @endif
  createModal.show();
}

// Handle create form submission with AJAX
document.getElementById('createForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.officials.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      createModal.hide();
      window.location.href = data.redirect || '{{ route("admin.officials.index") }}';
    } else {
      alert('Error: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error creating official');
  });
});

// Edit Modal
let editModal;
document.addEventListener('DOMContentLoaded', function() {
  editModal = new bootstrap.Modal(document.getElementById('editModal'));
});

function openEditModal(id) {
  // Set form action before showing modal
  document.getElementById('editForm').action = '/admin/officials/' + id;
  
  fetch(`/admin/officials/${id}/modal/edit`, {
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    const official = data.official;
    document.getElementById('edit_id').value = official.id;
    document.getElementById('edit_name').value = official.name;
    document.getElementById('edit_position').value = official.position;
    document.getElementById('edit_barangay_term_id').value = official.barangay_term_id;
    document.getElementById('edit_committee').value = official.committee || '';
    document.getElementById('edit_contact_no').value = official.contact_no || '';
    document.getElementById('edit_email').value = official.email || '';
    document.getElementById('edit_notes').value = official.notes || '';
    editModal.show();
  });
}

// Handle edit form submission with AJAX PUT
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('_method', 'PUT');
  const id = document.getElementById('edit_id').value;
  
  fetch(`/admin/officials/${id}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      editModal.hide();
      window.location.href = data.redirect || '{{ route("admin.officials.index") }}';
    } else {
      alert('Error: ' + (data.message || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error updating official');
  });
});

// Show Modal
let showModal;
let currentOfficialId;
document.addEventListener('DOMContentLoaded', function() {
  showModal = new bootstrap.Modal(document.getElementById('showModal'));
});

function openShowModal(id) {
  currentOfficialId = id;
  fetch(`/admin/officials/${id}/modal/show`, {
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    const official = data.official;
    
    document.getElementById('show_name').textContent = official.name;
    document.getElementById('show_position').textContent = official.position;
    document.getElementById('show_term').textContent = official.term ? official.term.term_label : 'No Term';
    document.getElementById('show_committee').textContent = official.committee || '-';
    document.getElementById('show_contact').textContent = official.contact_no || '-';
    document.getElementById('show_email').textContent = official.email || '-';
    
    // Photo
    const photoContainer = document.getElementById('show_photo_container');
    if (official.photo_path) {
      photoContainer.innerHTML = `<img src="${official.photo_path }" alt="${official.name}" class="rounded-circle mx-auto mb-3" style="width: 100px; height: 100px; object-fit: cover;">`;
    } else {
      photoContainer.innerHTML = `<div class="avatar avatar-xl bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;"><i class="bi bi-person-badge-fill fs-1"></i></div>`;
    }
    
    // Notes
    if (official.notes) {
      document.getElementById('show_notes_section').style.display = 'block';
      document.getElementById('show_notes').textContent = official.notes;
    } else {
      document.getElementById('show_notes_section').style.display = 'none';
    }
    
    // Edit button
    document.getElementById('editFromShowBtn').onclick = function() {
      showModal.hide();
      setTimeout(() => openEditModal(id), 300);
    };
    
    showModal.show();
  });
}

// Delete Official
function deleteOfficial(id) {
  if (confirm('Are you sure you want to delete this official?')) {
    fetch(`/admin/officials/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(response => response.json())
    .then(data => {
      location.reload();
    });
  }
}
</script>
@endsection
