<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Kaks NFL-teemalist kommentaari igale postitusele, kus kommentaare veel pole.
     */
    public function run(): void
    {
        $pairsByIndex = [
            [
                ['author_name' => 'Tailgate_Tõnu', 'content' => 'Hea ülevaade! Kas järgmises postituses võtaksid ette salary cap dead money’ga seotud põhimõtted?'],
                ['author_name' => 'RB_rotation', 'content' => 'Rookie RB snap count on fantasy jaoks ülioluline — ootan depth chart’i uuendusi training camp’ist.'],
            ],
            [
                ['author_name' => 'EPA_huviline', 'content' => 'EPA selgitus aitas lõpuks aru saada, miks mõni drive „vaikselt“ hästi lõppes.'],
                ['author_name' => 'Fantasy_Eesti', 'content' => 'Kas red zone target share’i võiks järgmine kord graafikuna näidata?'],
            ],
            [
                ['author_name' => 'PrimeTime_Mari', 'content' => 'Tõsi — mu kolleeg vaatab ainult box score’t ja imestab, miks mäng nii pikk oli.'],
                ['author_name' => 'Replay_Fan', 'content' => 'Challenge reeglid muutuvad peaaegu igal aastal; keep us posted kui midagi uut tuleb.'],
            ],
            [
                ['author_name' => 'DLCoachLite', 'content' => 'Stunt selgitus oli konkreetne. Oleks huvitav näha ühte kaameranurka, mis näitab OT käte asendit.'],
                ['author_name' => 'QB_pressure', 'content' => 'Pocket collapse time vs sack rate — kas sul on mõni link lisalelugudele?'],
            ],
            [
                ['author_name' => 'SpecialTeamsGeek', 'content' => 'FG blokkid pole loterii — nõustun täielikult timing release üle.'],
                ['author_name' => 'Tuulemees', 'content' => 'Kodus ja väljas väljaku võrdlus oli tabav. Loeks veel crosswind kohta.'],
            ],
            [
                ['author_name' => 'Wildcard_Watcher', 'content' => 'Wildcard nädala seed’ide mõju koduväljakule tasuks igal aastal meelde tuletada.'],
                ['author_name' => 'PowerRankSkeptic', 'content' => 'Keskmiste yard’ide võrgu alt tõstmine on mõttekas — paljud ajakirjanikud unustavad selle ära.'],
            ],
        ];

        $posts = Post::query()->orderBy('id')->get();
        foreach ($posts->values() as $index => $post) {
            if ($post->comments()->exists()) {
                continue;
            }

            $pair = $pairsByIndex[$index % count($pairsByIndex)];
            foreach ($pair as $row) {
                Comment::query()->create([
                    'post_id' => $post->id,
                    'author_name' => $row['author_name'],
                    'content' => $row['content'],
                ]);
            }
        }
    }
}
