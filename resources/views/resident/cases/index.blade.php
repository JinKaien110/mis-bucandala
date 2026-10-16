@extends('layouts.resident', ['currentRoute' => 'resident.cases'])

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h2 class="mb-1">My Cases &amp; Blotter History</h2>
      <p class="opacity-75 mb-0">View your submitted blotter reports and follow case progress, hearing schedules, and outcomes.</p>
    </div>
    <a href="{{ route('resident.blotters.create') }}" class="btn btn-warning fw-semibold"><i class="bi bi-plus-lg me-2"></i>Submit E-Blotter</a>
  </div>

  @if($blotters->isEmpty())
    <div class="glass p-5 text-center">
      <i class="bi bi-folder2-open fs-1 opacity-75"></i>
      <h4 class="mt-3">No blotter reports yet</h4>
      <p class="opacity-75">Reports you submit will appear here, along with their case and hearing history when available.</p>
      <a href="{{ route('resident.blotters.create') }}" class="btn btn-warning fw-semibold"><i class="bi bi-journal-plus me-2"></i>Submit an E-Blotter Report</a>
    </div>
  @else
    <div class="row g-3">
      @foreach($blotters as $blotter)
        @php
          $case = $blotter->case;
          $status = strtolower($case?->status ?? $blotter->status ?? 'filed');
          $statusClass = match($status) {
            'ongoing', 'open' => 'status-active',
            'scheduled' => 'status-scheduled',
            'settled' => 'status-settled',
            'dismissed', 'archived' => 'status-closed',
            'referred' => 'status-referred',
            default => 'status-filed',
          };
          $statusLabel = match($status) {
            'referred' => 'Certified to File Action',
            default => ucfirst($status),
          };
        @endphp
        <div class="col-12">
          <article class="blotter-history-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h5 class="mb-1">Blotter {{ $blotter->blotter_no }}</h5>
                <div class="small history-meta">
                  <span><i class="bi bi-exclamation-circle me-1"></i>{{ $blotter->incident_type }}</span>
                  <span class="mx-2">&middot;</span>
                  <span><i class="bi bi-calendar-event me-1"></i>{{ $blotter->incident_date->format('M d, Y') }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="history-status {{ $statusClass }}">{{ $statusLabel }}</span>
                <a href="{{ route('resident.blotters.show', $blotter) }}" class="btn btn-glass btn-sm">
                  <i class="bi bi-eye me-1"></i>View Details
                </a>
              </div>
            </div>
            <div class="history-meta small mt-2">
              @if($case)
                Case {{ $case->case_no }}
                @if($case->closed_at)
                  <span class="mx-1">&middot;</span>Closed {{ $case->closed_at->format('M d, Y') }}
                @elseif($case->hearings->isNotEmpty())
                  <span class="mx-1">&middot;</span>{{ $case->hearings->count() }} {{ \Illuminate\Support\Str::plural('hearing', $case->hearings->count()) }} recorded
                @else
                  <span class="mx-1">&middot;</span>Case opened {{ $case->opened_at?->format('M d, Y') ?: 'date unavailable' }}
                @endif
              @else
                Submitted {{ $blotter->created_at->format('M d, Y') }} &middot; Awaiting case review
              @endif
            </div>
          </article>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection

@push('styles')
<style>
  .blotter-history-card {
    padding: 1rem 1.25rem;
    color: #ffffff;
    background: rgba(13, 71, 161, 0.82);
    border: 1px solid rgba(255, 215, 0, 0.32);
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
    transition: background 0.2s ease, border-color 0.2s ease;
  }

  .blotter-history-card:hover {
    background: rgba(13, 71, 161, 0.94);
    border-color: rgba(255, 215, 0, 0.6);
  }

  .blotter-history-card h5,
  .blotter-history-card .history-meta {
    color: rgba(255, 255, 255, 0.88);
  }

  .history-status {
    display: inline-block;
    padding: 0.35rem 0.65rem;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  .status-filed { color: #ffffff; background: #344256; border-color: #718096; }
  .status-active { color: #1f2937; background: #facc15; border-color: #fde047; }
  .status-scheduled { color: #ffffff; background: #1d4ed8; border-color: #93c5fd; }
  .status-settled { color: #ffffff; background: #15803d; border-color: #86efac; }
  .status-closed { color: #ffffff; background: #4b5563; border-color: #cbd5e1; }
  .status-referred { color: #ffffff; background: #0e7490; border-color: #67e8f9; }

  @media (max-width: 575.98px) {
    .blotter-history-card {
      padding: 0.875rem;
    }

    .blotter-history-card .btn {
      white-space: nowrap;
    }
  }
</style>
@endpush
