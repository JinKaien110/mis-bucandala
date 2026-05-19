@extends('layouts.public', ['currentRoute' => 'public.news'])

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/main.min.css">
<style>
/* Pagination Override for this page */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin: 30px 0;
  flex-wrap: wrap;
}

.pagination .page-item .page-link {
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
  border: 1px solid rgba(255, 215, 0, 0.3);
  color: #1f2937;
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 500;
  transition: all 0.3s ease;
  min-width: 44px;
  text-align: center;
}

.pagination .page-item .page-link:hover {
  background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
  border-color: #FFD700;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 215, 0, 0.4);
  color: #1f2937;
}

.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
  border-color: #FFD700;
  color: #1f2937;
  font-weight: 700;
  box-shadow: 0 6px 16px rgba(255, 215, 0, 0.4);
}

.pagination .page-item.disabled .page-link {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.1);
  color: rgba(31, 41, 55, 0.35);
  cursor: not-allowed;
  pointer-events: none;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
  border-radius: 12px;
  min-width: 50px;
}

.page-header { text-align: center; margin-bottom: 30px; }

.filter-tabs {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.filter-tab {
    padding: 12px 20px; border-radius: 12px;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
    border: 2px solid rgba(255, 215, 0, 0.4);
    color: #1f2937; text-decoration: none; font-weight: 500; transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.filter-tab:hover {
    background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 215, 0, 0.4);
}

.view-toggle { display: flex; gap: 10px; }
.view-btn {
    padding: 12px 20px; border-radius: 12px;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
    border: 2px solid rgba(255, 215, 0, 0.4);
    color: #1f2937; font-weight: 600; cursor: pointer; transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.view-btn:hover {
    background: linear-gradient(135deg, rgba(255,215,0,0.3) 0%, rgba(255,215,0,0.15) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 215, 0, 0.4);
}
.view-btn.active {
    background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
    border-color: #FFD700;
    box-shadow: 0 6px 16px rgba(255, 215, 0, 0.5);
}

.announcement-card {
    padding: 20px; margin-bottom: 16px; transition: all 0.3s;
}
.announcement-card:hover { transform: translateY(-3px); background: rgba(255,255,255,0.12); }
.announcement-badge {
    display: inline-block; padding: 3px 10px; border-radius: 12px;
    font-size: 0.7rem; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;
}
.badge-important { background: rgba(239,68,68,0.2); color: #fca5a5; }
.badge-event { background: rgba(59,130,246,0.2); color: #93c5fd; }
.badge-news { background: rgba(168,85,247,0.2); color: #d8b4fe; }
.badge-general { background: rgba(40,167,69,0.2); color: #86efac; }

.event-card {
    padding: 16px; margin-bottom: 12px; display: flex; gap: 14px;
    align-items: flex-start; transition: all 0.3s;
}
.event-card:hover { transform: translateY(-3px); background: rgba(255,255,255,0.12); }
.event-date-box {
    min-width: 50px; text-align: center; padding: 8px;
    border-radius: 10px; background: rgba(255,255,255,0.1);
}
.event-month { font-size: 0.65rem; text-transform: uppercase; color: #FFD700; }
.event-day { font-size: 1.3rem; font-weight: 700; }

.event-status {
    display: inline-block; padding: 3px 8px; border-radius: 10px;
    font-size: 0.65rem; font-weight: 600;
}
.status-upcoming { background: rgba(59,130,246,0.2); color: #93c5fd; }
.status-ongoing { background: rgba(34,197,94,0.2); color: #4ade80; }
.status-past { background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); }

.empty-state { text-align: center; padding: 50px 20px; }
.empty-state i { font-size: 3.5rem; opacity: 0.3; margin-bottom: 16px; }

#calendar { padding: 20px; border-radius: 16px; }
.fc { background: transparent; color: #1f2937; }
.fc-toolbar-title { font-size: 1.2rem !important; color: #1f2937 !important; }
.fc-button { background: #FFD700 !important; border: none !important; color: #1f2937 !important; }
.fc-button:hover { background: #FFC107 !important; }
.fc-day-today { background: rgba(255,215,0,0.15) !important; }
.fc-event { border: none !important; cursor: pointer; }
.fc-event-announcement { background: #1055C9 !important; }
.fc-event-event { background: #059669 !important; }
.fc-daygrid-event { padding: 2px 4px; font-size: 0.8rem; }

.modal-content { background: #fff; border-radius: 16px; }
.modal-header { border-bottom: 1px solid rgba(255,215,0,0.2); }
.modal-title { color: #1f2937; font-weight: 600; }
.modal-body { color: #1f2937; }

/* Pagination */
.pagination { display: flex; justify-content: center; align-items: center; gap: 10px; margin: 30px 0; flex-wrap: wrap; }
.pagination .page-link,
.pagination a,
.pagination span {
  background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%) !important;
  border: 2px solid rgba(255, 215, 0, 0.5) !important;
  color: #1f2937 !important;
  padding: 14px 20px !important;
  border-radius: 14px !important;
  font-weight: 600 !important;
  font-size: 15px !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  min-width: 55px;
  text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  display: inline-block;
}
.pagination .page-link:hover,
.pagination a:hover {
  background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%) !important;
  border-color: #FFD700 !important;
  transform: translateY(-4px) scale(1.08) !important;
  box-shadow: 0 12px 28px rgba(255, 215, 0, 0.55) !important;
  color: #1f2937 !important;
  text-decoration: none;
}
.pagination .page-item.active .page-link,
.pagination .page-item.active a,
.pagination .page-item.active span {
  background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%) !important;
  border-color: #FFD700 !important;
  color: #1f2937 !important;
  font-weight: 700 !important;
  box-shadow: 0 8px 24px rgba(255, 215, 0, 0.5) !important;
  transform: translateY(-2px);
}
.pagination .page-item.disabled .page-link,
.pagination .page-item.disabled a,
.pagination .page-item.disabled span,
.pagination .disabled span,
.pagination .disabled a {
  background: rgba(255, 255, 255, 0.1) !important;
  border-color: rgba(255, 255, 255, 0.2) !important;
  color: rgba(31, 41, 55, 0.3) !important;
  cursor: not-allowed !important;
  pointer-events: none !important;
  box-shadow: none !important;
}
.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
  border-radius: 14px !important;
  min-width: 70px;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="page-header">
        <h1 class="page-title">News & Events</h1>
        <p class="opacity-75">Stay updated with the latest announcements and events from Barangay Bucandala 1</p>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="filter-tabs">
            <a href="{{ route('public.home') }}" class="filter-tab"><i class="bi bi-house me-1"></i>Home</a>
            <a href="{{ route('public.services') }}" class="filter-tab"><i class="bi bi-gear me-1"></i>Services</a>
            <a href="{{ route('public.contact') }}" class="filter-tab"><i class="bi bi-envelope me-1"></i>Contact</a>
        </div>
        <div class="view-toggle">
            <button class="view-btn active" onclick="switchView('list')"><i class="bi bi-list-ul"></i> List</button>
            <button class="view-btn" onclick="switchView('calendar')"><i class="bi bi-calendar3"></i> Calendar</button>
        </div>
    </div>

    <div id="listView">
        <div class="row">
            <div class="col-lg-8">
                @if($announcements->count() > 0)
                <div class="glass p-4 mb-4">
                    <h5 class="mb-3"><i class="bi bi-megaphone me-2"></i>Announcements</h5>
                    @foreach($announcements as $announcement)
                        <div class="announcement-card glass">
                            <span class="announcement-badge 
                                @if($announcement->priority === 'high') badge-important
                                @elseif($announcement->type === 'event') badge-event
                                @elseif($announcement->type === 'news') badge-news
                                @else badge-general @endif">
                                {{ $announcement->type ?? 'announcement' }}
                            </span>
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-2">{{ $announcement->title }}</h6>
                                <small class="opacity-75">{{ $announcement->created_at->format('M d, Y') }}</small>
                            </div>
                            <p class="mb-2 small opacity-75">{{ Str::limit($announcement->content, 120) }}</p>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-title="{{ $announcement->title }}" data-content="{{ $announcement->content }}" data-date="{{ $announcement->created_at->format('M d, Y') }}">
                                <i class="bi bi-eye me-1"></i>View Details
                            </button>
                        </div>
                    @endforeach
                </div>
                @if($announcements->hasPages())
                <div class="mt-4" style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap;">
                    @php
                        $currentPage = $announcements->currentPage();
                        $lastPage = $announcements->lastPage();
                        $nextPageUrl = $announcements->nextPageUrl();
                        $prevPageUrl = $announcements->previousPageUrl();
                    @endphp
                    
                    {{-- Previous Button --}}
                    @if($prevPageUrl)
                        <a href="{{ $prevPageUrl }}" style="
                            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%);
                            border: 2px solid rgba(255, 215, 0, 0.5);
                            color: #1f2937;
                            padding: 12px 18px;
                            border-radius: 12px;
                            font-weight: 600;
                            text-decoration: none;
                            display: inline-block;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                            transition: all 0.3s;
                        " onmouseover="this.style.background='linear-gradient(135deg, #FFD700 0%, #FFC107 100%)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(255, 215, 0, 0.5)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';">« Previous</a>
                    @else
                        <span style="
                            background: rgba(255, 255, 255, 0.1);
                            border: 2px solid rgba(255, 255, 255, 0.2);
                            color: rgba(31, 41, 55, 0.35);
                            padding: 12px 18px;
                            border-radius: 12px;
                            font-weight: 600;
                            display: inline-block;
                            cursor: not-allowed;
                        ">« Previous</span>
                    @endif

                    {{-- Page Numbers --}}
                    @for($i = 1; $i <= $lastPage; $i++)
                        @if($i == $currentPage)
                            <span style="
                                background: linear-gradient(135deg, #FFD700 0%, #FFC107 100%);
                                border: 2px solid #FFD700;
                                color: #1f2937;
                                padding: 12px 18px;
                                border-radius: 12px;
                                font-weight: 700;
                                display: inline-block;
                                box-shadow: 0 6px 16px rgba(255, 215, 0, 0.5);
                            ">{{ $i }}</span>
                        @else
                            <a href="{{ $announcements->url($i) }}" style="
                                background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%);
                                border: 2px solid rgba(255, 215, 0, 0.5);
                                color: #1f2937;
                                padding: 12px 18px;
                                border-radius: 12px;
                                font-weight: 600;
                                text-decoration: none;
                                display: inline-block;
                                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                transition: all 0.3s;
                            " onmouseover="this.style.background='linear-gradient(135deg, #FFD700 0%, #FFC107 100%)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(255, 215, 0, 0.5)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';">{{ $i }}</a>
                        @endif
                    @endfor

                    {{-- Next Button --}}
                    @if($nextPageUrl)
                        <a href="{{ $nextPageUrl }}" style="
                            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%);
                            border: 2px solid rgba(255, 215, 0, 0.5);
                            color: #1f2937;
                            padding: 12px 18px;
                            border-radius: 12px;
                            font-weight: 600;
                            text-decoration: none;
                            display: inline-block;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                            transition: all 0.3s;
                        " onmouseover="this.style.background='linear-gradient(135deg, #FFD700 0%, #FFC107 100%)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(255, 215, 0, 0.5)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.15) 100%)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.1)';">Next »</a>
                    @else
                        <span style="
                            background: rgba(255, 255, 255, 0.1);
                            border: 2px solid rgba(255, 255, 255, 0.2);
                            color: rgba(31, 41, 55, 0.35);
                            padding: 12px 18px;
                            border-radius: 12px;
                            font-weight: 600;
                            display: inline-block;
                            cursor: not-allowed;
                        ">Next »</span>
                    @endif
                </div>
                @endif
                @endif

                @if($events->count() > 0)
                <div class="glass p-4">
                    <h5 class="mb-3"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h5>
                    @foreach($events as $event)
                        <?php
                            $isPast = $event->end_datetime && $event->end_datetime->isPast();
                        $isOngoing = $event->start_datetime->isPast() && $event->end_datetime && $event->end_datetime->isFuture();
                        ?>
                        <div class="event-card glass">
                            <div class="event-date-box">
                                <div class="event-month">{{ $event->start_datetime->format('M') }}</div>
                                <div class="event-day">{{ $event->start_datetime->format('d') }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0">{{ $event->title }}</h6>
                                    @if($isOngoing)
                                        <span class="event-status status-ongoing">Ongoing</span>
                                    @elseif($isPast)
                                        <span class="event-status status-past">Past</span>
                                    @else
                                        <span class="event-status status-upcoming">Upcoming</span>
                                    @endif
                                </div>
                                <p class="mb-1 small opacity-75">{{ Str::limit($event->description, 80) }}</p>
                                <small class="opacity-75"><i class="bi bi-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}@if($event->end_datetime) - {{ $event->end_datetime->format('g:i A') }}@endif</small>
                                @if($event->location)<small class="opacity-75 ms-2"><i class="bi bi-geo-alt ms-2"></i>{{ $event->location }}</small>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                @if($announcements->count() === 0 && $events->count() === 0)
                <div class="empty-state">
                    <i class="bi bi-newspaper"></i>
                    <h4>No News or Events</h4>
                    <p class="opacity-75">Check back later for updates.</p>
                </div>
                @endif
            </div>

<div class="col-lg-4">
                {{-- Events on the right sidebar --}}
                @if($events->count() > 0)
                <div class="glass p-4 mb-4">
                    <h6 class="mb-3"><i class="bi bi-calendar-event me-2"></i>Upcoming Events</h6>
                    @foreach($events as $event)
                        <?php
                            $isPast = $event->end_datetime && $event->end_datetime->isPast();
                        $isOngoing = $event->start_datetime->isPast() && $event->end_datetime && $event->end_datetime->isFuture();
                        ?>
                        <div class="event-card glass">
                            <div class="event-date-box">
                                <div class="event-month">{{ $event->start_datetime->format('M') }}</div>
                                <div class="event-day">{{ $event->start_datetime->format('d') }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $event->title }}</h6>
                                <small class="opacity-75"><i class="bi bi-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}</small>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Events Pagination --}}
                    @if($events->hasPages())
                    <div class="mt-3" style="display: flex; justify-content: center; gap: 6px; flex-wrap: wrap;">
                        @php
                            $eventCurrent = $events->currentPage();
                            $eventLast = $events->lastPage();
                            $eventPrev = $events->previousPageUrl();
                            $eventNext = $events->nextPageUrl();
                        @endphp
                        
                        @if($eventPrev)
                            <a href="{{ $eventPrev }}" style="padding: 8px 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.3); border-radius: 8px; color: #1f2937; text-decoration: none; font-size: 13px;">« Prev</a>
                        @endif
                        
                        @for($i = 1; $i <= $eventLast; $i++)
                            @if($i == $eventCurrent)
                                <span style="padding: 8px 12px; background: #FFD700; border: 1px solid #FFD700; border-radius: 8px; color: #1f2937; font-weight: 600; font-size: 13px;">{{ $i }}</span>
                            @else
                                <a href="{{ $events->url($i) }}" style="padding: 8px 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.3); border-radius: 8px; color: #1f2937; text-decoration: none; font-size: 13px;">{{ $i }}</a>
                            @endif
                        @endfor
                        
                        @if($eventNext)
                            <a href="{{ $eventNext }}" style="padding: 8px 12px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,215,0,0.3); border-radius: 8px; color: #1f2937; text-decoration: none; font-size: 13px;">Next »</a>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                {{-- Calendar with events --}}
                <div class="glass p-4 mb-4">
                    <h6 class="mb-3"><i class="bi bi-calendar3 me-2"></i>Calendar</h6>
                    <div id="calendarContainer">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" onclick="changeCalendarMonth(-1)" style="background:none;border:none;cursor:pointer;font-size:20px;color:#1f2937;padding:4px 12px;border-radius:8px;" onmouseover="this.style.background='rgba(255,215,0,0.2)'" onmouseout="this.style.background='none'">‹</button>
                            <span id="calendarMonthYear" style="font-weight:700;font-size:16px;color:#1f2937;"></span>
                            <button type="button" onclick="changeCalendarMonth(1)" style="background:none;border:none;cursor:pointer;font-size:20px;color:#1f2937;padding:4px 12px;border-radius:8px;" onmouseover="this.style.background='rgba(255,215,0,0.2)'" onmouseout="this.style.background='none'">›</button>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;">
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                <div style="font-size:11px;font-weight:600;opacity:0.7;color:#1f2937;padding:4px;">{{ $day }}</div>
                            @endforeach
                            <div id="calendarDays" style="grid-column: 1 / span 7; display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;"></div>
                        </div>
                    </div>
                </div>

                <script>
                    let calendarDate = new Date();
                    const calendarEvents = @json($calendarEvents);
                    
                    function renderCalendar() {
                        const year = calendarDate.getFullYear();
                        const month = calendarDate.getMonth();
                        const today = new Date();
                        
                        document.getElementById('calendarMonthYear').textContent = 
                            new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                        
                        const firstDay = new Date(year, month, 1).getDay();
                        const daysInMonth = new Date(year, month + 1, 0).getDate();
                        
                        let html = '';
                        for (let i = 0; i < firstDay; i++) {
                            html += '<div style="height:40px;"></div>';
                        }
                        
                        for (let day = 1; day <= daysInMonth; day++) {
                            const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                            const eventsOnDay = calendarEvents.filter(e => e.start === dateStr);
                            const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
                            const hasEvents = eventsOnDay.length > 0;
                            
                            let bgColor = 'transparent';
                            let fontWeight = '400';
                            let cursor = 'default';
                            let hoverEffect = '';
                            let clickHandler = '';
                            
                            if (isToday) {
                                bgColor = '#FFD700';
                                fontWeight = '700';
                            } else if (hasEvents) {
                                bgColor = 'rgba(16, 85, 201, 0.25)';
                                cursor = 'pointer';
                                hoverEffect = 'onmouseover="this.style.background=\'rgba(16, 85, 201, 0.4)\';" onmouseout="this.style.background=\'rgba(16, 85, 201, 0.25)\';"';
                                clickHandler = 'onclick="showDateEvents(\'' + dateStr + '\')"';
                            }
                            
                            html += '<div style="height:40px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:14px;background:' + bgColor + ';font-weight:' + fontWeight + ';color:#1f2937;cursor:' + cursor + ';' + hoverEffect + '" ' + clickHandler + '>' + day + '</div>';
                        }
                        
                        document.getElementById('calendarDays').innerHTML = html;
                    }
                    
                    function changeCalendarMonth(delta) {
                        calendarDate.setMonth(calendarDate.getMonth() + delta);
                        renderCalendar();
                    }
                    
                    function showDateEvents(dateStr) {
                        const eventsOnDay = calendarEvents.filter(e => e.start === dateStr);
                        if (eventsOnDay.length === 0) return;
                        
                        const modalContent = document.getElementById('dateEventsContent');
                        const modalTitle = document.getElementById('dateEventsModalTitle');
                        
                        const formattedDate = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        modalTitle.textContent = formattedDate;
                        
                        let html = '';
                        eventsOnDay.forEach(event => {
                            const typeLabel = event.type === 'event' ? 'Event' : 'Announcement';
                            const typeColor = event.type === 'event' ? '#059669' : '#1055C9';
                            html += '<div style="padding:12px;margin-bottom:10px;background:rgba(255,255,255,0.1);border-radius:8px;border-left:4px solid ' + typeColor + ';">';
                            html += '<span style="font-size:11px;font-weight:600;padding:2px 8px;background:' + typeColor + ';color:#fff;border-radius:4px;">' + typeLabel + '</span>';
                            html += '<h6 style="margin:8px 0 4px;font-weight:600;color:#1f2937;">' + event.title + '</h6>';
                            html += '</div>';
                        });
                        
                        modalContent.innerHTML = html;
                        new bootstrap.Modal(document.getElementById('dateEventsModal')).show();
                    }
                    
                    renderCalendar();
                </script>

                <div class="glass p-4">
                    <h6 class="mb-3"><i class="bi bi-clock me-2"></i>Office Hours</h6>
                    <div class="d-flex justify-content-between py-1"><span>Mon-Fri</span><span class="fw-medium">8AM-5PM</span></div>
                    <div class="d-flex justify-content-between py-1"><span>Saturday</span><span class="fw-medium">8AM-12PM</span></div>
                    <div class="d-flex justify-content-between py-1"><span>Sunday</span><span class="opacity-75">Closed</span></div>
                </div>
            </div>
        </div>
    </div>

    <div id="calendarView" style="display: none;">
        <div class="glass p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl" style="width: 90%; max-width: 900px;">
        <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title" id="detailTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="overflow-y: auto; flex: 1;">
                <p id="detailContent" class="mb-3"></p>
                <small class="opacity-75" id="detailDate"></small>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dateEventsModal" tabindex="-1">
    <div class="modal-dialog modal-lg" style="width: 90%; max-width: 700px;">
        <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column;">
            <div class="modal-header" style="flex-shrink: 0;">
                <h5 class="modal-title" id="dateEventsModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="dateEventsContent" style="overflow-y: auto; flex: 1;">
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/main.min.js"></script>
<script>
document.getElementById('detailModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    document.getElementById('detailTitle').textContent = button.getAttribute('data-title');
    document.getElementById('detailContent').textContent = button.getAttribute('data-content');
    document.getElementById('detailDate').textContent = 'Posted on: ' + button.getAttribute('data-date');
});

let calendar;

function switchView(view) {
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (view === 'list') {
        document.getElementById('listView').style.display = 'block';
        document.getElementById('calendarView').style.display = 'none';
    } else {
        document.getElementById('listView').style.display = 'none';
        document.getElementById('calendarView').style.display = 'block';
        initCalendar();
    }
}

function initCalendar() {
    if (calendar) return;
    
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        header: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listWeek' },
        events: @json($calendarEvents),
        eventClick: function(info) {
            const type = info.event.extendedProps.type;
            const title = info.event.title;
            if (type === 'announcement') {
                showDetail('announcement', info.event.id, title, 'Click view details for full content.', info.event.startStr);
            } else {
                showDetail('event', info.event.id, title, 'Click view details for full content.', info.event.startStr);
            }
        },
        eventRender: function(info) {
            info.el.classList.add('fc-event-' + info.event.extendedProps.type);
        }
    });
    calendar.render();
}
</script>
@endpush