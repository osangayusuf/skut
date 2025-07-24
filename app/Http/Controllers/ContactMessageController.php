<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(): JsonResponse
    {
        try {
            $messages = ContactMessage::all();

            return response()->json([
                'message' => 'Contact messages retrieved successfully.',
                'data' => ContactMessageResource::collection($messages),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while retrieving contact messages.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created contact message.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $message = ContactMessage::create($validator->validated());

            return response()->json([
                'message' => 'Contact message created successfully.',
                'data' => new ContactMessageResource($message),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating contact message.',
                'error' => $th->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $message): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Contact message retrieved successfully.',
                'data' => new ContactMessageResource($message),
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while retrieving the contact message.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
