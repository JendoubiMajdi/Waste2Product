<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index()
    {
        $upcomingEvents = Event::with(['user', 'participants'])
            ->where('status', 'active')
            ->where('event_date', '>=', now())
            ->withCount('participants')
            ->orderBy('event_date')
            ->paginate(12);

        $pastEvents = Event::with(['user', 'participants'])
            ->where('status', 'completed')
            ->orWhere(function ($query) {
                $query->where('event_date', '<', now());
            })
            ->withCount('participants')
            ->orderBy('event_date', 'desc')
            ->paginate(12);

        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        // Only admins can create events
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can create events.');
        }

        return view('events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can create events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date|after:today',
            'max_participants' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Split datetime-local into date and time
        $datetime = new \DateTime($request->event_date);
        $validated['event_date'] = $datetime->format('Y-m-d');
        $validated['event_time'] = $datetime->format('H:i:s');

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }

        Event::create($validated);

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $event->load(['user', 'participants']);
        $isRegistered = Auth::check() && $event->isUserRegistered(Auth::id());

        return view('events.show', compact('event', 'isRegistered'));
    }

    /**
     * Register for an event
     */
    public function register(Event $event)
    {
        if ($event->isFull()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'This event is already full.');
        }

        if ($event->isUserRegistered(Auth::id())) {
            return redirect()->route('events.show', $event)
                ->with('info', 'You are already registered for this event.');
        }

        $event->participants()->attach(Auth::id());

        return redirect()->route('events.show', $event)
            ->with('success', 'You have successfully registered for this event!');
    }

    /**
     * Unregister from an event
     */
    public function unregister(Event $event)
    {
        if (! $event->isUserRegistered(Auth::id())) {
            return redirect()->route('events.show', $event)
                ->with('error', 'You are not registered for this event.');
        }

        $event->participants()->detach(Auth::id());

        return redirect()->route('events.show', $event)
            ->with('success', 'You have successfully unregistered from this event.');
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit events.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can edit events.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,cancelled,completed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Split datetime-local into date and time
        $datetime = new \DateTime($request->event_date);
        $validated['event_date'] = $datetime->format('Y-m-d');
        $validated['event_time'] = $datetime->format('H:i:s');

        if ($request->hasFile('image')) {
            $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
            $validated['image'] = $imageData;
        }

        $event->update($validated);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete events.');
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Display events that the authenticated user has registered for
     */
    public function myEvents()
    {
        $user = Auth::user();

        // Get events the user has registered for
        $upcomingEvents = $user->events()
            ->with(['user', 'participants'])
            ->withCount('participants')
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->get();

        $pastEvents = $user->events()
            ->with(['user', 'participants'])
            ->withCount('participants')
            ->where('event_date', '<', now())
            ->orderBy('event_date', 'desc')
            ->get();

        return view('events.my-events', compact('upcomingEvents', 'pastEvents'));
    }
}
