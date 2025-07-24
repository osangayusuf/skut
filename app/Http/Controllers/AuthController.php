<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::create([
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => bcrypt($request->input('password')),
                'user_type' => 'user',
            ]);

            return response()->json([
                'message' => 'User registered successfully.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred during registration.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Login a user and return token.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $credentials = $request->only('email', 'password');
            if (! auth()->attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            $user = auth()->user();

            return response()->json([
                'message' => 'Login successful.',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred during login.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): JsonResponse
    {
        try {
            auth()->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout successful.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred during logout.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(): JsonResponse
    {
        try {
            $user = auth()->user();
            if (! $user) {
                return response()->json([
                    'message' => 'User not authenticated.',
                ], 401);
            }

            return response()->json([
                'data' => new UserResource($user),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while fetching the profile.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
