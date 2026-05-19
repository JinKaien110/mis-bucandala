@extends('layouts.admin')

@section('title', 'Announcement Details - Barangay MIS')

@section('content')
<div class="page-surface">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Announcement Details</h4>
      <p class="text-muted mb-0">{{ $announcement->title }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary">
        <i class="bi bi-pencil"></i> Edit
      </a>
      <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Archive this announcement?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</button>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="d-flex gap-2 mb-4">
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
          {!! $announcement->is_published ? '<i class="bi bi-check-circle-fill"></i> Published' : '<i class="bi bi-eye-slash"></i> Draft' !!}
        </span>
        @if($announcement->event_id)
          <span class="badge bg-danger"><i class="bi bi-calendar"></i> On Calendar</span>
        @endif
      </div>

      <h3>{{ $announcement->title }}</h3>

      <div class="text-muted mb-4">
        <small>
          <i class="bi bi-person"></i> Created by {{ $announcement->creator->admin->first_name ?? 'Unknown' }} {{ $announcement->creator->admin->last_name ?? '' }} |
          <i class="bi bi-clock"></i> {{ $announcement->created_at->format('F d, Y h:i A') }}
        </small>
      </div>

      <hr>

      <div class="content mt-4">
        {!! nl2br(e($announcement->content)) !!}
      </div>

      @if($announcement->publish_date || $announcement->expire_date)
        <hr>
        <div class="row mt-3">
          @if($announcement->publish_date)
            <div class="col-md-6">
              <strong>Publish Date:</strong> {{ $announcement->publish_date->format('F d, Y') }}
            </div>
          @endif
          @if($announcement->expire_date)
            <div class="col-md-6">
              <strong>Expire Date:</strong> {{ $announcement->expire_date->format('F d, Y') }}
            </div>
          @endif
        </div>
      @endif
    </div>
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Announcements
    </a>
  </div>
</div>
@endsection
