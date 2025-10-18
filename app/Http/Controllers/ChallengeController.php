<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            'description' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        }
        
        Challenge::create($data);
        
        return redirect()->route('challenges.index')->with('success', 'Challenge created successfully!');
    }

    // Auth users submit a proof (file or text)
    public function submitProof(Request $request, $challengeId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $challenge = Challenge::findOrFail($challengeId);
        $data = $request->validate([
            'description' => 'nullable|string',
            'proof_image' => 'nullable|image|max:5120',
        ]);
        
        $submission = new ChallengeSubmission();
        $submission->challenge_id = $challenge->id;
        $submission->user_id = Auth::id();
        $submission->description = $data['description'] ?? null;
        
        if ($request->hasFile('proof_image')) {
            $submission->proof_image = base64_encode(file_get_contents($request->file('proof_image')->getRealPath()));
        }
        
        $submission->status = 'pending';
        $submission->save();
        
        // Auto-approve with AI if enabled and we have both image and description
        if (config('services.gemini.api_key') && $submission->proof_image && $submission->description) {
            try {
                Log::info('🤖 Starting AI analysis', [
                    'submission_id' => $submission->id,
                    'challenge_title' => $challenge->title,
                    'has_image' => !empty($submission->proof_image),
                    'has_description' => !empty($submission->description),
                    'description_length' => strlen($submission->description ?? '')
                ]);
                
                $geminiService = new \App\Services\GeminiService();
                $aiResult = $geminiService->analyzeSubmission(
                    $challenge->title,
                    $challenge->description,
                    $submission->description,
                    $submission->proof_image
                );
                
                Log::info('🎯 AI Analysis Result', [
                    'submission_id' => $submission->id,
                    'approved' => $aiResult['approved'],
                    'confidence' => $aiResult['confidence'],
                    'confidence_percent' => round($aiResult['confidence'] * 100, 2) . '%',
                    'reason' => $aiResult['reason'],
                    'raw_response_preview' => substr($aiResult['raw_response'] ?? 'none', 0, 200)
                ]);
                
                // Store AI analysis
                $submission->review_notes = json_encode([
                    'ai_approved' => $aiResult['approved'],
                    'ai_confidence' => $aiResult['confidence'],
                    'ai_reason' => $aiResult['reason'],
                    'reviewed_at' => now()->toDateTimeString()
                ]);
                
                // Auto-approve if AI is confident
                if ($aiResult['approved'] && $aiResult['confidence'] >= 0.75) {
                    $submission->status = 'approved';
                    $submission->reviewed_by = null; // AI approval, not admin
                    $submission->reviewed_at = now();
                    $submission->save();
                    
                    // Award points to user and update badge
                    $user = $submission->user;
                    $user->increment('points', $challenge->points);
                    
                    // Update badge based on points
                    if ($user->points >= 1000) {
                        $user->badge = 'diamond';
                    } elseif ($user->points >= 500) {
                        $user->badge = 'platinum';
                    } elseif ($user->points >= 250) {
                        $user->badge = 'gold';
                    } elseif ($user->points >= 100) {
                        $user->badge = 'silver';
                    } elseif ($user->points >= 50) {
                        $user->badge = 'bronze';
                    } else {
                        $user->badge = 'beginner';
                    }
                    $user->save();
                    
                    return redirect()->route('challenges.index')->with('success', 
                        "✅ Proof approved automatically! You earned {$challenge->points} points. AI Confidence: " . 
                        round($aiResult['confidence'] * 100) . "%");
                }
                
                // Flag for manual review if AI is uncertain
                if ($aiResult['confidence'] < 0.75 && $aiResult['confidence'] >= 0.5) {
                    $submission->save();
                    return redirect()->route('challenges.index')->with('info', 
                        'Proof submitted for manual review. AI was uncertain about approval.');
                }
                
                // Auto-reject if AI is confident it's invalid
                if (!$aiResult['approved'] && $aiResult['confidence'] >= 0.75) {
                    $submission->status = 'rejected';
                    $submission->reviewed_at = now();
                    $submission->save();
                    return redirect()->route('challenges.index')->with('error', 
                        '❌ Proof rejected. Reason: ' . $aiResult['reason']);
                }
                
                // Default: pending for manual review
                $submission->save();
                return redirect()->route('challenges.index')->with('info', 
                    'Proof submitted, awaiting manual review.');
                    
            } catch (\Exception $e) {
                Log::error('AI approval failed', ['error' => $e->getMessage()]);
                // Continue with manual approval if AI fails
            }
        }
        
        return redirect()->route('challenges.index')->with('success', 'Proof submitted, awaiting admin approval.');
    }

    // Admin approves a submission
    public function approveSubmission($id)
    {
        $this->authorizeAdmin();
        $submission = ChallengeSubmission::findOrFail($id);
        
        // Check if already approved (prevent double point award)
        if ($submission->status === 'approved') {
            return back()->with('info', 'Submission already approved.');
        }
        
        $submission->status = 'approved';
        $submission->reviewed_by = Auth::id();
        $submission->reviewed_at = now();
        $submission->save();
        
        // Award points to user and update badge
        $challenge = $submission->challenge;
        $user = $submission->user;
        $user->addPoints($challenge->points);
        
        return back()->with('success', "Submission approved! User earned {$challenge->points} points.");
    }
    
    // Admin rejects a submission
    public function rejectSubmission(Request $request, $id)
    {
        $this->authorizeAdmin();
        $submission = ChallengeSubmission::findOrFail($id);
        
        $submission->status = 'rejected';
        $submission->reviewed_by = Auth::id();
        $submission->reviewed_at = now();
        $submission->review_notes = $request->input('notes');
        $submission->save();
        
        return back()->with('success', 'Submission rejected.');
    }

    protected function authorizeAdmin()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }
}
