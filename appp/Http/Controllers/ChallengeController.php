<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChallengeController extends Controller
{
    // Show today's challenge (randomly chosen per day) to normal users and collectors
    public function index(Request $request)
    {
        $today = now()->toDateString();

        // Choose a deterministic "random" challenge for the day using hashing
        $challenge = Challenge::where('status', 'active')->get()->whenNotEmpty(function ($list) use ($today) {
            $index = hexdec(substr(md5($today), 0, 8)) % $list->count();
            return $list->values()[$index];
        }, function () {
            return null;
        });

        // If a single model was returned via whenNotEmpty then ensure $challenge is a model
        if ($challenge instanceof \Illuminate\Support\Collection) {
            $challenge = $challenge->first();
        }

        $userSubmission = null;
        if (Auth::check() && $challenge) {
            $userSubmission = ChallengeSubmission::where('challenge_id', $challenge->id)
                ->where('user_id', Auth::id())
                ->latest()
                ->first();
        }

        return view('challenges.index', compact('challenge', 'userSubmission'));
    }

    // Admin: show form to create new challenge
    public function create()
    {
        $this->authorizeAdmin();
        return view('challenges.create');
    }

    // Admin: store new challenge
    public function store(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'goal' => 'nullable|integer',
            'reward' => 'nullable|string',
            'status' => 'required|in:upcoming,active,completed',
        ]);
        Challenge::create($data);
        return redirect()->route('challenges.index')->with('success', 'Challenge created.');
    }

    // Auth users submit a proof (file or text)
    public function submitProof(Request $request, $challengeId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $challenge = Challenge::findOrFail($challengeId);
        $data = $request->validate([
            'proof_text' => 'nullable|string',
            'proof_image' => 'nullable|image|max:5120',
        ]);
        $submission = new ChallengeSubmission();
        $submission->challenge_id = $challenge->id;
        $submission->user_id = Auth::id();
        $submission->proof_text = $data['proof_text'] ?? null;
        if ($request->hasFile('proof_image')) {
            $submission->proof_image = base64_encode(file_get_contents($request->file('proof_image')->getRealPath()));
        }
        $submission->status = 'pending';
        $submission->save();
        return redirect()->route('challenges.index')->with('success', 'Proof submitted, awaiting admin approval.');
    }

    // Admin approves a submission
    public function approveSubmission($id)
    {
        $this->authorizeAdmin();
        $submission = ChallengeSubmission::findOrFail($id);
        $submission->status = 'approved';
        $submission->approved_by = Auth::id();
        $submission->approved_at = now();
        $submission->save();
        return back()->with('success', 'Submission approved.');
    }

    protected function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }
}
