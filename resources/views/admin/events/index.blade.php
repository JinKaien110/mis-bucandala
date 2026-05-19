@extends('layouts.admin')

@section('title', 'Events & Calendar - Barangay MIS')

@section('content')
<div class="page-surface">
  <!-- Alert Message -->
  <div id="message" class="alert" style="display:none;"></div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1">Events & Calendar</h4>
      <p class="text-muted mb-0">Manage barangay events and reminders</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
      <i class="bi bi-plus-lg"></i> Add Event
    </button>
  </div>

  <!-- Month Navigation -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.events.index', ['month' => $month - 1, 'year' => $year]) }}" class="btn btn-outline-secondary">
      <i class="bi bi-chevron-left"></i>
    </a>
    <h5 class="mb-0">{{ \Carbon\Carbon::createFromDate($year, $month)->format('F Y') }}</h5>
    <a href="{{ route('admin.events.index', ['month' => $month + 1, 'year' => $year]) }}" class="btn btn-outline-secondary">
      <i class="bi bi-chevron-right"></i>
    </a>
  </div>

  <div class="row">
    <!-- Calendar Grid -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <table class="table table-bordered calendar-table mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center">Sun</th>
                  <th class="text-center">Mon</th>
                  <th class="text-center">Tue</th>
                  <th class="text-center">Wed</th>
                  <th class="text-center">Thu</th>
                  <th class="text-center">Fri</th>
                  <th class="text-center">Sat</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
                  $daysInMonth = $firstDay->daysInMonth;
                  $startDayOfWeek = $firstDay->dayOfWeek;
                  $day = 1;
                @endphp
                
                @for ($week = 0; $week < 6; $week++)
                  @if ($day > $daysInMonth) @break @endif
                  <tr>
                    @for ($dow = 0; $dow < 7; $dow++)
                      @if ($week === 0 && $dow < $startDayOfWeek)
                        <td class="bg-light"></td>
                      @elseif ($day > $daysInMonth)
                        <td class="bg-light"></td>
                      @else
                        <td class="calendar-day">
                          <div class="day-number {{ \Carbon\Carbon::createFromDate($year, $month, $day)->isToday() ? 'today' : '' }}">
                            {{ $day }}
                          </div>
                          @php
                            $dayEvents = $events->filter(function($e) use ($year, $month, $day) {
                              return $e->start_datetime->year == $year && 
                                     $e->start_datetime->month == $month && 
                                     $e->start_datetime->day == $day;
                            });
                            $dayAnnouncements = $announcements->filter(function($a) use ($year, $month, $day) {
                              $publishDate = \Carbon\Carbon::parse($a->publish_date);
                              $expireDate = $a->expire_date ? \Carbon\Carbon::parse($a->expire_date) : null;
                              $currentDate = \Carbon\Carbon::createFromDate($year, $month, $day);
                              
                              // Check if current day falls within the announcement's active period
                              if ($expireDate) {
                                return $currentDate->between($publishDate->startOfDay(), $expireDate->endOfDay());
                              }
                              // For announcements without expiration, show on publish date
                              return $publishDate->year == $year && 
                                     $publishDate->month == $month && 
                                     $publishDate->day == $day;
                            });
                            $hasItems = $dayEvents->count() > 0 || $dayAnnouncements->count() > 0;
                          @endphp
                          @if($hasItems)
                            <div class="d-flex flex-wrap gap-1">
                              @if($dayEvents->count() > 0)
                                <span class="event-icon event-{{ $dayEvents->first()->type }}" 
                                      data-bs-toggle="modal" 
                                      data-bs-target="#dayEventsModal{{ $year }}{{ $month }}{{ $day }}" 
                                      title="{{ $dayEvents->first()->title }}" style="cursor: pointer;">
                                  <i class="bi bi-calendar-event"></i>
                                </span>
                              @endif
                              @if($dayAnnouncements->count() > 0)
                                <span class="event-icon announcement" 
                                      data-bs-toggle="modal" 
                                      data-bs-target="#dayAnnouncementsModal{{ $year }}{{ $month }}{{ $day }}" 
                                      title="{{ $dayAnnouncements->first()->title }}" style="cursor: pointer;">
                                  <i class="bi bi-megaphone"></i>
                                </span>
                              @endif
                            </div>
                          @endif
                        </td>
                        @php $day++ @endphp
                      @endif
                    @endfor
                  </tr>
                @endfor
              </tbody>
            </table>
        </div>
      </div>
    </div>

    <!-- Upcoming Events Sidebar -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
          <h5 class="mb-0">Upcoming Events</h5>
        </div>
        <div class="card-body p-0">
          @if($upcomingEvents->count() > 0)
            @foreach($upcomingEvents as $event)
              <div class="border-bottom p-3">
                <div class="d-flex justify-content-between">
                  <strong>{{ $event->title }}</strong>
                  <span class="badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'program' ? 'success' : 'secondary') }}">
                    {{ $event->type }}
                  </span>
                </div>
                <small class="text-muted">
                  <i class="bi bi-calendar"></i> {{ $event->start_datetime->format('M d, Y') }}
                  @if(!$event->is_all_day)
                    <i class="bi bi-clock ms-2"></i> {{ $event->start_datetime->format('h:i A') }}
                  @endif
                </small>
                @if($event->location)
                  <br><small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $event->location }}</small>
                @endif
              </div>
            @endforeach
          @else
            <div class="text-center py-4 text-muted">
              <i class="bi bi-calendar-event fs-1 d-block mb-2"></i>
              No upcoming events
            </div>
          @endif
        </div>
      </div>

      <!-- Upcoming Announcements -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-light">
          <h5 class="mb-0"><i class="bi bi-megaphone"></i> Upcoming Announcements</h5>
        </div>
        <div class="card-body p-0">
          @if(isset($upcomingAnnouncements) && $upcomingAnnouncements->count() > 0)
            @foreach($upcomingAnnouncements as $announcement)
              <div class="border-bottom p-3">
                <div class="d-flex justify-content-between">
                  <strong>{{ $announcement->title }}</strong>
                  <span class="badge bg-danger">
                    {{ $announcement->type }}
                  </span>
                </div>
                <small class="text-muted">
                  <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($announcement->publish_date)->format('M d, Y') }}
                </small>
                <div class="mt-2">
                  <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> View Details
                  </a>
                </div>
              </div>
            @endforeach
          @else
            <div class="text-center py-4 text-muted">
              <i class="bi bi-megaphone fs-1 d-block mb-2"></i>
              No upcoming announcements
            </div>
          @endif
        </div>
      </div>

      <!-- Event Types Legend -->
      <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Event Types</h5>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-primary">Meeting</span>
            <span class="badge bg-success">Program</span>
            <span class="badge bg-secondary">General</span>
            <span class="badge bg-warning text-dark">Reminder</span>
            <span class="badge bg-danger">Announcement</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Day Modals Container - Moved outside page-surface for proper z-index -->
@for ($week = 0; $week < 6; $week++)
  @php
    $weekStartDay = 1;
    if ($week === 0) {
      $weekStartDay = 1 - $startDayOfWeek;
    } else {
      $weekStartDay = ($week * 7) + 1 - $startDayOfWeek;
    }
  @endphp
  @for ($d = 0; $d < 7; $d++)
    @php
      $checkDay = $weekStartDay + $d;
      if ($checkDay < 1 || $checkDay > $daysInMonth) continue;
      
      $dayEventsLoop = $events->filter(function($e) use ($year, $month, $checkDay) {
        return $e->start_datetime->year == $year && 
               $e->start_datetime->month == $month && 
               $e->start_datetime->day == $checkDay;
      });
      $dayAnnouncementsLoop = $announcements->filter(function($a) use ($year, $month, $checkDay) {
        $publishDate = \Carbon\Carbon::parse($a->publish_date);
        $expireDate = $a->expire_date ? \Carbon\Carbon::parse($a->expire_date) : null;
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, $checkDay);
        if ($expireDate) {
          return $currentDate->between($publishDate->startOfDay(), $expireDate->endOfDay());
        }
        return $publishDate->year == $year && 
               $publishDate->month == $month && 
               $publishDate->day == $checkDay;
      });
    @endphp
    @if($dayEventsLoop->count() > 0)
    <!-- Events Modal for Day {{ $checkDay }} -->
    <div class="modal fade" id="dayEventsModal{{ $year }}{{ $month }}{{ $checkDay }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered modal-lg modal-event-custom">
        <div class="modal-content border-0 shadow">
          <div class="modal-header event-modal-header">
            <div class="d-flex align-items-center">
              <i class="bi bi-calendar-event me-2 fs-5"></i>
              <h5 class="modal-title">Events for {{ \Carbon\Carbon::createFromDate($year, $month, $checkDay)->format('F d, Y') }}</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            @foreach($dayEventsLoop as $event)
              <div class="event-card mb-3 p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <span class="badge event-type-badge bg-{{ $event->type === 'meeting' ? 'primary' : ($event->type === 'program' ? 'success' : 'secondary') }}">
                    <i class="bi bi-{{ $event->type === 'meeting' ? 'people' : ($event->type === 'program' ? 'gift' : 'info-circle') }} me-1"></i>
                    {{ ucfirst($event->type) }}
                  </span>
                </div>
                <h6 class="event-title mb-2">{{ $event->title }}</h6>
                
                <!-- Time Display -->
                <div class="event-datetime mb-2">
                  <div class="d-flex align-items-center text-primary fw-bold">
                    <i class="bi bi-clock-fill me-2 fs-5"></i>
                    <span class="fs-6">
                      @if($event->is_all_day === true)
                        All Day Event
                      @else
                        {{ \Carbon\Carbon::parse($event->start_datetime)->format('h:i A') }}
                        @if($event->end_datetime)
                          - {{ \Carbon\Carbon::parse($event->end_datetime)->format('h:i A') }}
                        @endif
                      @endif
                    </span>
                  </div>
                  <div class="text-muted small ms-4">
                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('l, F d, Y') }}
                  </div>
                </div>
                
                @if($event->location)
                  <p class="mb-2 text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $event->location }}</p>
                @endif
                @if($event->description)
                  <p class="mb-0 text-muted">{{ $event->description }}</p>
                @endif
              </div>
            @endforeach
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-1"></i> Close
            </button>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if($dayAnnouncementsLoop->count() > 0)
    <!-- Announcements Modal for Day {{ $checkDay }} -->
    <div class="modal fade" id="dayAnnouncementsModal{{ $year }}{{ $month }}{{ $checkDay }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered modal-lg modal-announcement-custom">
        <div class="modal-content border-0 shadow">
          <div class="modal-header announcement-modal-header">
            <div class="d-flex align-items-center">
              <i class="bi bi-megaphone me-2 fs-5"></i>
              <h5 class="modal-title">Announcements for {{ \Carbon\Carbon::createFromDate($year, $month, $checkDay)->format('F d, Y') }}</h5>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            @foreach($dayAnnouncementsLoop as $announcement)
              <div class="announcement-card mb-3 p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <span class="badge announcement-type-badge">
                    <i class="bi bi-megaphone-fill me-1"></i>
                    {{ ucfirst($announcement->type ?? 'Announcement') }}
                  </span>
                  <span class="announcement-date-badge">
                    <i class="bi bi-calendar-check me-1"></i>
                    {{ \Carbon\Carbon::parse($announcement->publish_date)->format('M d, Y') }}
                  </span>
                </div>
                <h6 class="announcement-title mb-2">{{ $announcement->title }}</h6>
                @if($announcement->expire_date)
                  <p class="mb-2 text-muted small"><i class="bi bi-calendar-x me-1"></i>Expires: {{ \Carbon\Carbon::parse($announcement->expire_date)->format('F d, Y') }}</p>
                @endif
                @if($announcement->content)
                  <p class="mb-0 text-muted">{{ $announcement->content }}</p>
                @endif
              </div>
            @endforeach
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-1"></i> Close
            </button>
          </div>
        </div>
      </div>
    </div>
    @endif
  @endfor
@endfor

<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header create-event-modal-header">
        <div class="d-flex align-items-center">
          <div class="modal-icon-bg me-3">
            <i class="bi bi-calendar-plus-fill fs-4"></i>
          </div>
          <div>
            <h5 class="modal-title mb-0">Create New Event</h5>
            <small class="opacity-75">Fill in the details below</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createEventForm" method="POST" action="{{ route('admin.events.store') }}">
        @csrf
        <div class="modal-body p-4">
          <!-- Event Title -->
          <div class="form-section mb-4">
            <div class="form-section-icon">
              <i class="bi bi-type"></i>
            </div>
            <label class="form-label fw-semibold">Event Title</label>
            <input type="text" name="title" class="form-control form-control-lg border-0 shadow-sm" 
                   placeholder="e.g., Monthly Flag Ceremony" required>
          </div>

          <!-- Description -->
          <div class="form-section mb-4">
            <div class="form-section-icon">
              <i class="bi bi-text-paragraph"></i>
            </div>
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control border-0 shadow-sm" rows="2" 
                      placeholder="Event details..."></textarea>
          </div>

          <!-- Event Type & Location -->
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-section">
                <div class="form-section-icon">
                  <i class="bi bi-tag"></i>
                </div>
                <label class="form-label fw-semibold">Event Type</label>
                <select name="type" class="form-select border-0 shadow-sm" required>
                  <option value="general">📌 General</option>
                  <option value="meeting">👥 Meeting</option>
                  <option value="program">🎉 Program</option>
                  <option value="reminder">⏰ Reminder</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-section">
                <div class="form-section-icon">
                  <i class="bi bi-geo-alt"></i>
                </div>
                <label class="form-label fw-semibold">Location</label>
                <input type="text" name="location" class="form-control border-0 shadow-sm" 
                       placeholder="e.g., Barangay Hall">
              </div>
            </div>
          </div>

          <!-- Date & Time -->
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-section">
                <div class="form-section-icon">
                  <i class="bi bi-play-circle"></i>
                </div>
                <label class="form-label fw-semibold">Start Date & Time</label>
                <input type="datetime-local" name="start_datetime" class="form-control border-0 shadow-sm" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-section">
                <div class="form-section-icon">
                  <i class="bi bi-stop-circle"></i>
                </div>
                <label class="form-label fw-semibold">End Date & Time</label>
                <input type="datetime-local" name="end_datetime" class="form-control border-0 shadow-sm">
              </div>
            </div>
          </div>

          <!-- Checkboxes -->
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-check custom-checkbox-card selectable-card" data-checkbox-id="modal_is_all_day">
                <input class="form-check-input" type="checkbox" name="is_all_day" id="modal_is_all_day" value="1">
                <label class="form-check-label" for="modal_is_all_day"></label>
                <span class="card-icon"><i class="bi bi-calendar-range"></i></span>
                <span class="checkbox-text">
                  <strong>All Day Event</strong>
                  <small class="d-block text-muted">Event runs for the entire day</small>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check custom-checkbox-card selectable-card" data-checkbox-id="modal_is_published">
                <input class="form-check-input" type="checkbox" name="is_published" id="modal_is_published" value="1" checked>
                <label class="form-check-label" for="modal_is_published"></label>
                <span class="card-icon"><i class="bi bi-eye"></i></span>
                <span class="checkbox-text">
                  <strong>Publish immediately</strong>
                  <small class="d-block text-muted">Make visible on the calendar</small>
                </span>
              </div>
            </div>
          </div>

          <!-- Reminder -->
          <div class="form-section mb-0">
            <div class="form-section-icon">
              <i class="bi bi-bell"></i>
            </div>
            <label class="form-label fw-semibold">Reminder</label>
            <select name="reminder" class="form-select border-0 shadow-sm">
              <option value="">🔕 No reminder</option>
              <option value="15min">⏱️ 15 minutes before</option>
              <option value="30min">⏱️ 30 minutes before</option>
              <option value="1hour">⏱️ 1 hour before</option>
              <option value="1day">📅 1 day before</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 p-4">
          <button type="button" class="btn btn-light border-0 shadow-sm px-4" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary px-4 shadow">
            <i class="bi bi-check-circle me-2"></i> Create Event
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Modal z-index fix */
.modal {
  z-index: 1056 !important;
}
.modal-backdrop {
  z-index: 1055 !important;
}

/* Create Event Modal Styles */
.create-event-modal-header {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
  color: white;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}
.modal-icon-bg {
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.create-event-modal-header .modal-title {
  font-weight: 600;
  font-size: 1.25rem;
}
.form-section {
  position: relative;
  padding-left: 40px;
}
.form-section-icon {
  position: absolute;
  left: 0;
  top: 32px;
  width: 28px;
  height: 28px;
  background: #e7f1ff;
  color: #0d6efd;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.85rem;
}
#createEventModal .form-control:focus, 
#createEventModal .form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}
#createEventModal .form-control,
#createEventModal .form-select {
  border-radius: 10px;
}
#createEventModal .btn-primary {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
  border: none;
  border-radius: 10px;
  padding: 0.6rem 1.5rem;
  transition: all 0.3s ease;
}
#createEventModal .btn-primary:hover {
  background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(13, 110, 253, 0.4);
}
#createEventModal .btn-light {
  background: #f8f9fa;
  border-radius: 10px;
  transition: all 0.3s ease;
}
#createEventModal .btn-light:hover {
  background: #e9ecef;
}

/* Custom Checkbox Styles */
.custom-checkbox-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 12px 16px;
  transition: all 0.2s ease;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}
.custom-checkbox-card:hover {
  background: #e9ecef;
}
.custom-checkbox-card .form-check-input {
  width: 1.3rem;
  height: 1.3rem;
  margin-top: 0;
  cursor: pointer;
}
.custom-checkbox-card .form-check-label {
  cursor: pointer;
  padding: 0;
  margin: 0;
  width: 20px;
  height: 20px;
  background: #fff;
  border: 2px solid #dee2e6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}
.custom-checkbox-card .form-check-label::after {
  content: '✓';
  color: white;
  font-size: 12px;
  display: none;
}
.custom-checkbox-card .form-check-input:checked + .form-check-label {
  background: #0d6efd;
  border-color: #0d6efd;
}
.custom-checkbox-card .form-check-input:checked + .form-check-label::after {
  display: block;
}
.custom-checkbox-card .checkbox-text {
  display: flex;
  flex-direction: column;
}


/* Enhanced Modal Styles */
.modal-event-custom .modal-content {
  border-radius: 12px;
  overflow: hidden;
}
.modal-announcement-custom .modal-content {
  border-radius: 12px;
  overflow: hidden;
}
.event-modal-header {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
  color: white;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}
.announcement-modal-header {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
  color: white;
  padding: 1.25rem 1.5rem;
  border-bottom: none;
}
.event-card, .announcement-card {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  transition: all 0.2s ease;
}
.event-card:hover {
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
  transform: translateY(-2px);
}
.announcement-card {
  background: #fff5f5 !important;
  border-left: 4px solid #dc3545;
}
.announcement-card:hover {
  box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
  transform: translateY(-2px);
}
.event-type-badge, .announcement-type-badge {
  font-size: 0.75rem;
  padding: 0.4em 0.8em;
  border-radius: 20px;
}
.announcement-type-badge {
  background: #dc3545 !important;
}
.event-time-badge, .announcement-date-badge {
  background: #e9ecef;
  padding: 0.35rem 0.85rem;
  border-radius: 20px;
  font-size: 0.75rem;
  color: #6c757d;
  white-space: nowrap;
}
.event-title, .announcement-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #212529;
}
.event-card {
  background: #f0f7ff !important;
  border-left: 4px solid #0d6efd;
}
.event-card:hover {
  background: #e6f0fa !important;
  border-left-color: #0a58ca;
}
.modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e9ecef;
}
.modal-footer .btn {
  padding: 0.5rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
}
.calendar-table {
  table-layout: fixed;
  width: 100%;
  border-collapse: separate;
  border-spacing: 2px;
}
.calendar-table td {
  height: 140px;
  min-height: 140px;
  vertical-align: top;
  padding: 8px !important;
  background: #fff;
  border: 1px solid #e9ecef !important;
  border-radius: 8px;
  transition: all 0.2s ease;
}
.calendar-table td:hover {
  background: #f8f9fa;
  box-shadow: inset 0 0 0 1px #dee2e6;
}
.calendar-table td.bg-light {
  background: #f1f3f5;
  border: 1px solid #e9ecef !important;
}
.calendar-table th {
  padding: 12px 4px !important;
  font-size: 0.85rem;
  font-weight: 600;
  background: #495057;
  color: white;
  border: none !important;
  border-radius: 4px;
}
.day-number {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: #212529;
}
.day-number.today {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
}
.event-item {
  display: block;
  color: white;
  padding: 6px 8px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.75rem;
  margin-bottom: 4px;
  text-decoration: none;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: all 0.2s ease;
  width: 100%;
  font-weight: 500;
  background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}
.event-item:hover {
  transform: translateX(3px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.event-item .event-title {
  display: inline-block;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
}
.event-item.announcement {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}
.event-item.event-meeting {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
}
.event-item.event-program {
  background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
}
.event-item.event-general {
  background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}
/* Event Icons - Round */
.event-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  color: white;
  font-size: 0.7rem;
  cursor: pointer;
  transition: transform 0.2s;
}
.event-icon:hover {
  transform: scale(1.2);
}
.event-icon.event-meeting {
  background: #0d6efd !important;
}
.event-icon.event-program {
  background: #198754 !important;
}
.event-icon.event-general {
  background: #6c757d !important;
}
.event-icon.announcement {
  background: #dc3545 !important;
}
.event-badge:hover {
  opacity: 0.9;
}
/* More events indicator */
.more-events {
  font-size: 0.65rem;
  color: #6c757d;
  padding: 2px 4px;
  cursor: pointer;
  font-weight: 600;
}
.more-events:hover {
  color: #0d6efd;
  text-decoration: underline;
}
/* Selectable Card Checkbox Style */
.selectable-card {
  cursor: pointer;
  transition: all 0.2s ease;
  border: 2px solid transparent;
  border-radius: 12px;
  padding: 16px;
  background: #f8f9fa;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}
.selectable-card:hover {
  background: #e9ecef;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.selectable-card .form-check-input,
.selectable-card .form-check-label {
  display: none;
}
.selectable-card .card-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  min-width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #dee2e6;
  font-size: 1.2rem;
  color: #6c757d;
  transition: all 0.2s ease;
}
.selectable-card .checkbox-text {
  flex: 1;
}
/* Selected/Checked State */
.selectable-card.active {
  background: #e7f1ff;
  border-color: #1055C9;
}
.selectable-card.active .card-icon {
  background: #1055C9;
  color: white;
}
.selectable-card.active:hover {
  background: #d0e3ff;
}
/* Responsive */
@media (max-width: 992px) {
  .calendar-table td {
    height: 120px;
    min-height: 120px;
  }
}
@media (max-width: 768px) {
  .calendar-table td {
    height: 100px;
    min-height: 100px;
    padding: 6px !important;
  }
  .day-number {
    font-size: 0.85rem;
  }
  .event-item {
    font-size: 0.65rem;
    padding: 4px 6px;
  }
}
@media (max-width: 576px) {
  .calendar-table td {
    height: 80px;
    min-height: 80px;
    padding: 4px !important;
  }
  .calendar-table th {
    padding: 8px 2px !important;
    font-size: 0.65rem;
  }
  .day-number {
    font-size: 0.7rem;
  }
  .event-item {
    font-size: 0.55rem;
    padding: 2px 4px;
    margin-bottom: 2px;
  }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize selectable cards based on initial checkbox state
  document.querySelectorAll('.selectable-card').forEach(function(card) {
    const checkboxId = card.getAttribute('data-checkbox-id');
    const checkbox = document.getElementById(checkboxId);
    if (checkbox && checkbox.checked) {
      card.classList.add('active');
    }
    
    // Add click event to the entire card
    card.addEventListener('click', function(e) {
      // Don't trigger if clicking on the label directly
      if (e.target.tagName === 'LABEL' || e.target.closest('label')) {
        return;
      }
      
      // Toggle the checkbox
      if (checkbox) {
        checkbox.checked = !checkbox.checked;
        // Toggle the active class
        card.classList.toggle('active', checkbox.checked);
      }
    });
  });
});

// Alert functions
const msgBox = document.getElementById('message');

function showMsg(obj) {
  msgBox.style.display = 'block';
  const isSuccess = obj?.success === true;
  const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
  const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
  msgBox.className = `alert ${alertClass} shadow-sm border-0`;
  msgBox.innerHTML = `<i class="bi ${icon} me-2"></i> ${obj?.message || JSON.stringify(obj)}`;
}

// Create Event Form
document.getElementById('createEventForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = this;
  const formData = new FormData(form);
  const modal = bootstrap.Modal.getInstance(document.getElementById('createEventModal'));
  
  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: formData
    });
    
    const data = await res.json();
    
    if (data.success || res.ok) {
      showMsg({ success: true, message: data.message || 'Event created successfully!' });
      modal.hide();
      form.reset();
      setTimeout(() => location.reload(), 1500);
    } else {
      showMsg({ success: false, message: data.message || 'Error creating event' });
    }
  } catch (err) {
    showMsg({ success: false, message: 'Error: ' + err.message });
  }
});
</script>
@endsection
