<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Curated published posts for the public /blog feed.
     * Skips if there is already at least one published post (safe for production re-runs).
     */
    public function run(): void
    {
        if (Post::query()->where('published', true)->exists()) {
            return;
        }

        $authors = Author::query()->orderBy('id')->get();
        if ($authors->isEmpty()) {
            $this->command?->warn('PostSeeder: no authors found. Run AuthorSeeder first.');

            return;
        }

        $entries = [
            [
                'title' => 'Tere tulemast meie blogisse',
                'description' => "See on näidispostitus, mis ilmub avalikus blogis (/blog).\n\nSiin saad kirjeldada projekti eesmärki, kasutatud tehnoloogiaid (Laravel, Inertia, Vue) ja linkida dokumentatsiooni.",
            ],
            [
                'title' => 'Kuidas ilmateenust dashboardil kasutada',
                'description' => "Töölaud kuvab OpenWeatherMapi andmeid valitud linna kohta.\n\nVaikimisi on Kuressaare; saad otsida teisi linnu välja kasti kaudu. Andmed on vahemälus, et API limiit ei kukuks liiga kiiresti täis.",
            ],
            [
                'title' => 'Kaardimarkerid ja OpenStreetMap',
                'description' => "Markerid salvestatakse andmebaasi ja kuvatakse Leafleti kaardil.\n\nKlõpsa kaardil, et lisada uus punkt; paremal nimekirjas saad muuta või kustutada.",
            ],
            [
                'title' => 'Kommentaarid ja modereerimine',
                'description' => "Avalik blogi võimaldab külalistel kommenteerida (nõutav on nimi).\n\nSisse loginud administraator (test@example.com) saab kommentaare kustutada.",
            ],
            [
                'title' => 'Deploy ja migratsioonid',
                'description' => "Tootmises käivitab Deployer tavaliselt `php artisan migrate`.\n\nKui blog on tühi, saad üks kord käivitada `php artisan db:seed` või ainult blogi sisu seederi.",
            ],
            [
                'title' => 'API võtmed ja .env',
                'description' => "Ära commiti `.env` faili ega salajasi võtmeid.\n\nIlmateenuse jaoks kasuta `WEATHER_API` (OpenWeatherMap), Google OAuth jaoks vastavaid `GOOGLE_*` muutujaid.",
            ],
            [
                'title' => 'Autorid ja postituste haldus',
                'description' => "Sisse logides leiad jaotise Posts, kus saad luua mustandeid või avaldada tekste.\n\nAvalik blogi näitab ainult `published` linnukestega postitusi.",
            ],
            [
                'title' => 'Turvalisus: CSRF ja throttle',
                'description' => "Vormid kasutavad CSRF kaitset.\n\nAvaliku blogi kommentaaride teed kaitseb throttle, et vähendada rämpsposti riski.",
            ],
            [
                'title' => 'Vue + Inertia: miks mitte klassikaline Blade?',
                'description' => 'Inertia ühendab Laraveli marsruudid ja Vue komponendid ilma eralitava SPA API-ta, mida sa vajad ainult siis, kui ehitad mobiilirakendust või välist klienti.',
            ],
            [
                'title' => 'Andmebaas: SQLite vs MySQL',
                'description' => "Arenduses sobib SQLite kiiresti käima.\n\nTootmises on sageli MySQL/MariaDB; veendu, et migratsioonid on samad mõlemas keskkonnas.",
            ],
            [
                'title' => 'Testkasutaja ja rollid',
                'description' => "Seeder loob kasutaja test@example.com (parool vastavalt DatabaseSeederile).\n\nTulevikus tasub asendada päris rollipõhine õiguste süsteem (nt spatie/laravel-permission).",
            ],
            [
                'title' => 'Järgmised sammud projektis',
                'description' => "Võid täiendada e-poodi, makseid (Stripe), oma JSON API-d või lemmikteema tabelit.\n\nHoia commitid väikesed ja kirjeldused selged — see aitab hindajal aru saada.",
            ],
        ];

        $i = 0;
        foreach ($entries as $entry) {
            $author = $authors[$i % $authors->count()];
            $i++;

            Post::query()->create([
                'title' => $entry['title'],
                'description' => $entry['description'],
                'content' => $entry['description'],
                'author_id' => $author->id,
                'published' => true,
            ]);
        }
    }
}
