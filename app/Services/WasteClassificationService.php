<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WasteClassificationService
{
    protected $apiUrl;
    protected $timeout;

    public function __construct()
    {
        $this->apiUrl = env('WASTE_AI_API_URL', 'http://localhost:5000');
        $this->timeout = env('WASTE_AI_TIMEOUT', 30);
    }

    /**
     * Check if the AI API is available
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            return $response->successful() && $response->json('model_loaded', false);
        } catch (Exception $e) {
            Log::warning('AI API health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Classify waste from base64 encoded image
     * 
     * @param string $base64Image Base64 encoded image
     * @return array ['success' => bool, 'waste_type' => string, 'confidence' => float, 'error' => string]
     */
    public function classifyFromBase64(string $base64Image): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/classify", [
                    'image' => $base64Image
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'waste_type' => $data['waste_type'] ?? 'Unknown',
                    'confidence' => $data['confidence'] ?? 0,
                    'all_predictions' => $data['all_predictions'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'API returned error: ' . $response->body(),
            ];

        } catch (Exception $e) {
            Log::error('Waste classification error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Classify waste from uploaded file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function classifyFromFile($file): array
    {
        try {
            // Convert file to base64
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            
            return $this->classifyFromBase64($imageData);

        } catch (Exception $e) {
            Log::error('File classification error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get available waste categories
     */
    public function getCategories(): array
    {
        return [
            'Plastic',
            'Paper',
            'Metal',
            'Glass',
            'General Waste',
        ];
    }
}
