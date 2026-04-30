<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Running Shoes X1',
                'description' => 'Lightweight running shoes for daily training and long walks.',
                'image' => 'https://placehold.co/600x400/png?text=Running+Shoes+X1',
                'price' => 89.90,
                'sku' => 'SHOE-X1-001',
                'stock_quantity' => 24,
            ],
            [
                'name' => 'Hoodie Urban Grey',
                'description' => 'Soft cotton hoodie with a relaxed fit and warm interior.',
                'image' => 'https://placehold.co/600x400/png?text=Hoodie+Urban+Grey',
                'price' => 49.90,
                'sku' => 'HOOD-GREY-002',
                'stock_quantity' => 31,
            ],
            [
                'name' => 'Wireless Earbuds Pro',
                'description' => 'Compact earbuds with clear sound and charging case.',
                'image' => 'https://placehold.co/600x400/png?text=Wireless+Earbuds+Pro',
                'price' => 79.00,
                'sku' => 'AUDIO-PRO-003',
                'stock_quantity' => 19,
            ],
            [
                'name' => 'Smart Water Bottle',
                'description' => 'Insulated bottle that keeps drinks cold and tracks daily intake.',
                'image' => 'https://placehold.co/600x400/png?text=Smart+Water+Bottle',
                'price' => 34.50,
                'sku' => 'BOTTLE-SMART-004',
                'stock_quantity' => 42,
            ],
            [
                'name' => 'Backpack Travel 28L',
                'description' => 'Durable backpack with laptop sleeve and hidden zipper pocket.',
                'image' => 'https://placehold.co/600x400/png?text=Backpack+Travel+28L',
                'price' => 64.90,
                'sku' => 'PACK-TRAVEL-005',
                'stock_quantity' => 15,
            ],
            [
                'name' => 'Fitness Mat Comfort',
                'description' => 'Non-slip exercise mat for stretching, yoga, and home workouts.',
                'image' => 'https://placehold.co/600x400/png?text=Fitness+Mat+Comfort',
                'price' => 29.90,
                'sku' => 'MAT-COMFORT-006',
                'stock_quantity' => 36,
            ],
            [
                'name' => 'Desk Lamp Minimal',
                'description' => 'Adjustable LED desk lamp with warm and cool light modes.',
                'image' => 'https://placehold.co/600x400/png?text=Desk+Lamp+Minimal',
                'price' => 39.90,
                'sku' => 'LAMP-MIN-007',
                'stock_quantity' => 22,
            ],
            [
                'name' => 'Mechanical Keyboard 87',
                'description' => 'Compact keyboard with tactile switches and white backlight.',
                'image' => 'https://placehold.co/600x400/png?text=Mechanical+Keyboard+87',
                'price' => 99.00,
                'sku' => 'KEYBOARD-87-008',
                'stock_quantity' => 14,
            ],
            [
                'name' => 'Portable SSD 1TB',
                'description' => 'Fast external SSD for backups, media, and project files.',
                'image' => 'https://placehold.co/600x400/png?text=Portable+SSD+1TB',
                'price' => 119.00,
                'sku' => 'SSD-1TB-009',
                'stock_quantity' => 17,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
