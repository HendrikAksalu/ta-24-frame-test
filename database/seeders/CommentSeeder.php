<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Adds a couple of comments per post that still have none (idempotent).
     */
    public function run(): void
    {
        $samples = [
            ['author_name' => 'Lugeja', 'content' => 'Tänu hea ülevaate eest!'],
            ['author_name' => 'Mari', 'content' => 'Kas saaksid järgmises postituses koodi näidet lisada?'],
        ];

        foreach (Post::query()->cursor() as $post) {
            if ($post->comments()->exists()) {
                continue;
            }

            foreach ($samples as $sample) {
                Comment::query()->create([
                    'post_id' => $post->id,
                    'author_name' => $sample['author_name'],
                    'content' => $sample['content'],
                ]);
            }
        }
    }
}
