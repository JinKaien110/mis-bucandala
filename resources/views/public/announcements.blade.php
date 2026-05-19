@extends('layouts.public', ['currentRoute' => 'public.news'])

@push('styles')
<style>
.page-header {
    text-align: center;
    margin-bottom: 40px;
}
.page-header .btn-primary { background: #FFD700; color: #1f2937; border: none; font-weight: 600; }
.page-header .btn-primary:hover { background: #FFC107; }

.announcement-card {
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.3s;
}

.announcement-card:hover {
    transform: translateY(-4px);
    background: rgba(255, 255, 255, 0.15);
}

.announcement-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.badge-important {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5;
}

.badge-event {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}

.badge-general {
    background: rgba(40, 167, 69, 0.2);
    color: #86efac;
}

.badge-news {
    background: rgba(168, 85, 247, 0.2);
    color: #d8b4fe;
}

.announcement-date {
    font-size: 0.85rem;
    opacity: 0.7;
    margin-bottom: 8px;
}

.announcement-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 12px;
}

.announcement-content {
    font-size: 1rem;
    line-height: 1.7;
    opacity: 0.85;
}

.announcement-meta {
    display: flex;
    gap: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.85rem;
    opacity: 0.7;
}

.pinned-card {
    border: 2px solid var(--mis-yellow);
}

.pinned-card::before {
    content: 'PINNED';
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--mis-yellow);
    color: #1f2937;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
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
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Announcements</h1>
    <p class="opacity-75">Stay updated with the latest news and announcements from Barangay Bucandala 1</p>
    <a href="{{ route('public.news') }}" class="btn btn-primary mt-2">
        <i class="bi bi-calendar3 me-2"></i>View Calendar
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        @forelse($announcements as $announcement)
            <div class="announcement-card glass {{ $announcement->is_pinned ? 'pinned-card' : '' }} position-relative">
                <span class="announcement-badge 
                    @if($announcement->priority === 'high') badge-important
                    @elseif($announcement->type === 'event') badge-event
                    @elseif($announcement->type === 'news') badge-news
                    @else badge-general @endif">
                    {{ $announcement->type ?? 'announcement' }}
                </span>
                <div class="announcement-date">
                    <i class="bi bi-calendar me-1"></i>
                    {{ \Carbon\Carbon::parse($announcement->created_at)->format('F d, Y') }}
                </div>
                <h3 class="announcement-title">{{ $announcement->title }}</h3>
                <div class="announcement-content">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
                <div class="announcement-meta">
                    <span><i class="bi bi-person me-1"></i>{{ $announcement->creator->name ?? 'Admin' }}</span>
                    @if($announcement->is_pinned)
                        <span><i class="bi bi-pin-angle me-1"></i>Pinned</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-megaphone"></i>
                <h3>No Announcements Yet</h3>
                <p class="opacity-75">Check back later for updates from the barangay.</p>
                <a href="{{ route('public.news') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-calendar3 me-2"></i>View Calendar
                </a>
            </div>
        @endforelse
    </div>

    <div class="col-lg-4">
        <div class="glass p-4 mb-4">
            <h5 class="mb-3"><i class="bi bi-bell me-2"></i>Quick Links</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('public.home') }}" class="btn btn-glass btn-sm">
                    <i class="bi bi-house me-2"></i>Home
                </a>
                <a href="{{ route('public.news') }}" class="btn btn-glass btn-sm">
                    <i class="bi bi-calendar-event me-2"></i>News & Events
                </a>
                <a href="{{ route('public.services') }}" class="btn btn-glass btn-sm">
                    <i class="bi bi-file-earmark-text me-2"></i>Services
                </a>
                <a href="{{ route('public.contact') }}" class="btn btn-glass btn-sm">
                    <i class="bi bi-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>

        <div class="glass p-4">
            <h5 class="mb-3"><i class="bi bi-clock me-2"></i>Office Hours</h5>
            <div class="d-flex justify-content-between mb-2">
                <span>Monday - Friday</span>
                <span>8:00 AM - 5:00 PM</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Saturday</span>
                <span>8:00 AM - 12:00 PM</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Sunday</span>
                <span>Closed</span>
            </div>
        </div>
    </div>
</div>
@endsection
