<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VoiceMessageController extends Controller
{
    private $openaiApiKey;
    
    public function __construct()
    {
        $this->openaiApiKey = env('OPENAI_API_KEY');
    }

    /**
     * Process voice message for user-to-user chat
     */
    public function processUserVoice(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,ogg|max:10240',
            'receiver_id' => 'required|exists:users,id',
            'conversation_id' => 'required|exists:conversations,id'
        ]);

        try {
            $user = auth()->user();

            // Store the audio file
            $audioFile = $request->file('audio');
            $fileName = 'voice_' . time() . '_' . Str::random(10) . '.' . $audioFile->getClientOriginalExtension();
            $filePath = $audioFile->storeAs('voice_messages/users', $fileName, 'public');

            // Get audio duration
            $duration = $this->estimateAudioDuration($audioFile);

            // Transcribe audio
            $transcription = $this->transcribeAudio(storage_path('app/public/' . $filePath));

            // Save message
            $message = Message::create([
                'conversation_id' => $request->conversation_id,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'message' => $transcription,
                'voice_file_path' => $filePath,
                'voice_transcription' => $transcription,
                'voice_tone' => null,
                'voice_duration' => $duration
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'transcription' => $transcription,
                'voice_url' => Storage::url($filePath),
                'duration' => $duration
            ]);

        } catch (\Exception $e) {
            \Log::error('Voice processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to process voice message',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Transcribe audio using OpenAI Whisper API
     */
    private function transcribeAudio($audioPath)
    {
        try {
            \Log::info('Starting Whisper transcription for: ' . $audioPath);

            // Whisper API requires multipart/form-data
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openaiApiKey,
                ])
                ->attach(
                    'file',
                    file_get_contents($audioPath),
                    basename($audioPath)
                )
                ->post('https://api.openai.com/v1/audio/transcriptions', [
                    'model' => 'whisper-1',
                    'language' => 'en', // Auto-detect or specify language (en, fr, ar, etc.)
                    'response_format' => 'json',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['text']) && !empty($data['text'])) {
                    \Log::info('Whisper transcription successful: ' . substr($data['text'], 0, 100));
                    return trim($data['text']);
                }
            }

            // Log error details
            $errorBody = $response->body();
            \Log::error('Whisper API error: ' . $errorBody);
            \Log::error('Status code: ' . $response->status());

            // Fallback to placeholder if Whisper fails
            return "Voice message received (transcription unavailable).";

        } catch (\Exception $e) {
            \Log::error('Whisper transcription exception: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback to placeholder
            return "Voice message received (transcription failed).";
        }
    }

    /**
     * Estimate audio duration from file size (rough approximation)
     */
    private function estimateAudioDuration($file)
    {
        // Rough estimate: average bitrate for voice is about 32kbps
        // Duration (seconds) ≈ (file_size_bytes * 8) / (bitrate_bps)
        $fileSizeBytes = $file->getSize();
        $estimatedDuration = round(($fileSizeBytes * 8) / (32 * 1000));
        
        return max(1, $estimatedDuration); // At least 1 second
    }
}
