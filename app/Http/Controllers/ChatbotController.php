<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private $geminiApiKey;
    private $geminiApiUrl;

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';
    }

    public function index()
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        
        // Get chat history for this user/session
        $conversations = ChatConversation::where(function($query) use ($user, $sessionId) {
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->orderBy('created_at', 'asc')
        ->take(50) // Last 50 messages
        ->get();

        return view('chatbot.index', compact('conversations'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user = auth()->user();
        $sessionId = session()->getId();
        $userName = $user ? $user->name : 'Guest';

        // Create the prompt for LABIB with waste-to-product specialization
        $systemPrompt = "You are LABIB, a friendly and knowledgeable AI assistant specialized in waste management, recycling, and waste-to-product solutions. You help users understand how to convert waste materials into valuable products, provide recycling tips, suggest creative upcycling ideas, and promote environmental sustainability. Always address the user by their name ({$userName}) and be helpful, professional, and encouraging about environmental conservation. Focus your responses on:\n\n1. Waste reduction strategies\n2. Recycling processes and best practices\n3. Upcycling and creative reuse ideas\n4. Converting specific waste materials into products\n5. Environmental impact and sustainability\n6. Circular economy concepts\n7. DIY waste-to-product projects\n\nKeep responses concise (2-3 paragraphs), friendly, and actionable.";

        $userMessage = $request->message;

        // Get recent conversation context (last 5 messages)
        $recentConversations = ChatConversation::where(function($query) use ($user, $sessionId) {
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get()
        ->reverse();

        // Build conversation history for context
        $conversationHistory = $recentConversations->map(function($conv) {
            return [
                'user' => $conv->message,
                'assistant' => $conv->response
            ];
        })->toArray();

        // Call Gemini API
        try {
            $response = $this->callGeminiApi($systemPrompt, $userMessage, $conversationHistory, $userName);
            
            // Save conversation
            ChatConversation::create([
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'message' => $userMessage,
                'response' => $response,
                'sender' => 'user'
            ]);

            return response()->json([
                'success' => true,
                'response' => $response,
                'sender' => 'labib'
            ]);

        } catch (\Exception $e) {
            \Log::error('Chatbot Error: ' . $e->getMessage());
            \Log::error('Chatbot Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Sorry, I\'m having trouble connecting right now. Please try again in a moment.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function callGeminiApi($systemPrompt, $userMessage, $conversationHistory, $userName)
    {
        // Build the full prompt with context
        $fullPrompt = $systemPrompt . "\n\n";
        
        // Add conversation history for context
        foreach ($conversationHistory as $conv) {
            $fullPrompt .= "User ({$userName}): " . $conv['user'] . "\n";
            $fullPrompt .= "LABIB: " . $conv['assistant'] . "\n\n";
        }
        
        $fullPrompt .= "User ({$userName}): " . $userMessage . "\n";
        $fullPrompt .= "LABIB: ";

        \Log::info('Making Gemini API request for user: ' . $userName);
        \Log::info('API URL: ' . $this->geminiApiUrl);
        \Log::info('API Key exists: ' . (!empty($this->geminiApiKey) ? 'Yes' : 'No'));

        $response = Http::timeout(30)->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $fullPrompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 500,
            ]
        ]);

        \Log::info('Gemini API Response Status: ' . $response->status());
        
        if (!$response->successful()) {
            $errorBody = $response->body();
            \Log::error('Gemini API Error Response: ' . $errorBody);
            throw new \Exception('Gemini API returned error: ' . $response->status() . ' - ' . $errorBody);
        }

        $data = $response->json();
        \Log::info('Gemini API Response: ' . substr(json_encode($data), 0, 500));
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        \Log::error('Unexpected Gemini API response structure: ' . json_encode($data));
        throw new \Exception('Unexpected response structure from Gemini API');
    }

    public function clearHistory()
    {
        $user = auth()->user();
        $sessionId = session()->getId();

        ChatConversation::where(function($query) use ($user, $sessionId) {
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return response()->json(['success' => true]);
    }
}
