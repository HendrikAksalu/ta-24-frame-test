<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (\App\Models\Product::all() as $product) {
            \App\Models\Review::factory(rand(5, 8))->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
