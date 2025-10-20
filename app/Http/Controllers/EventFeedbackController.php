<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventFeedbackController extends Controller
{
    /**
     * Store a newly created feedback
     */
    public function store(Request $request, Event $event)
    {
        // Check if event has ended
        if (!$event->hasEnded()) {
            return back()->with('error', 'You can only provide feedback for ended events.');
        }

        // Check if user already provided feedback
        if ($event->hasUserFeedback(Auth::id())) {
            return back()->with('error', 'You have already provided feedback for this event.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        EventFeedback::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Update the specified feedback
     */
    public function update(Request $request, EventFeedback $feedback)
    {
        // Ensure user owns this feedback
        if ($feedback->user_id !== Auth::id()) {
            return back()->with('error', 'You can only edit your own feedback.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $feedback->update($validated);

        return back()->with('success', 'Your feedback has been updated!');
    }

    /**
     * Remove the specified feedback
     */
    public function destroy(EventFeedback $feedback)
    {
        // Ensure user owns this feedback
        if ($feedback->user_id !== Auth::id()) {
            return back()->with('error', 'You can only delete your own feedback.');
        }

        $feedback->delete();

        return back()->with('success', 'Your feedback has been deleted.');
    }
}
