<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;

class ArtistController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function show($id)
    {
        try {
            $artist = $this->spotifyService->getArtist($id);

            return response()->json($artist);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // public function search(Request $request)
    // {
    //   $query = $request->input('q');

    //   try {
    //     $artists = $this->spotifyService->searchArtists($query);
    //     return response()->json($artists);
    //   } catch (\Exception $e) {
    //     return response()->json(['error' => $e->getMessage()], 400);
    //   }
    // }

    // public function getTopTracks($id)
    // {
    //   try {
    //     $tracks = $this->spotifyService->getArtistTopTracks($id);
    //     return response()->json($tracks);
    //   } catch (\Exception $e) {
    //     return response()->json(['error' => $e->getMessage()], 400);
    //   }
    // }
}
