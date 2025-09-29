<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $events = Event::with(['user', 'participants'])
            ->when($search, function($query, $search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get();

        return view('events.index', compact('events', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    if (!auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('verification.notice')
            ->with('error', 'Please verify your email before creating events.');
    }

    return view('events.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            if (!auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('verification.notice')
            ->with('error', 'Please verify your email before creating events.');
    }
    
        $request->validate([
            'title' => 'required|string|max:255',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'meet_link' => 'required|url',
        ]);

        $data = $request->only(['title', 'description', 'date', 'time', 'meet_link']);
        $data['user_id'] = auth()->id();

        // Handle picture upload
        if ($request->hasFile('picture')) {
            $data['picture'] = $request->file('picture')->store('events', 'public');
        }

        $event = Event::create($data);

        return redirect()->route('events.show', $event->id)->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with(['user', 'participants'])->findOrFail($id);
        $participants = $event->participants;
        return view('events.show', compact('event', 'participants'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);

        // Check if user is the event creator
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $event = Event::findOrFail($id);

        // Check if user is the event creator
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'meet_link' => 'required|url',
        ]);

        $data = $request->only(['title', 'description', 'date', 'time', 'meet_link']);

        // Handle picture upload
        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($event->picture) {
                Storage::disk('public')->delete($event->picture);
            }
            $data['picture'] = $request->file('picture')->store('events', 'public');
        }

        $event->update($data);

        return redirect()->route('events.show', $event->id)->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);

        // Check if user is the event creator
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete picture if exists
        if ($event->picture) {
            Storage::disk('public')->delete($event->picture);
        }

        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully!');
    }

    /**
     * Join an event.
     */
    public function join($eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = auth()->user();

        // Check if user already joined
        if (!$event->participants->contains($user->id)) {
            $event->participants()->attach($user->id);
        }

        return redirect()->route('events.show', $eventId)->with('success', 'You joined the event!');
    }
}
