<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    /**
     * Subscribe a user to the newsletter.
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'email' => 'required|email|max:255|unique:newsletter_subscribers,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $subscriber = NewsletterSubscriber::create($validator->validated());

            return response()->json([
                'message' => 'You have successfully subscribed to our newsletter.',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while subscribing.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Unsubscribe a user from the newsletter.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        try {
            $validator = validator($request->all(), [
                'email' => 'required|email|max:255|exists:newsletter_subscribers,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $subscriber = NewsletterSubscriber::where('email', $request->email)->first();
            $subscriber->delete();

            return response()->json([
                'message' => 'You have successfully unsubscribed from our newsletter.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while unsubscribing.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
