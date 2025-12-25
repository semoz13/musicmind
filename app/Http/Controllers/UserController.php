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
        $user = $this->userService->updateProfile($request->validated());

        return apiResponse(true, 'Profile updated successfully', [
            'user' => new UserResource($user),
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
