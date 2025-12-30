<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class EmotionService
{
    private string $endpoint = 'http://127.0.0.1:5000/predict';

    public function predictFromFile(UploadedFile $file): array
    {
        $response = Http::timeout(30)->asMultipart()->attach(
            'file',
            file_get_contents($file->getRealPath()),
            $file->getClientOriginalName()
        )->post($this->endpoint);

        if ($response->failed()) {
            throw new \Exception('AI Model Error: ' . $response->body());
        }
        
        return $response->json();
    }
}