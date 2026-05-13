<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Seeder;

/**
 * Kustutab kõik blogi postitused ja kommentaarid, siis loob uuesti NFL näidissisu.
 * Käivita: php artisan db:seed --class=NflBlogReseedSeeder
 */
class NflBlogReseedSeeder extends Seeder
{
    public function run(): void
    {
        Comment::query()->delete();
        Post::query()->delete();

        $this->call([
            AuthorSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
