<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{///////////////////////////////////////////ADD TRY CATCH PELASE ////////////////////////////////////
    public function register(Request $request)
    {
      //you can simply validate the request data like this
      $validatedData = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create($validatedData);
    $token = $user->createToken('auth_token')->plainTextToken;

    // the format of the return should be uniform across all laravel controllers
    // it should return [status, message, data]
    //I prefer to make helper functions for this purpose but for now let's keep it simple
    
    return response()->json([
        'status' => 'success',
        'message' => 'User registered successfully',
        'data' => [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
    ], 201);
}

public function login(Request $request)
{
    // Validate input
    $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // You can simplify user retrieval and password check using Auth::attempt
    // Reminder: add try/catch if needed for more detailed error handling
    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Auth::attempt returns boolean, so get the authenticated user explicitly
    $user = Auth::user();

    // Create token for API authentication
    $token = $user->createToken('auth_token')->plainTextToken;
// if u notice I kept the response format same as register function for consistency
// for better practice u can make a helper function to handle such responses
    return response()->json([
        'status' => 'success',
        'message' => 'User logged in successfully',
        'data' => [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],
    ]);
}


public function logout(Request $request)
{
       // Ensure the user is authenticated and a token exists
        $user = $request->user();
        $token = $user?->currentAccessToken();

        if (!$user || !$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active session found.',
            ], 401);
        }

        // Delete the current API token only (safe logout)
        $token->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
}
}

