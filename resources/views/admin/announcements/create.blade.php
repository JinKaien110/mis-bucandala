@extends('layouts.admin')

@section('title', 'New Announcement - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">New Announcement</h4>
      <p class="text-muted mb-0">Create a new announcement</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.announcements.store') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select" required>
              <option value="general">General</option>
              <option value="event">Event</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="is_published" value="1" id="published">
              <label class="form-check-label" for="published">Publish immediately</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Publish Date</label>
            <input type="date" name="publish_date" class="form-control">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Expire Date</label>
            <input type="date" name="expire_date" class="form-control">
          </div>
        </div>
        <div class="mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="show_on_calendar" value="1" id="show_on_calendar">
            <label class="form-check-label" for="show_on_calendar">
              Show on Events Calendar
            </label>
            <small class="text-muted d-block">Checking this will display this announcement on the events calendar</small>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Content *</label>
          <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Create Announcement</button>
          <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
