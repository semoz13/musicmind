<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/profile', [UserController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->post('/change-password', [UserController::class, 'changePassword']);


// Auth routes
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/songs/index', [SongController::class, 'index']);
    Route::get('/songs/search', [SongController::class, 'search']);
    Route::post('/songs', [SongController::class, 'getSongsByIds']);

  
    Route::post('favorites/add-to-favorite', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{spotify_song_id}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/index', [FavoriteController::class, 'index']);
    Route::get('/favorites/filter-by-mood', [FavoriteController::class, 'filterByMood']);
    
    Route::get('/artist', [ArtistController::class, 'getArtists']);
    Route::get('/artist/search', [ArtistController::class, 'search']);
    Route::get('/artists/{id}/songs', [ArtistController::class, 'topTracks']);
    Route::get('/artists/{id}/albums', [ArtistController::class, 'albums']);
    Route::get('/artist/{id}', [ArtistController::class, 'show']);


    Route::post('/songs/recommend-by-mood', [SongController::class, 'recommendByMood']);

    
    Route::get('/test-spotify', function() {
        $spotifyService = new App\Services\SpotifyService();
        return response()->json($spotifyService->testConnection());
    });

    //recommendation model 
    Route::post('/recommendation', [RecommendationController::class, 'recommend']);

});
