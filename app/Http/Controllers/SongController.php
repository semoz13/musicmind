<?php

namespace App\Http\Controllers;

use App\Http\Requests\SongFilterRequest;
use App\Services\SongService;
use Illuminate\Http\Request;

class SongController extends Controller
{
    protected $songService;

    public function __construct(SongService  $songService)
    {
        $this->songService = $songService;
    }

    public function index(SongFilterRequest $request)
    {
        $data = $request->validated();
        $songs = $this->songService->getSongsByIds($data['ids']);
        return apiResponse(true, 'songs retrived successfully', $songs);
    }
    
    public function search(Request $request)
    {
        $data =$request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $songs = $this->songService->searchSongs(
            $data['q'],
            $data['limit'] ?? 10
        );
        return apiResponse(true, 'song search result', $songs);

    }

}
