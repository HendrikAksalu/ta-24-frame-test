<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Marker::query()->orderBy('name')->get()
        );
    }

    public function show(Marker $marker): JsonResponse
    {
        return response()->json($marker);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|max:10000',
        ]);

        $marker = Marker::query()->create($data);

        return response()->json($marker, 201);
    }

    public function update(Request $request, Marker $marker): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string|max:10000',
        ]);

        $marker->update($data);

        return response()->json($marker->fresh());
    }

    public function destroy(Marker $marker): JsonResponse
    {
        $marker->delete();

        return response()->json(null, 204);
    }
}
