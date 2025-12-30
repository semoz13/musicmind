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
            'happiness' => 'required|integer|min:0|max:100',
            'sadness' => 'required|integer|min:0|max:100',
            'energy' => 'required|integer|min:0|max:100',
            'calmness' => 'required|integer|min:0|max:100',
            'danceability' => 'required|integer|min:0|max:100',
            'tempo' => 'required|integer|min:0|max:100',
            'top_n' => 'nullable|integer|min:1|max:20'
        ]);

        try {
            // 1️⃣ Call Python AI service
            $aiResponse = $recommender->recommend($validated);

            $recommendations = $aiResponse['recommendations'] ?? [];

            if (empty($recommendations)) {
                return response()->json([
                    'success' => true,
                    'count' => 0,
                    'data' => []
                ]);
            }

            // 2️⃣ Enrich with Spotify data
            $finalSongs = [];

            foreach ($recommendations as $rec) {

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
                            'image' => $track['album']['images'][0]['url'] ?? null,
                            'preview_url' => $track['preview_url'],
                            'distance' => $rec['distance'],
                            'source' => 'spotify'
                        ];
                    } else {
                        // fallback
                        $finalSongs[] = [
                            'spotify_id' => null,
                            'name' => $rec['track_name'],
                            'artists' => $rec['artists'],
                            'album' => $rec['album_name'] ?? null,
                            'image' => null,
                            'preview_url' => null,
                            'distance' => $rec['distance'],
                            'source' => 'ai'
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Spotify search failed', [
                        'query' => $searchQuery,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'mood' => $aiResponse['mood'] ?? null,
                'confidence' => $aiResponse['confidence'] ?? null,
                'count' => count($finalSongs),
                'data' => $finalSongs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Recommendation service unavailable',
                'message' => $e->getMessage()
            ], 503);
        }
    }
}
