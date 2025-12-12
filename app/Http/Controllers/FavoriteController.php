<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;
use App\Http\Requests\StoreFavoriteRequest;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    protected $favoriteService;
    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        
    }

    public function index()
    {
        try {
            $favorite = $this->favoriteService->get_favorite_songs();
            return apiResponse(true, 'data retrieved successfully', $favorite, 200);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), [], 500);
        }
    }

    public function store(StoreFavoriteRequest $request)
    {
        $data = $request->validated();
        $favorite = $this->favoriteService->store($data);
        return apiResponse(true , 'created' , $favorite , 201);
    }

    public function destroy(string $id)
    {
        try{
            $favorite = $this->favoriteService->remove_from_favorite($id);
            return apiResponse(true , 'deleted' , $favorite);
        } catch (ValidationException $e) {
            return apiResponse(false , $e->getMessage() , [] , 404);
        }
    }
}
