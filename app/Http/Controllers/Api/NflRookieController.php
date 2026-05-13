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
            'director' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', 'max:32'],
            'sort' => ['nullable', 'string', 'max:32'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'season_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $teamFilter = $validated['team'] ?? $validated['director'] ?? null;
        $positionFilter = $validated['position'] ?? $validated['genre'] ?? null;
        $seasonYear = $validated['season_year'] ?? $validated['year'] ?? (int) now()->year;

        $sortInput = $validated['sort_by'] ?? $validated['sort'] ?? 'created_at';
        $sortAliases = [
            'release_year' => 'season_year',
            'director' => 'team',
            'genre' => 'position',
            'rating' => 'draft_round',
        ];
        $sortBy = $sortAliases[$sortInput] ?? $sortInput;

        $allowedSorts = ['title', 'team', 'position', 'draft_round', 'season_year', 'created_at'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $direction = $validated['direction'] ?? ($sortBy === 'created_at' ? 'desc' : 'asc');
        $limit = $validated['limit'] ?? 20;

        $cacheVersionKey = 'nfl_rookies_cache_version';
        $cacheVersion = (int) Cache::get($cacheVersionKey, 1);

        $params = [
            'search' => $validated['search'] ?? null,
            'team' => $teamFilter,
            'position' => $positionFilter,
            'sort_by' => $sortBy,
            'direction' => $direction,
            'limit' => $limit,
            'season_year' => $seasonYear,
        ];
        $hash = md5(json_encode($params, JSON_UNESCAPED_SLASHES));
        $cacheKey = "nfl_rookies:list:v{$cacheVersion}:{$hash}";

        $subjects = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($validated, $seasonYear, $sortBy, $direction, $limit, $teamFilter, $positionFilter) {
            $query = MyFavoriteSubject::query()->where('season_year', $seasonYear);

            if (! empty($validated['search'])) {
                $query->where('title', 'like', '%'.$validated['search'].'%');
            }

            if (! empty($teamFilter)) {
                $query->where('team', 'like', '%'.$teamFilter.'%');
            }

            if (! empty($positionFilter)) {
                $query->where('position', $positionFilter);
            }

            return $query->orderBy($sortBy, $direction)->limit($limit)->get();
        });

        $data = $subjects->map(fn (MyFavoriteSubject $s) => array_merge($s->toArray(), [
            'rating' => $this->formatRating($s),
        ]))->values();

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $subject = MyFavoriteSubject::query()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => array_merge($subject->toArray(), [
                'rating' => $this->formatRating($subject),
            ]),
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
            'data' => array_merge($subject->toArray(), [
                'rating' => $this->formatRating($subject),
            ]),
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

        $fresh = $subject->fresh();

        return response()->json([
            'success' => true,
            'data' => array_merge($fresh->toArray(), [
                'rating' => $this->formatRating($fresh),
            ]),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = MyFavoriteSubject::query()->findOrFail($id);
        $subject->delete();
        $this->bumpCacheVersion();

        return response()->json(null, 204);
    }

    private function formatRating(MyFavoriteSubject $subject): string
    {
        $v = 9.2 - ($subject->draft_round - 1) * 0.42;

        return number_format(max(6.0, min(9.9, $v)), 1);
    }

    private function bumpCacheVersion(): void
    {
        $cacheVersionKey = 'nfl_rookies_cache_version';
        $current = (int) Cache::get($cacheVersionKey, 1);

        Cache::put($cacheVersionKey, $current + 1, now()->addDays(30));
    }
}
