@extends('layouts.admin')

@section('title', 'Event Details - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">{{ $event->title }}</h4>
      <p class="text-muted mb-0">
        <span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'program' ? 'success' : 'secondary') }}">
          {{ ucfirst($event->type) }}
        </span>
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
          <h5 class="mb-0">Event Information</h5>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <td class="text-muted" style="width: 180px;">Title</td>
              <td class="fw-semibold">{{ $event->title }}</td>
            </tr>
            <tr>
              <td class="text-muted">Type</td>
              <td><span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'program' ? 'success' : 'secondary') }}">{{ ucfirst($event->type) }}</span></td>
            </tr>
            <tr>
              <td class="text-muted">Date & Time</td>
              <td>
                {{ $event->start_datetime->format('F d, Y') }}
                @if($event->is_all_day)
                  <span class="badge bg-info">All Day</span>
                @else
                  {{ $event->start_datetime->format('h:i A') }}
                  @if($event->end_datetime)
                    - {{ $event->end_datetime->format('h:i A') }}
                  @endif
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted">Location</td>
              <td>{{ $event->location ?? '-' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Status</td>
              <td>
                @if($event->is_published)
                  <span class="badge bg-success">Published</span>
                @else
                  <span class="badge bg-secondary">Draft</span>
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted">Reminder</td>
              <td>{{ $event->reminder ? ucfirst($event->reminder) : 'None' }}</td>
            </tr>
            @if($event->description)
            <tr>
              <td class="text-muted">Description</td>
              <td>{{ $event->description }}</td>
            </tr>
            @endif
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">Actions</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Archive this event?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger w-100">
              <i class="bi bi-trash"></i> Delete Event
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
