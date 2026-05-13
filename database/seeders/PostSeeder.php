<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * NFL-teemalised avaldatud postitused blogisse.
     * Kui avaldatud postitusi juba on, seeder jäetakse vahele (reaalse keskkonna ohutus).
     * Uute näidiste jaoks: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        if (Post::query()->where('published', true)->exists()) {
            return;
        }

        $authors = Author::query()->orderBy('id')->get();
        if ($authors->isEmpty()) {
            $this->command?->warn('PostSeeder: autorid puuduvad. Käivita enne AuthorSeeder.');

            return;
        }

        $entries = [
            [
                'title' => 'NFL draft: miks rookie hooaeg muudab tiimi dünaamikat?',
                'description' => "Igal aastal täidavad esimese ringi valitud mängijad kohe nähtava rolli — eriti kui tiimil oli vaja QB või esiründeliini tugevdust.\n\nScoutide töö kombineerib kolledži tape'i, mõõtmisi ja intervjuusid; vigade hindamine tapab sageli ka väga õnnestunud draft'i.\n\nFantasy mängijatele on oluline jälgida training camp'i depth chart'e ja vigastuste uudiseid — need mõjutavad snap count'e kohe augustis.",
            ],
            [
                'title' => 'Red zone ja EPA: lihtsad mõisted fantasy jaoks',
                'description' => "Red zone visiidid annavad TD tõenäosusest parema ettekujutuse kui ainult yard'id.\n\nEPA (expected points added) näitab, kas mänguloeng tõstis või vähendas tiimi võimalust punkte skoorida.\n\nKui vaatad Sunday tikkerit, tasub võrrelda QB pocket time'i ja WR separation'i — need kaks käivad sageli koos.",
            ],
            [
                'title' => 'Miks NFL kasutab otsustamiseks rohkem videot kui mõni teine liiga?',
                'description' => "Challenge süsteem ja automaatne ülevaatus muutsid mängu tempo ja treenerite riskivalikut.\n\nMõned situatsioonid (nt runner'i progress) jäävad ikka kohtuniku human-error külge — see tekitab aastakümnetega polariseeruva arutelu.\n\nKui jälgid EM ajavööndis prime time'i, planeeri vähemalt kolme tunni aken — media timeout'id venitavad reaalajas kestvust.",
            ],
            [
                'title' => "Kaitseliini stunt'id ja sack'i eeldused",
                'description' => "Nelja mehe rush'is tähendab stunt tihti seda, et DT pistab väliselt ja EDGE tuleb sisemaalt.\n\nQB timing sõltub drop sügavusest: kiire release vähendab sack riski, aga piirab sügavaid marsruute.\n\nKui vaatad defense snap count'e, näed sageli situatsioonilist rotation'i — short yardage pakub teistsugust personnel'i kui third-and-long.",
            ],
            [
                'title' => 'Special teams: miks field goal blokkid ei ole loterii',
                'description' => "Kick block üksused töötavad release timing'u ja offensive line käte asetuse analüüsi najal.\n\nTuule ja müra mõju domeeni vs väliväljakul on märgatav ka statistikates.\n\nKohtunike uued taotlused timing penalty kohta panid mõned tiimid muutma oma cadence'i.",
            ],
            [
                'title' => 'Konverentside tasakaal ja wildcard nädal',
                'description' => "AFC ja NFC playoff joonised sõltuvad division võitudest — wildcard võib tuua rematch'e koheste rivaalide vahel.\n\nSeed'id määravad koduväljaku; üks nädal ei anna järgmist koduetappi automaatselt.\n\nBlogis võrdleme hooaja teisel poolel DVOA-laadseid võrdlusi lihtsate keskmiste yard'idega — keskmine võib eksitada.",
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
