@extends('layouts.admin')

@section('title', 'Manage Terms - Barangay MIS')

@section('content')
<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Barangay Terms</h4>
      <p class="text-muted mb-0">Manage official term periods</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.officials.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Officials
      </a>
      <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="bi bi-plus-lg me-1"></i> New Term
      </button>
    </div>
  </div>

  <!-- View Toggle -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-2">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link {{ $view === 'active' ? 'active' : '' }}" href="{{ route('admin.officials.terms.index', ['view' => 'active']) }}">
                    <i class="bi bi-calendar-check me-1"></i> Active Terms
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $view === 'archived' ? 'active bg-secondary' : 'text-secondary' }}" href="{{ route('admin.officials.terms.index', ['view' => 'archived']) }}">
                    <i class="bi bi-archive-fill me-1"></i> Archived
                </a>
            </li>
        </ul>
    </div>
  </div>

  <!-- Terms List -->
  @if($terms->count() > 0)
    <div class="row g-4">
      @foreach($terms as $term)
        <div class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100 term-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h5 class="fw-bold mb-1">{{ $term->term_label }}</h5>
                  <span class="badge {{ $term->is_current ? 'bg-success' : 'bg-secondary' }}">
                    {{ $term->is_current ? 'Current' : 'Past' }}
                  </span>
                  @if($term->is_archived)
                    <span class="badge bg-warning ms-1">Archived</span>
                  @endif
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light border rounded-circle" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="openEditModal({{ $term->id }})"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                    @if(!$term->is_archived)
                      <li><a class="dropdown-item" href="#" onclick="archiveTerm({{ $term->id }})"><i class="bi bi-archive me-2"></i>Archive</a></li>
                    @else
                      <li><a class="dropdown-item" href="#" onclick="unarchiveTerm({{ $term->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>Restore</a></li>
                    @endif
                  </ul>
                </div>
              </div>
              
              <div class="small text-muted mb-3">
                <i class="bi bi-calendar-range me-1"></i>
                {{ $term->term_start }} - {{ $term->term_end ?? 'Present' }}
              </div>
              
              <div class="d-grid">
                <a href="{{ route('admin.officials.index', ['term' => $term->id]) }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-people me-1"></i> View Officials ({{ $term->officials_count }})
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-5">
      <div class="mb-3">
        <i class="bi bi-calendar-range fs-1 text-muted opacity-25"></i>
      </div>
      <h5 class="fw-normal text-muted">No {{ $view }} terms found</h5>
      @if($view === 'active')
        <button type="button" class="btn btn-primary mt-2" onclick="openCreateModal()">
            <i class="bi bi-plus-lg me-1"></i> Create First Term
        </button>
      @endif
    </div>
  @endif

  <div class="mt-4">
    {{ $terms->links() }}
  </div>
</div>

<!-- Create Term Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>New Term</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createForm">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Start Year <span class="text-danger">*</span></label>
              <input type="number" name="term_start" class="form-control border-0 shadow-sm" min="2000" max="2100" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">End Year</label>
              <input type="number" name="term_end" class="form-control border-0 shadow-sm" min="2000" max="2100" placeholder="Leave empty for current">
              <small class="text-muted">Leave empty if this is the current term</small>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Title (Optional)</label>
              <input type="text" name="title" class="form-control border-0 shadow-sm" placeholder="e.g., Term 2023-2026">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" class="form-control border-0 shadow-sm" rows="2" placeholder="Additional notes..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Term</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Term Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Term</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Start Year <span class="text-danger">*</span></label>
              <input type="number" name="term_start" id="edit_term_start" class="form-control border-0 shadow-sm" min="2000" max="2100" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">End Year</label>
              <input type="number" name="term_end" id="edit_term_end" class="form-control border-0 shadow-sm" min="2000" max="2100">
              <small class="text-muted">Leave empty if this is the current term</small>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Title (Optional)</label>
              <input type="text" name="title" id="edit_title" class="form-control border-0 shadow-sm">
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                <label class="form-check-label fw-semibold" for="edit_is_active">
                  Active Term
                </label>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Notes</label>
              <textarea name="notes" id="edit_notes" class="form-control border-0 shadow-sm" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Term</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Term Card Hover Effect */
.term-card {
  transition: all 0.3s ease;
}
.term-card:hover {
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

.form-control:focus {
  border-color: #1055C9;
  box-shadow: 0 0 0 0.2rem rgba(16, 85, 201, 0.15);
}
</style>

<script>
let createModal, editModal;

document.addEventListener('DOMContentLoaded', function() {
  createModal = new bootstrap.Modal(document.getElementById('createModal'));
  editModal = new bootstrap.Modal(document.getElementById('editModal'));
});

function openCreateModal() {
  document.getElementById('createForm').reset();
  // Set default year to current year
  document.querySelector('input[name="term_start"]').value = new Date().getFullYear();
  createModal.show();
}

document.getElementById('createForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.officials.terms.store") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    createModal.hide();
    location.reload();
  })
  .catch(error => {
    createModal.hide();
    location.reload();
  });
});

function openEditModal(id) {
  fetch(`/admin/officials/terms/${id}/modal/edit`, {
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(response => response.json())
  .then(data => {
    const term = data.term;
    document.getElementById('edit_id').value = term.id;
    document.getElementById('edit_term_start').value = term.term_start;
    document.getElementById('edit_term_end').value = term.term_end || '';
    document.getElementById('edit_title').value = term.title || '';
    document.getElementById('edit_is_active').checked = term.is_active;
    document.getElementById('edit_notes').value = term.notes || '';
    editModal.show();
  });
}

document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('edit_id').value;
  const formData = new FormData(this);
  formData.append('_method', 'PUT');
  
  fetch(`/admin/officials/terms/${id}`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    editModal.hide();
    location.reload();
  })
  .catch(error => {
    editModal.hide();
    location.reload();
  });
});

function archiveTerm(id) {
  if (confirm('Archive this term? Officials in this term will no longer be visible.')) {
    fetch(`/admin/officials/terms/${id}/archive`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(() => location.reload());
  }
}

function unarchiveTerm(id) {
  fetch(`/admin/officials/terms/${id}/unarchive`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
  })
  .then(() => location.reload());
}
</script>
@endsection
