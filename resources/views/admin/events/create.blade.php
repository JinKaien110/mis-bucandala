@extends('layouts.admin')

@section('title', 'Add Event - Barangay MIS')

@section('content')
<div class="page-surface">
  <!-- Alert Message -->
  <div id="message" class="alert" style="display:none;"></div>

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Add New Event</h4>
      <p class="text-muted mb-0">Create a new calendar event</p>
    </div>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Back to Calendar
    </a>
  </div>

  <div class="row">
    <div class="col-lg-10 mx-auto">
      <!-- Main Form Card -->
      <div class="card border-0 shadow-lg">
        <div class="card-header event-form-header py-3">
          <div class="d-flex align-items-center">
            <i class="bi bi-calendar-plus fs-4 me-2"></i>
            <h5 class="mb-0">Event Details</h5>
          </div>
        </div>
        <div class="card-body p-4">
          <form id="createForm" method="POST" action="{{ route('admin.events.store') }}">
            @csrf

            <!-- Event Title -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-type me-1"></i> Event Title
              </label>
              <input type="text" name="title" class="form-control form-control-lg" 
                     placeholder="e.g., Monthly Flag Ceremony" required>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-text-paragraph me-1"></i> Description
              </label>
              <textarea name="description" class="form-control" rows="3" 
                        placeholder="Event details..."></textarea>
            </div>

            <!-- Event Type & Location -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-tag me-1"></i> Event Type
                </label>
                <select name="type" class="form-select" required>
                  <option value="general">📌 General</option>
                  <option value="meeting">👥 Meeting</option>
                  <option value="program">🎉 Program</option>
                  <option value="reminder">⏰ Reminder</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-geo-alt me-1"></i> Location
                </label>
                <input type="text" name="location" class="form-control" 
                       placeholder="e.g., Barangay Hall">
              </div>
            </div>

            <!-- Date & Time -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-play-circle me-1"></i> Start Date & Time
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light">
                    <i class="bi bi-calendar-event"></i>
                  </span>
                  <input type="datetime-local" name="start_datetime" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="bi bi-stop-circle me-1"></i> End Date & Time
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light">
                    <i class="bi bi-calendar-event"></i>
                  </span>
                  <input type="datetime-local" name="end_datetime" class="form-control">
                </div>
              </div>
            </div>

            <!-- Checkboxes -->
            <div class="row mb-4">
              <div class="col-md-6">
                <div class="form-check custom-check-box">
                  <input class="form-check-input" type="checkbox" name="is_all_day" id="is_all_day" value="1">
                  <label class="form-check-label" for="is_all_day">
                    <span class="check-icon"><i class="bi bi-check"></i></span>
                    All Day Event
                  </label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-check custom-check-box">
                  <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" checked>
                  <label class="form-check-label" for="is_published">
                    <span class="check-icon"><i class="bi bi-check"></i></span>
                    Publish immediately
                  </label>
                </div>
              </div>
            </div>

            <!-- Reminder -->
            <div class="mb-4">
              <label class="form-label fw-semibold">
                <i class="bi bi-bell me-1"></i> Reminder
              </label>
              <select name="reminder" class="form-select">
                <option value="">🔕 No reminder</option>
                <option value="15min">⏱️ 15 minutes before</option>
                <option value="30min">⏱️ 30 minutes before</option>
                <option value="1hour">⏱️ 1 hour before</option>
                <option value="1day">📅 1 day before</option>
              </select>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3 mt-4 pt-3 border-top">
              <button type="submit" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-check-circle me-2"></i> Create Event
              </button>
              <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-x-circle me-2"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>

      <!-- Tips Card -->
      <div class="card border-0 shadow-sm mt-4 bg-light">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">
            <i class="bi bi-lightbulb me-2 text-warning"></i> Tips
          </h6>
          <ul class="mb-0 small text-muted">
            <li>Fill in all required fields to create an event</li>
            <li>Select "All Day" if the event spans the entire day</li>
            <li>Check "Publish immediately" to make it visible on the calendar</li>
            <li>Set a reminder to get notified before the event</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const msgBox = document.getElementById('message');

  function showMsg(obj) {
    msgBox.style.display = 'block';
    const isSuccess = obj?.success === true;
    const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
    msgBox.className = `alert ${alertClass} shadow-sm border-0`;
    msgBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${obj?.message || JSON.stringify(obj)}`;
  }

  function hideMsg() {
    msgBox.style.display = 'none';
    msgBox.textContent = '';
  }

  document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      });
      
      const data = await res.json();
      
      if (data.success || res.ok) {
        showMsg({ success: true, message: data.message || 'Event created successfully!' });
        setTimeout(() => {
          window.location.href = '{{ route("admin.events.index") }}';
        }, 1500);
      } else {
        showMsg({ success: false, message: data.message || 'Error creating event' });
      }
    } catch (err) {
      showMsg({ success: false, message: 'Error: ' + err.message });
    }
  });
</script>

<style>
  .event-form-header {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
  }
  .event-form-header i {
    opacity: 0.9;
  }
  .form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
  }
  .input-group-text {
    border-color: #dee2e6;
  }
  .custom-check-box {
    background: #f8f9fa;
    padding: 12px 16px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  .custom-check-box:hover {
    background: #e9ecef;
  }
  .custom-check-box .form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    margin-top: 0;
    cursor: pointer;
  }
  .custom-check-box .form-check-label {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .custom-check-box .check-icon {
    display: none;
    width: 18px;
    height: 18px;
    background: #0d6efd;
    color: white;
    border-radius: 4px;
    font-size: 12px;
    align-items: center;
    justify-content: center;
  }
  .custom-check-box .form-check-input:checked + .form-check-label .check-icon {
    display: flex;
  }
  .btn-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border: none;
    transition: all 0.2s ease;
  }
  .btn-primary:hover {
    background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
  }
  .card {
    border-radius: 12px;
  }
</style>
@endsection
