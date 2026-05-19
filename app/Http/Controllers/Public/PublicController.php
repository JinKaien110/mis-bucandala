<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;

class PublicController extends Controller
{
    public function announcements()
    {
        $announcements = Announcement::with('creator:id,email')
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('public.announcements', [
            'currentRoute' => 'public.news',
            'announcements' => $announcements,
            'pageTitle' => 'Announcements - Barangay Bucandala 1',
            'pageDescription' => 'Latest news and announcements from Barangay Bucandala 1',
        ]);
    }

    public function events()
    {
        $upcomingEvents = Event::where('is_published', true)
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->get();

        $pastEvents = Event::where('is_published', true)
            ->where('end_datetime', '<', now())
            ->orderBy('start_datetime', 'desc')
            ->get();

        return view('public.events', [
            'currentRoute' => 'public.news',
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'pageTitle' => 'Events Calendar - Barangay Bucandala 1',
            'pageDescription' => 'Upcoming events and activities in Barangay Bucandala 1',
        ]);
    }

    public function news()
    {
        $announcements = Announcement::with('creator:id,email')
            ->where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_announcements');

        $allAnnouncements = Announcement::where('is_published', true)
            ->whereNull('archived_at')
            ->get();

        $events = Event::where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('start_datetime', 'desc')
            ->paginate(5, ['*'], 'page_events');

        $allEvents = Event::where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('start_datetime', 'desc')
            ->get();

        $calendarEvents = [];
        foreach ($allAnnouncements as $announcement) {
            $calendarEvents[] = [
                'id' => 'announcement-'.$announcement->id,
                'title' => $announcement->title,
                'start' => $announcement->publish_date ? $announcement->publish_date->format('Y-m-d') : $announcement->created_at->format('Y-m-d'),
                'type' => 'announcement',
                'className' => 'bg-announcement',
            ];
        }
        foreach ($allEvents as $event) {
            $calendarEvents[] = [
                'id' => 'event-'.$event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->format('Y-m-d'),
                'end' => $event->end_datetime ? $event->end_datetime->format('Y-m-d') : null,
                'type' => 'event',
                'className' => 'bg-event',
            ];
        }

        return view('public.news', [
            'currentRoute' => 'public.news',
            'announcements' => $announcements,
            'events' => $events,
            'calendarEvents' => $calendarEvents,
            'pageTitle' => 'News & Events',
            'pageDescription' => 'Latest news, announcements and events from Barangay Bucandala 1',
        ]);
    }

    public function home()
    {
        $announcements = Announcement::with('creator:id,email')
            ->where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'page_announcements');

        $events = Event::where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('start_datetime', 'desc')
            ->paginate(5, ['*'], 'page_events');

        $calendarEvents = [];
        $allAnnouncements = Announcement::where('is_published', true)
            ->whereNull('archived_at')
            ->get();
        $allEvents = Event::where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('start_datetime', 'desc')
            ->get();

        foreach ($allAnnouncements as $announcement) {
            $calendarEvents[] = [
                'id' => 'announcement-'.$announcement->id,
                'title' => $announcement->title,
                'start' => $announcement->publish_date ? $announcement->publish_date->format('Y-m-d') : $announcement->created_at->format('Y-m-d'),
                'type' => 'announcement',
                'className' => 'bg-announcement',
            ];
        }
        foreach ($allEvents as $event) {
            $calendarEvents[] = [
                'id' => 'event-'.$event->id,
                'title' => $event->title,
                'start' => $event->start_datetime->format('Y-m-d'),
                'end' => $event->end_datetime ? $event->end_datetime->format('Y-m-d') : null,
                'type' => 'event',
                'className' => 'bg-event',
            ];
        }

        return view('public.home', [
            'currentRoute' => 'public.home',
            'announcements' => $announcements,
            'events' => $events,
            'calendarEvents' => $calendarEvents,
            'pageTitle' => 'Barangay Bucandala 1 - Official Website',
            'pageDescription' => 'Barangay Bucandala 1 - City of Imus, Cavite. Official website for resident services, document requests, announcements, and community information.',
        ]);
    }
}
