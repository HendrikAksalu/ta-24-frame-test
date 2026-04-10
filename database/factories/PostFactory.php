<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $body = $this->faker->paragraphs(3, true);

        return [
            'title' => $this->faker->sentence(),
            'description' => $body,
            'content' => $body,
            'author_id' => Author::factory(),
            'published' => $this->faker->boolean(),
        ];
    }

    /**
     * Attach comments to the post when creating.
     *
     * Usage: Post::factory()->withComments(3)->create();
     */
    public function withComments(int $count = 3)
    {
        return $this->has(Comment::factory()->count($count), 'comments');
    }
}
