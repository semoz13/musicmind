<?php

namespace App\Http\Controllers;

use App\Services\ArtistService;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class ArtistController extends Controller
{

    public function __construct(
        protected SpotifyService $spotifyService,
        protected ArtistService $artistService )
    {
        
    }

    public function getArtists(Request $request)
    {
       $validated = $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'string',
       ]);
       $artists = $this->artistService->getArtistsByIds($validated['ids']);
       return apiResponse(true, 'Artist retrived successfully', $artists);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);
        $artists = $this->artistService->searchArtists(
            $validated['q'],
            $validated['limit'] ?? 10
        );

        return apiResponse(true, 'Artist search results', $artists);

    }

    public function topTracks(string $id)
    {
        $tracks = $this->artistService->getArtistTopTracks($id);
        
        return apiResponse(true, 'artist top tracks', $tracks);
    }

    public function albums(Request $request, string $artistId)
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:50',
            'offset' => 'nullable|integer|min:0'
        ]);
        $albums = $this->artistService->getArtistAlbums(
            $artistId,
            $validated['limit'] ?? 20,
            $validated['offset'] ?? 0
        );
        return apiResponse(true, 'artist albums retrived successfully', $albums);
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
