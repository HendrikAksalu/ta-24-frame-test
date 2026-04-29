<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MyFavoriteSubject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NflRookieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'team' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'in:title,team,position,draft_round,season_year'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'season_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $seasonYear = $validated['season_year'] ?? now()->year;
        $sortBy = $validated['sort_by'] ?? 'draft_round';
        $direction = $validated['direction'] ?? 'asc';
        $limit = $validated['limit'] ?? 20;

        $cacheVersionKey = 'nfl_rookies_cache_version';
        $cacheVersion = (int) Cache::get($cacheVersionKey, 1);

        $params = [
            'search' => $validated['search'] ?? null,
            'team' => $validated['team'] ?? null,
            'position' => $validated['position'] ?? null,
            'sort_by' => $sortBy,
            'direction' => $direction,
            'limit' => $limit,
            'season_year' => $seasonYear,
        ];
        $hash = md5(json_encode($params, JSON_UNESCAPED_SLASHES));
        $cacheKey = "nfl_rookies:list:v{$cacheVersion}:{$hash}";

        $subjects = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($validated, $seasonYear, $sortBy, $direction, $limit) {
            $query = MyFavoriteSubject::query()->where('season_year', $seasonYear);

            if (!empty($validated['search'])) {
                $query->where('title', 'like', '%' . $validated['search'] . '%');
            }

            if (!empty($validated['team'])) {
                $query->where('team', $validated['team']);
            }

            if (!empty($validated['position'])) {
                $query->where('position', $validated['position']);
            }

            return $query->orderBy($sortBy, $direction)->limit($limit)->get();
        });

        return response()->json([
            'success' => true,
            'count' => $subjects->count(),
            'data' => $subjects,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $subject = MyFavoriteSubject::query()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'string', 'max:2048'],
            'description' => ['required', 'string', 'max:10000'],
            'team' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'draft_round' => ['required', 'integer', 'min:1'],
            'season_year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $subject = MyFavoriteSubject::query()->create($validated);
        $this->bumpCacheVersion();

        return response()->json([
            'success' => true,
            'data' => $subject,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $subject = MyFavoriteSubject::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:2048'],
            'description' => ['required', 'string', 'max:10000'],
            'team' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'draft_round' => ['required', 'integer', 'min:1'],
            'season_year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $subject->update($validated);
        $this->bumpCacheVersion();

        return response()->json([
            'success' => true,
            'data' => $subject->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = MyFavoriteSubject::query()->findOrFail($id);
        $subject->delete();
        $this->bumpCacheVersion();

        return response()->json(null, 204);
    }

    private function bumpCacheVersion(): void
    {
        $cacheVersionKey = 'nfl_rookies_cache_version';
        $current = (int) Cache::get($cacheVersionKey, 1);

        Cache::put($cacheVersionKey, $current + 1, now()->addDays(30));
    }
}

