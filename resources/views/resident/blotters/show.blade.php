@extends('layouts.resident', ['currentRoute' => 'resident.cases'])

@section('content')
@php
  $case = $blotter->case;
  $status = $case?->status ?? $blotter->status;
  $statusClass = match(strtolower($status)) {
    'settled', 'released' => 'bg-success',
    'dismissed', 'cancelled', 'archived' => 'bg-secondary',
    'scheduled', 'ongoing' => 'bg-warning text-dark',
    default => 'bg-info text-dark',
  };
@endphp
<div class="container">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">E-Blotter Report</h2>
      <p class="opacity-75 mb-0">Reference {{ $blotter->blotter_no }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('resident.cases') }}" class="btn btn-glass">My Cases</a>
      <a href="{{ route('resident.dashboard') }}" class="btn btn-glass">Dashboard</a>
    </div>
  </div>

  @if(session('success'))
    <div class="blotter-submission-alert mb-4" role="status">
      <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
    </div>
  @endif

  <section class="glass p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <div class="small text-uppercase opacity-75">Blotter Reference</div>
        <h3 class="mb-0">{{ $blotter->blotter_no }}</h3>
      </div>
      <span class="badge {{ $statusClass }} px-3 py-2">{{ ucfirst($status) }}</span>
    </div>
    <hr class="border-light opacity-25">
    <div class="row g-3">
      <div class="col-md-6"><strong>Incident Type</strong><div>{{ $blotter->incident_type }}</div></div>
      <div class="col-md-6"><strong>Report Filed</strong><div>{{ $blotter->created_at->format('M d, Y h:i A') }}</div></div>
      <div class="col-md-6"><strong>Incident Date &amp; Time</strong><div>{{ $blotter->incident_date->format('M d, Y h:i A') }}</div></div>
      <div class="col-md-6"><strong>Location</strong><div>{{ $blotter->incident_location }}</div></div>
      <div class="col-md-6"><strong>Respondent</strong><div>{{ $blotter->respondent_name ?: 'Not provided' }}</div></div>
      <div class="col-md-6"><strong>Respondent Address Line</strong><div>{{ $blotter->respondent_address_line ?: 'Not provided' }}</div></div>
      <div class="col-12"><strong>Report</strong><div class="mt-1" style="white-space: pre-wrap;">{{ $blotter->narrative }}</div></div>
      @if($blotter->remarks)
        <div class="col-12"><strong>Remarks</strong><div class="mt-1" style="white-space: pre-wrap;">{{ $blotter->remarks }}</div></div>
      @endif
    </div>
  </section>

  @if($case)
    <section class="glass p-4">
      <h4 class="mb-3">Case Tracking <span class="small opacity-75">({{ $case->case_no }})</span></h4>
      <p class="small opacity-75">Hearing schedules are recorded below; a separate summon date is not stored in the current system.</p>
      @if($case->hearings->isNotEmpty())
        <div class="list-group list-group-flush">
          @foreach($case->hearings as $hearing)
            <div class="list-group-item bg-transparent text-white border-light border-opacity-25 px-0">
              <div class="d-flex justify-content-between flex-wrap gap-2">
                <strong>{{ $hearing->scheduled_at->format('M d, Y h:i A') }}</strong>
                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $hearing->status)) }}</span>
              </div>
              <div class="mt-1"><i class="bi bi-geo-alt me-1"></i>{{ $hearing->location ?: 'Location not specified' }}</div>
              @if($hearing->notes)<div class="small opacity-75 mt-1">{{ $hearing->notes }}</div>@endif
            </div>
          @endforeach
        </div>
      @else
        <p class="mb-0 opacity-75">No hearing has been scheduled yet.</p>
      @endif
    </section>
  @else
    <div class="glass p-4">
      <h5><i class="bi bi-hourglass-split me-2"></i>Awaiting Barangay Review</h5>
      <p class="mb-0 opacity-75">This report is filed, but has not yet been opened as a case. Case tracking and hearing schedules will appear here if staff open a case.</p>
    </div>
  @endif
</div>
@endsection

@push('styles')
<style>
  .blotter-submission-alert {
    padding: 1rem 1.25rem;
    color: #ffffff;
    background: rgba(13, 71, 161, 0.92);
    border: 1px solid rgba(134, 239, 172, 0.55);
    border-left: 5px solid #86efac;
    border-radius: 0.75rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
  }
</style>
@endpush
