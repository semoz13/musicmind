<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class AuthController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {

        try {
            $validatedData = $request->validated();
            $info = $this->userService->register($validatedData);
            $info['user'] = new UserResource($info['user']);

            return apiResponse(true, 'user registered successfully', $info, 201);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), 500);
        }

    }

    public function login(LoginRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $info = $this->userService->login($validatedData);
            if (isset($info['error'])) {
                return apiResponse(false, $info['error'], null, 401);
            }

            return apiResponse(true, 'logged in successfully', [
                'user' => new UserResource($info['user']),
                'token' => $info['token'],
            ]);
        } catch (\Exception $e) {
            return apiResponse(false, $e->getMessage(), 500);
        }

    }

    public function logout()
    {
        $done = $this->userService->logout();

        if (! $done) {
            return apiResponse(false, 'No active session found', null, 401);
        }

        return apiResponse(true, 'Logged out successfully');
    }
}
