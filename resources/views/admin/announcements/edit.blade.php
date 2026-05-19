@extends('layouts.admin')

@section('title', 'Edit Announcement - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Edit Announcement</h4>
      <p class="text-muted mb-0">Update announcement</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
          <label class="form-label">Title *</label>
          <input type="text" name="title" class="form-control" required value="{{ $announcement->title }}">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select" required>
              <option value="general" {{ $announcement->type == 'general' ? 'selected' : '' }}>General</option>
              <option value="event" {{ $announcement->type == 'event' ? 'selected' : '' }}>Event</option>
              <option value="urgent" {{ $announcement->type == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Status</label>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="is_published" value="1" id="published" {{ $announcement->is_published ? 'checked' : '' }}>
              <label class="form-check-label" for="published">Published</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Publish Date</label>
            <input type="date" name="publish_date" class="form-control" value="{{ $announcement->publish_date?->format('Y-m-d') }}">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Expire Date</label>
            <input type="date" name="expire_date" class="form-control" value="{{ $announcement->expire_date?->format('Y-m-d') }}">
          </div>
        </div>
        <div class="mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="show_on_calendar" value="1" id="show_on_calendar" {{ $announcement->event_id ? 'checked' : '' }}>
            <label class="form-check-label" for="show_on_calendar">
              Show on Events Calendar
            </label>
            <small class="text-muted d-block">Checking this will display this announcement on the events calendar</small>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Content *</label>
          <textarea name="content" class="form-control" rows="6" required>{{ $announcement->content }}</textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Update Announcement</button>
          <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
