<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventAIAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventAnalyticsController extends Controller
{
    protected $aiAnalyzer;

    public function __construct(EventAIAnalyzer $aiAnalyzer)
    {
        $this->aiAnalyzer = $aiAnalyzer;
    }

    /**
     * Display AI analytics for an event
     */
    public function show(Event $event)
    {
        // Only admin or event creator can view analytics
        if (!Auth::user()->isAdmin() && $event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to view analytics for this event.');
        }

        // If not analyzed yet or feedback has changed, re-analyze
        if (!$event->ai_analyzed_at || 
            $event->feedback()->where('updated_at', '>', $event->ai_analyzed_at)->exists()) {
            $this->aiAnalyzer->analyzeFeedback($event);
            $event->refresh();
        }

        $insights = json_decode($event->ai_insights, true);
        $reportSummary = $this->aiAnalyzer->generateReportSummary($event);

        return view('events.analytics', compact('event', 'insights', 'reportSummary'));
    }

    /**
     * Generate/Regenerate AI analysis for an event
     */
    public function generate(Event $event)
    {
        // Only admin or event creator can generate analytics
        if (!Auth::user()->isAdmin() && $event->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized to generate analytics for this event.');
        }

        $result = $this->aiAnalyzer->analyzeFeedback($event);

        if ($result['success']) {
            return back()->with('success', 'AI analysis completed successfully!');
        } else {
            return back()->with('error', $result['message']);
        }
    }
}
