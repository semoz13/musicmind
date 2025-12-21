<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongFilterRequest;
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
}
