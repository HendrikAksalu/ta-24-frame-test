<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * A small, stable set of authors for blog admin dropdowns and demos.
     * Safe to run on production: skips if authors already exist.
     */
    public function run(): void
    {
        if (Author::query()->exists()) {
            return;
        }

        $rows = [
            ['first_name' => 'Mari', 'last_name' => 'Kask', 'date_of_birth' => '1992-05-14'],
            ['first_name' => 'Jaan', 'last_name' => 'Tamm', 'date_of_birth' => '1988-11-02'],
            ['first_name' => 'Liis', 'last_name' => 'Rebane', 'date_of_birth' => '1995-03-21'],
            ['first_name' => 'Karl', 'last_name' => 'Saar', 'date_of_birth' => '1990-07-30'],
            ['first_name' => 'Helena', 'last_name' => 'Mägi', 'date_of_birth' => '1993-12-08'],
        ];

        foreach ($rows as $row) {
            Author::query()->create($row);
        }
    }
}
