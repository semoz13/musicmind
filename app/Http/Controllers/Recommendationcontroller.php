<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendationsService;
use App\Services\SongService;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function recommend(
        Request $request,
        RecommendationsService $recommender,
        SongService $songService
        ) {
        $validated = $request->validate([
            'happiness' => 'required|numeric|min:0|max:10',
            'sadness' => 'required|numeric|min:0|max:10',
            'energy' => 'required|numeric|min:0|max:10',
            'calmness' => 'required|numeric|min:0|max:10',
            'danceability' => 'required|numeric|min:0|max:10',
            'tempo' => 'required|numeric|min:0|max:10',
        ]);
        
        try {
            // 1. Get AI recommendations from Python service
            $aiRecommendations = $recommender->recommend($validated);
            
            if (empty($aiRecommendations)) {
                return response()->json([
                    'error' => 'No recommendations found',
                    'data' => []
                ], 404);
            }
            
            // 2. Enrich with Spotify data
            $finalSongs = [];
            foreach ($aiRecommendations as $rec) {
                $searchQuery = $rec['track_name'] . ' ' . $rec['artists'];
                
                try {
                    $spotifyResult = $songService->searchSongs($searchQuery, 1);
                    
                    if (!empty($spotifyResult['tracks']['items'][0])) {
                        $track = $spotifyResult['tracks']['items'][0];
                        $finalSongs[] = [
                            'spotify_id' => $track['id'],
                            'name' => $track['name'],
                            'artists' => collect($track['artists'])->pluck('name')->join(', '),
                            'album' => $track['album']['name'],
                            'preview_url' => $track['preview_url'] ?? null,
                            'album_image' => $track['album']['images'][0]['url'] ?? null,
                            'spotify_url' => $track['external_urls']['spotify'] ?? null,
                            'ai_similarity_score' => $rec['distance'],
                        ];
                    }
                } catch (\Exception $e) {
                    Log::warning("Spotify search failed for: {$searchQuery}", [
                        'error' => $e->getMessage()
                    ]);
                    // Continue with next track
                    continue;
                }
            }
            
            return response()->json([
                'success' => true,
                'count' => count($finalSongs),
                'data' => $finalSongs
            ]);
            
        } catch (\Exception $e) {
            Log::error('Recommendation failed', [
                'error' => $e->getMessage(),
                'input' => $validated
            ]);
            
            return response()->json([
                'error' => 'Recommendation service unavailable',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}