<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScooterRequest;
use App\Http\Requests\UpdateScooterRequest;
use App\Http\Resources\ScooterResource;
use App\Models\Scooter;
use Illuminate\Http\JsonResponse;

class ScooterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $scooters = Scooter::all();

            return response()->json([
                'message' => 'Scooters retrieved successfully.',
                'data' => ScooterResource::collection($scooters),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while retrieving scooters.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScooterRequest $request): JsonResponse
    {
        try {
            $scooter = Scooter::create($request->validated());

            return response()->json([
                'message' => 'Scooter created successfully.',
                'data' => new ScooterResource($scooter),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while creating the scooter.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Scooter $scooter): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Scooter retrieved successfully.',
                'data' => new ScooterResource($scooter),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while retrieving the scooter.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScooterRequest $request, Scooter $scooter): JsonResponse
    {
        try {
            $scooter->update($request->validated());

            return response()->json([
                'message' => 'Scooter updated successfully.',
                'data' => $scooter,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while updating the scooter.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scooter $scooter): JsonResponse
    {
        try {
            $scooter->delete();

            return response()->json([
                'message' => 'Scooter deleted successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while deleting the scooter.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
