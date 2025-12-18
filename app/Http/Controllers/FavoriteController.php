<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteStoreRequest;
use App\Models\Favorite;
use auth;
use Illuminate\Http\Request;
use App\Services\SpotifyService;
use App\Services\FavoriteService;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    //protected $favoriteService;
    public function __construct(
        protected FavoriteService $favoriteService,
        protected SpotifyService $spotifyService
        ){
        
    }

    public function index(Request $request)
    {

            $user = $request->user();
            
            if (!$user){
                return apiResponse(false, 'unauthenticated', [], 401);
            }

            $spotifyIds = $this->favoriteService->getUserFavoriteIds($user->id);
            $songs = $this->spotifyService->getSongsByIds($spotifyIds);
            return apiResponse(true, 'favorites retrieved successfully', $songs, 200);

    }

    public function store(FavoriteStoreRequest $request)
    {
        $this->favoriteService->addToFavorites(
            $request->user()->id,
            $request->spotify_song_id
        );
        return apiResponse(true , 'song added to favorites' , [] , 201);
    }

    public function destroy(Request $request , string $spotifySongId)
    {
        $user = $request->user();
            
        if (!$user){
                return apiResponse(false, 'unauthenticated', [], 401);
            }
            $deleted = $this->favoriteService->removeFromFavorite(
                $user->id,
                $spotifySongId
            );         

            if (!$deleted){
                return apiResponse(false,'favorite not found',[],404);
            }

            return apiResponse(true ,'song removed from favorites');
        
    }
}
