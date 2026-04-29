<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a specific test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('test@test.ee'),
            ],
        );

        // Blog first (authors → posts → comments), then demo catalog data
        $this->call([
            AuthorSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
            NflRookieSeeder::class,
        ]);
    }
}
