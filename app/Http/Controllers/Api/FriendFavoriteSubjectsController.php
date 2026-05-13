<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FriendFavoriteSubjectsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $url = config('services.friend_subjects.url');

        if (! is_string($url) || $url === '') {
            return response()->json([
                'success' => false,
                'message' => 'Sõbra API aadress puudub — sea env muutuja FRIEND_SUBJECTS_API_URL.',
                'count' => 0,
                'data' => [],
            ], 503);
        }

        try {
            $response = Http::timeout(25)
                ->acceptJson()
                ->get($url, $request->query());

            $response->throw();

            /** @var array<string, mixed> $payload */
            $payload = $response->json();
            $rows = $payload['data'] ?? [];

            if (! is_array($rows)) {
                $rows = [];
            }

            $normalized = collect($rows)
                ->map(fn (mixed $row, int $idx) => $this->normalize(is_array($row) ? $row : [], $idx))
                ->values();

            return response()->json([
                'success' => (bool) ($payload['success'] ?? true),
                'count' => $normalized->count(),
                'data' => $normalized,
                'upstream' => $url,
            ]);
        } catch (RequestException) {
            return response()->json([
                'success' => false,
                'message' => 'Sõbra API vastust ei saadud (HTTP viga).',
                'count' => 0,
                'data' => [],
            ], 502);
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'Sõbra API töötlemisel tekkis viga.',
                'count' => 0,
                'data' => [],
            ], 502);
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalize(array $row, int $idx): array
    {
        $fid = $row['id'] ?? ($idx + 1);

        // Sõbra demo kujul film (director / genre / release_year) → NFL kaardi väljad
        if (isset($row['director']) || isset($row['release_year'])) {
            $ratingRaw = $row['rating'] ?? null;
            $ratingNum = is_numeric($ratingRaw) ? (float) $ratingRaw : null;
            $ratingStr = $ratingNum !== null
                ? number_format($ratingNum, 1)
                : ($ratingRaw !== null && $ratingRaw !== '' ? (string) $ratingRaw : number_format(7.4 + ($idx % 6) * 0.15, 1));

            return [
                'id' => 'friend-'.$fid,
                'title' => (string) ($row['title'] ?? ''),
                'team' => (string) ($row['director'] ?? ''),
                'position' => (string) ($row['genre'] ?? ''),
                'season_year' => (int) ($row['release_year'] ?? date('Y')),
                'draft_round' => isset($row['draft_round'])
                    ? (int) $row['draft_round']
                    : ($ratingNum !== null ? max(1, min(7, (int) round(12 - $ratingNum))) : 3),
                'image' => $row['image'] ?? null,
                'description' => (string) ($row['description'] ?? ''),
                'rating' => $ratingStr,
            ];
        }

        $draftRound = isset($row['draft_round']) ? (int) $row['draft_round'] : 4;
        $ratingStr = isset($row['rating']) && $row['rating'] !== ''
            ? (is_numeric($row['rating']) ? number_format((float) $row['rating'], 1) : (string) $row['rating'])
            : number_format(max(6.0, min(9.9, 9.2 - ($draftRound - 1) * 0.42)), 1);

        return [
            'id' => 'friend-'.$fid,
            'title' => (string) ($row['title'] ?? ''),
            'team' => (string) ($row['team'] ?? ''),
            'position' => (string) ($row['position'] ?? ''),
            'season_year' => (int) ($row['season_year'] ?? date('Y')),
            'draft_round' => $draftRound,
            'image' => $row['image'] ?? null,
            'description' => (string) ($row['description'] ?? ''),
            'rating' => $ratingStr,
        ];
    }
}
