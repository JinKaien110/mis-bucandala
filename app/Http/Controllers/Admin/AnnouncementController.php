<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        
        $announcements = Announcement::with('creator.admin')
            ->notArchived()
            ->when($type, fn($q) => $q->where('type', $type))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements.index', [
            'announcements' => $announcements,
            'type' => $type,
        ]);
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:general,event,urgent'],
            'publish_date' => ['nullable', 'date'],
            'expire_date' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'show_on_calendar' => ['boolean'],
        ]);

        $data['created_by'] = Auth::id();
        
        // Create the announcement first
        $announcement = Announcement::create($data);
        
        // If show_on_calendar is checked and there's a publish_date, create an event
        if ($request->boolean('show_on_calendar') && $data['publish_date']) {
            $event = Event::create([
                'title' => $data['title'],
                'description' => $data['content'],
                'start_datetime' => $data['publish_date'] . ' 09:00:00',
                'end_datetime' => $data['publish_date'] . ' 17:00:00',
                'type' => 'announcement',
                'is_all_day' => true,
                'is_published' => $data['is_published'] ?? false,
                'created_by' => $data['created_by'],
            ]);
            
            // Link the event to the announcement
            $announcement->update(['event_id' => $event->id]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Announcement created successfully.']);
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:general,event,urgent'],
            'publish_date' => ['nullable', 'date'],
            'expire_date' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'show_on_calendar' => ['boolean'],
        ]);

        $announcement->update($data);
        
        // Handle event linkage
        if ($request->boolean('show_on_calendar') && $data['publish_date']) {
            if ($announcement->event) {
                // Update existing event
                $announcement->event->update([
                    'title' => $data['title'],
                    'description' => $data['content'],
                    'start_datetime' => $data['publish_date'] . ' 09:00:00',
                    'end_datetime' => $data['publish_date'] . ' 17:00:00',
                    'is_published' => $data['is_published'] ?? false,
                ]);
            } else {
                // Create new event
                $event = Event::create([
                    'title' => $data['title'],
                    'description' => $data['content'],
                    'start_datetime' => $data['publish_date'] . ' 09:00:00',
                    'end_datetime' => $data['publish_date'] . ' 17:00:00',
                    'type' => 'announcement',
                    'is_all_day' => true,
                    'is_published' => $data['is_published'] ?? false,
                    'created_by' => auth()->id(),
                ]);
                $announcement->update(['event_id' => $event->id]);
            }
        } else {
            // Remove event linkage if exists
            if ($announcement->event) {
                $announcement->event->archive();
                $announcement->update(['event_id' => null]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Announcement updated successfully.']);
        }

        return redirect()->route('admin.announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        // Archive associated event if exists
        if ($announcement->event) {
            $announcement->event->archive();
        }
        
        $announcement->archive();
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement archived successfully.');
    }

    // API methods for modals
    public function getCreateData()
    {
        return response()->json([
            'events' => Event::where('is_published', true)->orderBy('start_datetime')->get()
        ]);
    }

    public function getEditData(Announcement $announcement)
    {
        return response()->json([
            'announcement' => $announcement->load('creator.admin'),
            'events' => Event::where('is_published', true)->orderBy('start_datetime')->get()
        ]);
    }

    public function getShowData(Announcement $announcement)
    {
        return response()->json([
            'announcement' => $announcement->load('creator.admin')
        ]);
    }
}
