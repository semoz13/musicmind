<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongFilterRequest;
use App\Services\RecommendationsService;
use App\Services\SongService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class SongController extends Controller
{
    protected $songService;

    protected $spotifyService;

    public function __construct(SongService $songService, SpotifyService $spotifyService)
    {
        $this->songService = $songService;
        $this->spotifyService = $spotifyService;
    }

    public function index(SongFilterRequest $request)
    {
        $data = $request->validated();
        $songs = $this->songService->apply_filters($data);

        return apiResponse(true, 'songs retrived successfully', $songs);
    }

    public function getSongsByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        // Accept comma-separated string or array
        if (is_string($ids)) {
            $ids = array_filter(array_map('trim', explode(',', $ids)));
        }

        if (empty($ids)) {
            return apiResponse(false, 'No track ids provided. Use `ids` as comma-separated ids or array.', null, 422);
        }

        try {
            $songs = $this->spotifyService->getSongsByIds($ids);
            return apiResponse(true, 'Songs retrieved successfully', $songs);
        } catch (\RuntimeException $e) {
            return apiResponse(false, $e->getMessage(), null, 400);
        }
    }

    public function recommendByMood(
        Request $request ,
        RecommendationsService $recommendationsService,
        SongService $songService
    ){
        $data = $request->validate([
            'happines' => 'requird|numeric|min:0|max:10',
            'sadness' => 'requird|numeric|min:0|max:10',
            'energy' => 'requird|numeric|min:0|max:10',
            'calmness' => 'requird|numeric|min:0|max:10',
            'danceability' => 'requird|numeric|min:0|max:10',
            'tempo' => 'requird|numeric|min:0|max:10',
        ]);

        $modelResults = $recommendationsService->recommend($data);

        $finalSongs=[];

        foreach ($modelResults as $song){
            $query = $song['track_name'] . '' . $song['artists'];
            $spotifyResult = $songService->searchSongs($query, 1);
            if(!empty($spotifyResult['tracks']['items'][0])){
                $finalSongs[] = [
                    'model_data'=>$song,
                    'spotify_data'=>$spotifyResult['tracks']['items'][0],
                ];
            }
        }
        return response()->json([
            'recommendations' => $finalSongs
        ]);
    }

    
}
