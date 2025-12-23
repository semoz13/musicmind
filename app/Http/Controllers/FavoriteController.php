<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use auth;
use Illuminate\Http\Request;
use App\Services\FavoriteService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    protected $favoriteService;

    protected $spotifyService;

    public function __construct(FavoriteService $favoriteService, SpotifyService $spotifyService)
    {
        $this->favoriteService = $favoriteService;
        $this->spotifyService = $spotifyService;

    }

    public function index(Request $request)
    {
        try {
            $favorite = $this->favoriteService->get_favorite_songs();
            if (! $favorite) {
                throw ValidationException::withMessages([
                    'favorite' => ['No favorite songs found'],
                ]);
            }
            $spotify_ids = $favorite->pluck('spotify_id')->toArray(); // get the IDS from favorite table
            // now we need to send these ids to spotify service to get song details

            $favoriteInfo = $this->spotifyService->getSongsByIds($spotify_ids);

            return apiResponse(true, 'data retrieved successfully', $favoriteInfo, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), [], 500);
        }
    }

    public function store(Request $request)
    {
        $data = ['spotify_id' => $request->spotify_id];
        $favorite = $this->favoriteService->addToFavorite($data);

        return apiResponse(true, 'created', $favorite, 201);
    }

    public function destroy(Request $request , string $spotifySongId)
    {
        try {
            $favorite = $this->favoriteService->remove_from_favorite($id);

            return apiResponse(true, 'deleted', $favorite);
        } catch (ValidationException $e) {
            return apiResponse(false, $e->getMessage(), [], 404);
        }
    }
}
