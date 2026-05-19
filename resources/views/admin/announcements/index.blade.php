@extends('layouts.admin')

@section('title', 'Announcements - Barangay MIS')

@section('content')
<div class="page-surface">
  <!-- Alert Message -->
  <div id="message" class="alert" style="display:none;"></div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Announcements</h4>
      <p class="text-muted mb-0">Manage barangay announcements</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
      <i class="bi bi-plus-lg"></i> New Announcement
    </button>
  </div>

  <!-- Filter -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="d-flex gap-2">
        <select name="type" class="form-select" style="width: 200px;">
          <option value="">All Types</option>
          <option value="general" {{ $type == 'general' ? 'selected' : '' }}>General</option>
          <option value="event" {{ $type == 'event' ? 'selected' : '' }}>Event</option>
          <option value="urgent" {{ $type == 'urgent' ? 'selected' : '' }}>Urgent</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i></button>
      </form>
    </div>
  </div>

  <!-- Announcements -->
  <div class="row">
    @forelse($announcements as $announcement)
      <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100 announcement-card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              @switch($announcement->type)
                @case('urgent')
                  <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Urgent</span>
                  @break
                @case('event')
                  <span class="badge bg-info"><i class="bi bi-calendar-event"></i> Event</span>
                  @break
                @default
                  <span class="badge bg-secondary"><i class="bi bi-info-circle"></i> General</span>
              @endswitch
              <span class="{{ $announcement->is_published ? 'text-success' : 'text-muted' }}">
                <small>{!! $announcement->is_published ? '<i class="bi bi-check-circle-fill"></i> Published' : '<i class="bi bi-eye-slash"></i> Draft' !!}</small>
              </span>
            </div>
            <h5 class="card-title">{{ $announcement->title }}</h5>
            <p class="card-text text-muted">{{ Str::limit($announcement->content, 150) }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                <i class="bi bi-person"></i> {{ $announcement->creator->admin->first_name ?? 'Unknown' }} {{ $announcement->creator->admin->last_name ?? '' }}<br>
                <i class="bi bi-clock"></i> {{ $announcement->created_at->format('M d, Y') }}
              </small>
              <div>
                <button type="button" class="btn btn-info btn-sm rounded-circle" style="width: 32px; height: 32px;" onclick="openShowModal({{ $announcement->id }})" title="View">
                  <i class="bi bi-eye-fill"></i>
                </button>
                <button type="button" class="btn btn-warning btn-sm rounded-circle" style="width: 32px; height: 32px;" onclick="openEditModal({{ $announcement->id }})" title="Edit">
                  <i class="bi bi-pencil-fill"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="text-center py-5 text-muted">
          <i class="bi bi-megaphone fs-1 d-block mb-2"></i>
          No announcements found
        </div>
      </div>
    @endforelse
  </div>

  {{ $announcements->links() }}
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-megaphone me-2"></i>New Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createForm">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Title *</label>
            <input type="text" name="title" class="form-control border-0 shadow-sm" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Type *</label>
              <select name="type" class="form-select border-0 shadow-sm" required>
                <option value="general">General</option>
                <option value="event">Event</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Status</label>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="create_published">
                <label class="form-check-label" for="create_published">Publish immediately</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Publish Date</label>
              <input type="date" name="publish_date" class="form-control border-0 shadow-sm">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Expire Date</label>
              <input type="date" name="expire_date" class="form-control border-0 shadow-sm">
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="show_on_calendar" value="1" id="create_show_on_calendar">
              <label class="form-check-label" for="create_show_on_calendar">
                Show on Events Calendar
              </label>
              <small class="text-muted d-block">Checking this will display this announcement on the events calendar</small>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Content *</label>
            <textarea name="content" class="form-control border-0 shadow-sm" rows="6" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Announcement</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Title *</label>
            <input type="text" name="title" id="edit_title" class="form-control border-0 shadow-sm" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Type *</label>
              <select name="type" id="edit_type" class="form-select border-0 shadow-sm" required>
                <option value="general">General</option>
                <option value="event">Event</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Status</label>
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="edit_published">
                <label class="form-check-label" for="edit_published">Published</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Publish Date</label>
              <input type="date" name="publish_date" id="edit_publish_date" class="form-control border-0 shadow-sm">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Expire Date</label>
              <input type="date" name="expire_date" id="edit_expire_date" class="form-control border-0 shadow-sm">
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="show_on_calendar" value="1" id="edit_show_on_calendar">
              <label class="form-check-label" for="edit_show_on_calendar">
                Show on Events Calendar
              </label>
              <small class="text-muted d-block">Checking this will display this announcement on the events calendar</small>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Content *</label>
            <textarea name="content" id="edit_content" class="form-control border-0 shadow-sm" rows="6" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Announcement</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Announcement Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex gap-2 mb-4">
          <span id="show_type_badge"></span>
          <span id="show_published_status"></span>
          <span id="show_calendar_badge" class="badge bg-danger" style="display: none;"><i class="bi bi-calendar"></i> On Calendar</span>
        </div>

        <h3 id="show_title" class="mb-3"></h3>

        <div class="text-muted mb-4">
          <small>
            <i class="bi bi-person"></i> Created by <span id="show_creator"></span> |
            <i class="bi bi-clock"></i> <span id="show_date"></span>
          </small>
        </div>

        <hr>

        <div id="show_content" class="content mt-4"></div>

        <hr>
        <div class="row mt-3" id="show_dates"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="deleteBtn"><i class="bi bi-trash me-1"></i>Delete</button>
      </div>
    </div>
  </div>
</div>

<style>
/* Announcement Card Hover Effect */
.announcement-card {
  transition: all 0.3s ease;
}
.announcement-card:hover {
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

/* Custom Scrollbar for Modal Content */
.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
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
  createModal.show();
}

document.getElementById('createForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.announcements.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(err => Promise.reject(err));
    }
    return response.json();
  })
  .then(data => {
    createModal.hide();
    location.reload();
  })
  .catch(error => {
    console.error('Error:', error);
    alert(error.message || 'Error creating announcement');
  });
});

// Edit Modal
let editModal;
document.addEventListener('DOMContentLoaded', function() {
  editModal = new bootstrap.Modal(document.getElementById('editModal'));
});

function openEditModal(id) {
  fetch(`/admin/announcements/${id}/modal/edit`, {
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    const announcement = data.announcement;
    document.getElementById('edit_id').value = announcement.id;
    document.getElementById('edit_title').value = announcement.title;
    document.getElementById('edit_type').value = announcement.type;
    document.getElementById('edit_published').checked = announcement.is_published;
    document.getElementById('edit_publish_date').value = announcement.publish_date || '';
    document.getElementById('edit_expire_date').value = announcement.expire_date || '';
    document.getElementById('edit_show_on_calendar').checked = !!announcement.event_id;
    document.getElementById('edit_content').value = announcement.content;
    editModal.show();
  });
}

document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const id = document.getElementById('edit_id').value;
  const formData = new FormData(this);
  formData.append('_method', 'PUT');
  
  fetch(`/admin/announcements/${id}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      editModal.hide();
      location.reload();
    } else {
      alert('Error updating announcement');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error updating announcement');
  });
});

// Show Modal
let showModal;
let currentAnnouncementId;
document.addEventListener('DOMContentLoaded', function() {
  showModal = new bootstrap.Modal(document.getElementById('showModal'));
});

function openShowModal(id) {
  currentAnnouncementId = id;
  fetch(`/admin/announcements/${id}/modal/show`, {
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    const announcement = data.announcement;
    
    // Type badge
    const typeBadges = {
      'urgent': '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Urgent</span>',
      'event': '<span class="badge bg-info"><i class="bi bi-calendar-event"></i> Event</span>',
      'general': '<span class="badge bg-secondary"><i class="bi bi-info-circle"></i> General</span>'
    };
    document.getElementById('show_type_badge').innerHTML = typeBadges[announcement.type] || typeBadges['general'];
    
    // Published status
    const statusClass = announcement.is_published ? 'text-success' : 'text-muted';
    const statusIcon = announcement.is_published ? 'bi-check-circle-fill' : 'bi-eye-slash';
    const statusText = announcement.is_published ? 'Published' : 'Draft';
    document.getElementById('show_published_status').className = statusClass;
    document.getElementById('show_published_status').innerHTML = `<small><i class="bi ${statusIcon}"></i> ${statusText}</small>`;
    
    // Calendar badge
    document.getElementById('show_calendar_badge').style.display = announcement.event_id ? 'inline-flex' : 'none';
    
    // Title and content
    document.getElementById('show_title').textContent = announcement.title;
    document.getElementById('show_content').textContent = announcement.content;
    document.getElementById('show_creator').textContent = (announcement.creator?.admin?.first_name || '') + ' ' + (announcement.creator?.admin?.last_name || '') || 'Unknown';
    document.getElementById('show_date').textContent = new Date(announcement.created_at).toLocaleDateString('en-US', { 
      year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
    });
    
    // Dates
    let datesHtml = '';
    if (announcement.publish_date) {
      datesHtml += `<div class="col-md-6"><strong>Publish Date:</strong> ${new Date(announcement.publish_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>`;
    }
    if (announcement.expire_date) {
      datesHtml += `<div class="col-md-6"><strong>Expire Date:</strong> ${new Date(announcement.expire_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>`;
    }
    document.getElementById('show_dates').innerHTML = datesHtml;
    
    // Delete button
    document.getElementById('deleteBtn').onclick = function() {
      if (confirm('Are you sure you want to delete this announcement?')) {
        fetch(`/admin/announcements/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(response => response.json())
        .then(data => {
          showModal.hide();
          location.reload();
        });
      }
    };
    
    showModal.show();
  });
}

// Alert functions
const msgBox = document.getElementById('message');

function showMsg(obj) {
  msgBox.style.display = 'block';
  const isSuccess = obj?.success === true;
  const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
  const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
  msgBox.className = `alert ${alertClass} shadow-sm border-0`;
  msgBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${obj?.message || JSON.stringify(obj)}`;
}

// Update create form to show alert
document.getElementById('createForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  fetch('{{ route("admin.announcements.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(err => Promise.reject(err));
    }
    return response.json();
  })
  .then(data => {
    showMsg({ success: true, message: 'Announcement created successfully!' });
    createModal.hide();
    setTimeout(() => location.reload(), 1500);
  })
  .catch(error => {
    console.error('Error:', error);
    showMsg({ success: false, message: error.message || 'Error creating announcement' });
  });
});
</script>
@endsection
