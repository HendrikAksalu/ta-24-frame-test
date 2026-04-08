<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = Author::query()->first();

        if (!$author) {
            $author = Author::create([
                'first_name' => 'Test',
                'last_name' => 'Author',
                'date_of_birth' => '2000-01-01',
            ]);
        }

        foreach (range(1, 12) as $index) {
            Post::query()->create([
                'title' => "Sample blog post #{$index}",
                'description' => "This is sample description #{$index}.",
                'content' => "This is sample description #{$index}.",
                'author_id' => $author->id,
                'published' => true,
            ]);
        }
    }
}
