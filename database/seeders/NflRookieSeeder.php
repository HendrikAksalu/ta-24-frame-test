<?php

namespace Database\Seeders;

use App\Models\MyFavoriteSubject;
use Illuminate\Database\Seeder;

class NflRookieSeeder extends Seeder
{
    public function run(): void
    {
        // Simple, deterministic seed for the UI + API testing.
        $seasonYear = 2026;

        $entries = [
            [
                'title' => 'Ethan Caldwell',
                'image' => 'https://placehold.co/600x400/png?text=Ethan+Caldwell',
                'description' => 'Kiire release-iga algtaseme CB; hea taganemiskontroll ja kvaliteetne manoverduks.',
                'team' => 'Tennessee Titans',
                'position' => 'CB',
                'draft_round' => 2,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Mason Wright',
                'image' => 'https://placehold.co/600x400/png?text=Mason+Wright',
                'description' => 'Pocket movement + õige ajastus; sidus sööte ja rünnakutempo kontroll.',
                'team' => 'Indianapolis Colts',
                'position' => 'QB',
                'draft_round' => 1,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Jalen Harper',
                'image' => 'https://placehold.co/600x400/png?text=Jalen+Harper',
                'description' => 'Kontrollitud jooks, kontaktijärgne kiirus ja läbimurde risk.',
                'team' => 'Dallas Cowboys',
                'position' => 'RB',
                'draft_round' => 3,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Noah Kim',
                'image' => 'https://placehold.co/600x400/png?text=Noah+Kim',
                'description' => 'Stabiilne jalatoe pass-protectionis; jarjepidev ingliseplokk run-game\'is.',
                'team' => 'San Francisco 49ers',
                'position' => 'OT',
                'draft_round' => 2,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Oscar Bennett',
                'image' => 'https://placehold.co/600x400/png?text=Oscar+Bennett',
                'description' => 'Mitmekulgne TE: kindel catch-radius ja toe red-zone\'is.',
                'team' => 'Baltimore Ravens',
                'position' => 'TE',
                'draft_round' => 4,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Rafael Torres',
                'image' => 'https://placehold.co/600x400/png?text=Rafael+Torres',
                'description' => 'Edge-rusheri rütm: variatsioonid sain-/spin-manuvritega; pressurida suudab mitmest nurgast.',
                'team' => 'New York Giants',
                'position' => 'DE',
                'draft_round' => 1,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Logan Price',
                'image' => 'https://placehold.co/600x400/png?text=Logan+Price',
                'description' => 'Puhas short-area route runner; voidab coverage\'ist isolatsiooni ja toodab separatsiooni.',
                'team' => 'Miami Dolphins',
                'position' => 'WR',
                'draft_round' => 2,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Tariq Johnson',
                'image' => 'https://placehold.co/600x400/png?text=Tariq+Johnson',
                'description' => 'Interception instincts: kiire loetavus ja ball-hawking; stabiilne footwork pass defense’is.',
                'team' => 'Green Bay Packers',
                'position' => 'S',
                'draft_round' => 5,
                'season_year' => $seasonYear,
            ],
            [
                'title' => 'Victor Allen',
                'image' => 'https://placehold.co/600x400/png?text=Victor+Allen',
                'description' => 'Inside LB: sidus run-stop, kiire flow ja tsoonide katmine lühikestes ruumides.',
                'team' => 'Chicago Bears',
                'position' => 'ILB',
                'draft_round' => 3,
                'season_year' => $seasonYear,
            ],
        ];

        foreach ($entries as $entry) {
            MyFavoriteSubject::query()->updateOrCreate(
                [
                    'title' => $entry['title'],
                    'team' => $entry['team'],
                    'season_year' => $entry['season_year'],
                ],
                $entry,
            );
        }
    }
}

