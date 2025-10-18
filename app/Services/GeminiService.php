<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Analyze challenge submission proof (image + description)
     * 
     * @param string $challengeTitle The challenge title
     * @param string $challengeDescription The challenge description
     * @param string $userDescription User's proof description
     * @param string $imageBase64 Base64 encoded image
     * @return array ['approved' => bool, 'confidence' => float, 'reason' => string]
     */
    public function analyzeSubmission(
        string $challengeTitle,
        string $challengeDescription,
        string $userDescription,
        string $imageBase64
    ): array {
        try {
            // Remove data URI prefix if exists
            if (str_starts_with($imageBase64, 'data:image')) {
                $imageBase64 = explode(',', $imageBase64)[1];
            }

            // Create the prompt
            $prompt = $this->createAnalysisPrompt($challengeTitle, $challengeDescription, $userDescription);

            // Make API request
            $response = Http::timeout(30)
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => 'image/jpeg',
                                        'data' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'topK' => 32,
                        'topP' => 1,
                        'maxOutputTokens' => 500,
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('✅ Gemini API Success', [
                    'has_candidates' => isset($result['candidates']),
                    'candidates_count' => count($result['candidates'] ?? []),
                    'full_response' => json_encode($result, JSON_PRETTY_PRINT)
                ]);
                
                return $this->parseGeminiResponse($result);
            }

            Log::error('❌ Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            return $this->getDefaultResponse('API request failed');

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getDefaultResponse('Exception: ' . $e->getMessage());
        }
    }

    /**
     * Create analysis prompt for Gemini
     */
    protected function createAnalysisPrompt(string $challengeTitle, string $challengeDescription, string $userDescription): string
    {
        return <<<PROMPT
You are an AI assistant helping to verify environmental challenge submissions. Analyze the image and description to determine if the submission is valid.

**Challenge Details:**
Title: {$challengeTitle}
Description: {$challengeDescription}

**User's Submission Description:**
{$userDescription}

**Your Task:**
1. Examine the provided image carefully
2. Check if the image proof matches the challenge requirements
3. Verify if the user's description is consistent with what's shown in the image
4. Assess if this is a legitimate attempt to complete the challenge

**Response Format (IMPORTANT - Reply ONLY in this exact JSON format):**
{
    "approved": true or false,
    "confidence": 0.0 to 1.0,
    "reason": "Brief explanation (max 100 words)"
}

**Guidelines:**
- Approve if: Image clearly shows completion of the challenge AND description is relevant
- Reject if: Image doesn't match challenge OR description is irrelevant OR appears fake
- Confidence: 0.9-1.0 = very certain, 0.7-0.89 = moderately certain, <0.7 = uncertain
- Be fair but vigilant against fake submissions

Respond ONLY with the JSON object, no additional text.
PROMPT;
    }

    /**
     * Parse Gemini API response
     */
    protected function parseGeminiResponse(array $result): array
    {
        try {
            $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('📝 Parsing AI Response', [
                'raw_text' => $text,
                'text_length' => strlen($text)
            ]);
            
            // Extract JSON from response (in case there's extra text)
            if (preg_match('/\{[^}]+\}/', $text, $matches)) {
                $jsonStr = $matches[0];
                $data = json_decode($jsonStr, true);

                Log::info('🔍 JSON Parse Attempt', [
                    'found_json' => $jsonStr,
                    'parsed_data' => $data,
                    'json_error' => json_last_error_msg(),
                    'has_approved_key' => isset($data['approved'])
                ]);

                if (json_last_error() === JSON_ERROR_NONE && isset($data['approved'])) {
                    Log::info('✨ Successfully parsed structured response', [
                        'approved' => $data['approved'],
                        'confidence' => $data['confidence'] ?? 0.5
                    ]);
                    
                    return [
                        'approved' => (bool) $data['approved'],
                        'confidence' => (float) ($data['confidence'] ?? 0.5),
                        'reason' => (string) ($data['reason'] ?? 'No reason provided'),
                        'raw_response' => $text
                    ];
                }
            }

            // If JSON parsing fails, try to interpret the response
            $text = strtolower($text);
            $approved = str_contains($text, 'approved') || 
                       str_contains($text, 'valid') || 
                       str_contains($text, 'legitimate');

            return [
                'approved' => $approved,
                'confidence' => 0.5,
                'reason' => 'Could not parse structured response. Manual review recommended.',
                'raw_response' => $text
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse Gemini response', [
                'error' => $e->getMessage(),
                'result' => $result
            ]);

            return $this->getDefaultResponse('Failed to parse AI response');
        }
    }

    /**
     * Get default response when AI fails
     */
    protected function getDefaultResponse(string $reason): array
    {
        return [
            'approved' => false,
            'confidence' => 0.0,
            'reason' => $reason . ' - Manual review required.',
            'raw_response' => null
        ];
    }
}
