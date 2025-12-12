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
        $songs = $this->songService->apply_filters($data);
        return apiResponse(true, 'songs retrived successfully', $songs);
    }
    
}
