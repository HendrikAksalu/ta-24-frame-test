<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Run on production when you only need blog demo content:
 * php artisan db:seed --class=Database\\Seeders\\BlogContentSeeder
 */
class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AuthorSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
