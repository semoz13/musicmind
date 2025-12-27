<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Http\Requests\UpdateProfileRequest;


class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    )
    {
        
    }


    public function updateProfile(UpdateProfileRequest $request)
    {
    try {
        if (!$request->hasFile('avatar')) {
            return response()->json([
                'error' => 'No file received'
            ], 400);
        }
    
        $path = $request->file('avatar')->store('avatars', 'public');
    
        return response()->json([
            'saved' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    } catch (\Exception $e) {
        return apiResponse(false, $e->getMessage(), 500);
    }
    }


    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->userService->changePassword($request->validated());
        if (isset($result['error'])){
            return apiResponse(false, $result['error'], null, 400);
        }
        return apiResponse(true, 'password changed successfully. please login again.');
    }

}
