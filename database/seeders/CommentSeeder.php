<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 comments for each post
        $posts = \App\Models\Post::all();
        foreach ($posts as $post) {
            \App\Models\Comment::factory(3)->create([
                'post_id' => $post->id,
            ]);
        }
    }
}
