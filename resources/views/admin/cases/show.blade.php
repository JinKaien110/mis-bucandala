@extends('layouts.admin')

@section('title', 'Case Details - Barangay MIS')

@section('content')
@php
  $status = strtolower($case->status);
  $isClosed = in_array($status, ['settled','dismissed','referred','archived'], true);
  $hearingCount = $case->hearings->count();
  $maxHearings = 3;
  $isHearingLimitReached = $hearingCount >= $maxHearings;
  $canScheduleHearing = !$isClosed && !$isHearingLimitReached;

  $badgeClass = match($status) {
    'ongoing' => 'bg-warning-subtle text-warning border-warning-subtle',
    'settled' => 'bg-success-subtle text-success border-success-subtle',
    'dismissed' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
    'referred' => 'bg-info-subtle text-info border-info-subtle',
    'archived' => 'bg-dark-subtle text-dark border-dark-subtle',
    default => 'bg-primary-subtle text-primary border-primary-subtle',
  };
@endphp

<div class="page-surface">
  
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1 fw-bold text-primary">Case #{{ $case->case_no }}</h4>
      <p class="text-muted mb-0">
        From Blotter: <a href="{{ route('admin.blotters.show', $case->blotter) }}" class="text-decoration-none fw-bold">{{ $case->blotter->blotter_no }}</a>
        <span class="mx-2">•</span>
        <span class="badge {{ $badgeClass }} border rounded-pill">{{ strtoupper($case->status) }}</span>
      </p>
    </div>

    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="{{ route('admin.cases.index') }}">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
      @if($status === 'referred')
        <a class="btn btn-outline-dark" href="{{ route('admin.cases.cert_to_file_action.docx', $case) }}">
          <i class="bi bi-file-earmark-word me-1"></i> Download Certification
        </a>
      @endif
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-check-circle-fill fs-4 me-2"></i>
        <div>{{ session('success') }}</div>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center shadow-sm border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
        <div>{{ session('error') }}</div>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4">
      <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Error</div>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Locked Notice --}}
  @if($isClosed)
    <div class="alert alert-secondary d-flex align-items-center shadow-sm border-0 mb-4">
      <i class="bi bi-lock-fill fs-4 me-2"></i>
      <div>This case is <strong>{{ strtoupper($case->status) }}</strong>. Editing and scheduling are disabled.</div>
    </div>
  @endif

  {{-- Hearing limit notice --}}
  @if(!$isClosed && $isHearingLimitReached)
    <div class="alert alert-warning d-flex align-items-center shadow-sm border-0 mb-4">
      <i class="bi bi-exclamation-circle-fill fs-4 me-2"></i>
      <div>Hearing limit reached ({{ $maxHearings }} hearings). Scheduling is disabled.</div>
    </div>
  @endif

  <div class="row g-4">
    <!-- Left Column: Actions -->
    <div class="col-lg-4">
      
      <!-- Schedule Hearing Card -->
      <div class="card border-0 shadow-sm mb-4 {{ !$canScheduleHearing ? 'opacity-75' : '' }}">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-plus me-2"></i>Schedule Hearing</h6>
            <span class="badge bg-light text-muted border">{{ $hearingCount }}/{{ $maxHearings }}</span>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.cases.hearings.store', $case) }}">
            @csrf
            <input type="hidden" name="status" value="scheduled">

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Date & Time <span class="text-danger">*</span></label>
                <input type="datetime-local" name="scheduled_at" class="form-control" required @disabled(!$canScheduleHearing)>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Location</label>
                <input name="location" class="form-control" value="Barangay Hall" @disabled(!$canScheduleHearing)>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..." @disabled(!$canScheduleHearing)></textarea>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="send_notification" value="1" id="sendNotification" checked @disabled(!$canScheduleHearing)>
              <label class="form-check-label small" for="sendNotification">
                Notify parties via email
              </label>
            </div>

            <button class="btn btn-primary w-100" @disabled(!$canScheduleHearing)>
              <i class="bi bi-calendar-check me-1"></i> Add Hearing
            </button>
          </form>
        </div>
      </div>

      <!-- Close Case Card -->
      <div class="card border-0 shadow-sm {{ $isClosed ? 'opacity-75' : '' }}">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Close Case</h6>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.cases.close', $case) }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Close As <span class="text-danger">*</span></label>
                <select class="form-select" name="status" required @disabled($isClosed)>
                  <option value="">-- Select Outcome --</option>
                  <option value="settled">Settled</option>
                  <option value="referred">Certified to File Action</option>
                  <option value="dismissed">Dismissed</option>
                  <option value="archived">Archived</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Resolution Summary <span class="text-danger">*</span></label>
                <textarea name="resolution_summary" rows="3" class="form-control" required placeholder="Summary of outcome..." @disabled($isClosed)>{{ old('resolution_summary') }}</textarea>
            </div>

            <button class="btn btn-danger w-100" @disabled($isClosed)>
              <i class="bi bi-check-circle me-1"></i> Finalize Case
            </button>
          </form>
        </div>
      </div>

    </div>

    <!-- Right Column: Hearings List -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Hearing History</h6>
        </div>
        <div class="card-body p-0">
          @if($hearingCount === 0)
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                <p class="mb-0">No hearings scheduled yet.</p>
            </div>
          @else
            <div class="list-group list-group-flush">
                @foreach($case->hearings as $hearing)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">
                                    {{ $hearing->scheduled_at->format('F d, Y') }} 
                                    <span class="text-muted fw-normal">at {{ $hearing->scheduled_at->format('h:i A') }}</span>
                                </h6>
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $hearing->location }}
                                </div>
                                @if($hearing->notes)
                                    <div class="small bg-light p-2 rounded border mt-2">
                                        {{ $hearing->notes }}
                                    </div>
                                @endif
                            </div>
                            <span class="badge bg-light text-dark border">
                                {{ ucfirst($hearing->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
