<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Validate the request data
            $validator = validator($request->all(), [
                'firstname' => 'sometimes|required|string|max:255',
                'lastname' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $user->id,
                'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update user profile
            $user->update($validator->validated());

            return response()->json([
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
