<?php 

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class RecommendationsService
{
    public function recommend(array $emotions): array
    {
        $response = Http::timeout(10)->post(
            'http://127.0.0.1:5000/api/recommendations',
            $emotions
        );

        Log::info('Python API Raw Response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if (!$response->successful()){
            throw new \Exception('python recommendation service failed');
        }
        return $response->json()['recommendations'] ?? [];
    }
}