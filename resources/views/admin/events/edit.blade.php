@extends('layouts.admin')

@section('title', 'Edit Event - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Edit Event</h4>
      <p class="text-muted mb-0">Update event details</p>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.events.update', $event) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Event Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
              </div>

              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $event->description) }}</textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Event Type</label>
                <select name="type" class="form-select" required>
                  <option value="general" {{ $event->type === 'general' ? 'selected' : '' }}>General</option>
                  <option value="meeting" {{ $event->type === 'meeting' ? 'selected' : '' }}>Meeting</option>
                  <option value="program" {{ $event->type === 'program' ? 'selected' : '' }}>Program</option>
                  <option value="reminder" {{ $event->type === 'reminder' ? 'selected' : '' }}>Reminder</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}">
              </div>

              <div class="col-md-6">
                <label class="form-label">Start Date & Time</label>
                <input type="datetime-local" name="start_datetime" class="form-control" value="{{ old('start_datetime', $event->start_datetime->format('Y-m-d\TH:i')) }}" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">End Date & Time</label>
                <input type="datetime-local" name="end_datetime" class="form-control" value="{{ old('end_datetime', $event->end_datetime?->format('Y-m-d\TH:i')) }}">
              </div>

              <div class="col-md-6">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" name="is_all_day" id="is_all_day" value="1" {{ $event->is_all_day ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_all_day">
                    All Day Event
                  </label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ $event->is_published ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_published">
                    Publish immediately
                  </label>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Reminder</label>
                <select name="reminder" class="form-select">
                  <option value="">No reminder</option>
                  <option value="15min" {{ $event->reminder === '15min' ? 'selected' : '' }}>15 minutes before</option>
                  <option value="30min" {{ $event->reminder === '30min' ? 'selected' : '' }}>30 minutes before</option>
                  <option value="1hour" {{ $event->reminder === '1hour' ? 'selected' : '' }}>1 hour before</option>
                  <option value="1day" {{ $event->reminder === '1day' ? 'selected' : '' }}>1 day before</option>
                </select>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary">Update Event</button>
              <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
