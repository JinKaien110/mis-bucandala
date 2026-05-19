<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);
        
        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $events = Event::published()
            ->notArchived()
            ->whereBetween('start_datetime', [$startOfMonth, $endOfMonth])
            ->orderBy('start_datetime')
            ->get();

        // Get all published announcements within the month
        $announcements = Announcement::with('creator:id,email')
            ->notArchived()
            ->where('is_published', true)
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('publish_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhereBetween('expire_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('publish_date', '<=', $endOfMonth->toDateString())
                          ->where(function($sub) use ($startOfMonth) {
                              $sub->where('expire_date', '>=', $startOfMonth->toDateString())
                                  ->orWhereNull('expire_date');
                          });
                    });
            })
            ->get();

        // Get upcoming events for sidebar
        $upcomingEvents = Event::upcoming()->published()->limit(5)->get();

        // Get upcoming announcements (all published)
        $upcomingAnnouncements = Announcement::with('creator:id,email')
            ->where('is_published', true)
            ->where('publish_date', '>=', now()->toDateString())
            ->orderBy('publish_date')
            ->limit(5)
            ->get();

        return view('admin.events.index', [
            'events' => $events,
            'announcements' => $announcements,
            'upcomingEvents' => $upcomingEvents,
            'upcomingAnnouncements' => $upcomingAnnouncements,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'start_datetime' => ['required', 'date'],
                'end_datetime' => ['nullable', 'date', 'after:start_datetime'],
                'location' => ['nullable', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:50'],
                'is_all_day' => ['nullable'],
                'is_published' => ['nullable'],
                'reminder' => ['nullable', 'string'],
            ]);

            $data['created_by'] = Auth::id();
            $data['is_all_day'] = $request->has('is_all_day') ? true : false;
            $data['is_published'] = $request->has('is_published') ? true : false;

            Event::create($data);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Event created successfully.']);
            }

            return redirect()->route('admin.events.index')
                ->with('success', 'Event created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['nullable', 'date', 'after:start_datetime'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'is_all_day' => ['boolean'],
            'is_published' => ['boolean'],
            'reminder' => ['nullable', 'string'],
        ]);

        $data['is_all_day'] = $data['is_all_day'] ?? false;
        $data['is_published'] = $data['is_published'] ?? false;

        $event->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Event updated successfully.']);
        }

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->archive();
        return redirect()->route('admin.events.index')
            ->with('success', 'Event archived successfully.');
    }
}
