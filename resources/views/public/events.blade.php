@extends('layouts.public', ['currentRoute' => 'public.news'])

@push('styles')
<style>
.page-header {
    text-align: center;
    margin-bottom: 40px;
}
.page-header .btn-primary { background: #FFD700; color: #1f2937; border: none; font-weight: 600; }
.page-header .btn-primary:hover { background: #FFC107; }

.section-title-custom {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--mis-yellow);
}

.event-card {
    padding: 20px;
    margin-bottom: 16px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
    transition: all 0.3s;
}

.event-card:hover {
    transform: translateY(-4px);
    background: rgba(255, 255, 255, 0.15);
}

.event-date-box {
    min-width: 60px;
    text-align: center;
    padding: 12px 8px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.event-month {
    font-size: 0.7rem;
    text-transform: uppercase;
    color: var(--mis-yellow);
}

.event-day {
    font-size: 1.5rem;
    font-weight: 700;
}

.event-details {
    flex: 1;
}

.event-title {
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 8px;
}

.event-description {
    font-size: 0.9rem;
    opacity: 0.75;
    margin-bottom: 12px;
    line-height: 1.5;
}

.event-meta {
    font-size: 0.8rem;
    opacity: 0.7;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.event-meta i {
    margin-right: 4px;
}

.event-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-upcoming {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}

.status-ongoing {
    background: rgba(34, 197, 94, 0.2);
    color: #4ade80;
}

.status-past {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 20px;
}

.calendar-note {
    padding: 16px;
    border-radius: 12px;
    background: rgba(254, 238, 145, 0.1);
    border: 1px solid rgba(254, 238, 145, 0.2);
    margin-bottom: 30px;
}

.calendar-note i {
    color: var(--mis-yellow);
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Events Calendar</h1>
    <p class="opacity-75">Upcoming events and activities in Barangay Bucandala 1</p>
    <a href="{{ route('public.news') }}" class="btn btn-primary mt-2">
        <i class="bi bi-calendar3 me-2"></i>View Calendar
    </a>
</div>

<div class="calendar-note">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Note:</strong> All events are subject to change. Please contact the barangay office for confirmation.
</div>

@if($upcomingEvents->count() > 0)
<div class="glass p-4 mb-5">
    <h2 class="section-title-custom"><i class="bi bi-calendar-check me-2"></i>Upcoming Events</h2>
    <div class="row">
        @foreach($upcomingEvents as $event)
            <div class="col-md-6 mb-3">
                <div class="event-card glass">
                    <div class="event-date-box">
                        <div class="event-month">{{ \Carbon\Carbon::parse($event->start_datetime)->format('M') }}</div>
                        <div class="event-day">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d') }}</div>
                    </div>
                    <div class="event-details">
                        <h5 class="event-title">{{ $event->title }}</h5>
                        <p class="event-description">{{ Str::limit($event->description, 100) }}</p>
                        <div class="event-meta">
                            <span><i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}
                                @if($event->end_datetime)
                                    - {{ \Carbon\Carbon::parse($event->end_datetime)->format('g:i A') }}
                                @endif
                            </span>
                            <span><i class="bi bi-geo-alt"></i>{{ $event->location ?? 'Barangay Hall' }}</span>
                        </div>
                        @if($event->is_pinned)
                            <span class="event-status status-ongoing mt-2"><i class="bi bi-pin-angle me-1"></i>Featured</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($pastEvents->count() > 0)
<div class="glass p-4">
    <h2 class="section-title-custom"><i class="bi bi-clock-history me-2"></i>Past Events</h2>
    <div class="row">
        @foreach($pastEvents as $event)
            <div class="col-md-6 mb-3">
                <div class="event-card glass" style="opacity: 0.7;">
                    <div class="event-date-box">
                        <div class="event-month">{{ \Carbon\Carbon::parse($event->start_datetime)->format('M') }}</div>
                        <div class="event-day">{{ \Carbon\Carbon::parse($event->start_datetime)->format('d') }}</div>
                    </div>
                    <div class="event-details">
                        <h5 class="event-title">{{ $event->title }}</h5>
                        <p class="event-description">{{ Str::limit($event->description, 100) }}</p>
                        <div class="event-meta">
                            <span><i class="bi bi-clock"></i>
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}
                            </span>
                            <span><i class="bi bi-geo-alt"></i>{{ $event->location ?? 'Barangay Hall' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($upcomingEvents->count() === 0 && $pastEvents->count() === 0)
<div class="empty-state">
    <i class="bi bi-calendar-event"></i>
    <h3>No Events Yet</h3>
    <p class="opacity-75">Check back later for upcoming events and activities.</p>
    <a href="{{ route('public.news') }}" class="btn btn-primary mt-3">
        <i class="bi bi-calendar3 me-2"></i>View Calendar
    </a>
</div>
@endif
@endsection
