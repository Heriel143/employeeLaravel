<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handles user login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate the login request data
        $credentials = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        // Find the user by email
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            // Return error response if credentials are invalid
            return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
        }

        // Generate a token for the authenticated user
        $token = $user->createToken($user->email . "auth_token")->plainTextToken;

        // Return success response with the generated token
        return response()->json([
            'token' => $token,
            'message' => 'Login successful',
        ], 200);
    }

    /**
     * Handles user registration.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        // Validate the registration request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255'
        ]);

        // Create a new user with the validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Hash the password before saving it to the database
            'password' => Hash::make($request->password)
        ]);

        // Check if user was created successfully
        if ($user != null) {
            // Generate a token for the newly registered user
            $token = $user->createToken($user->email . "auth_token")->plainTextToken;

            // Return success response with the generated token
            return response()->json([
                'token' => $token,
                'message' => 'Registration successful',
            ], 201);
        } else {
            // Return error response if registration failed
            return response()->json([
                'message' => 'Registration failed',
            ], 400);
        }
    }

    /**
     * Handles user logout.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the current access token of the authenticated user
        $request->user()->currentAccessToken()->delete();

        // Return success response after logout
        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
