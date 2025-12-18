<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SongController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('songs/index', [SongController::class, 'index']);

    Route::get('favorites/index', [FavoriteController::class ,'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{spotify_song_id}', [FavoriteController::class, 'destroy']);

    Route::get('/artist', [ArtistController::class, 'show']);

    Route::get('/test-spotify', function() {
        $spotifyService = new App\Services\SpotifyService();
        return response()->json($spotifyService->testConnection());
    });
});
