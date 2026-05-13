<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Demo köögiviljad — ainult kohalikud /img/veg failid (ei kasuta Faker ladinateksti ega suuri hindu).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $rows = [
            ['name' => 'Tomatid (250 g)', 'desc' => 'Mini-kasti tomatid salati kiirkorraks.', 'img' => '/img/veg/tomato.jpg'],
            ['name' => 'Brokkoli (tükk)', 'desc' => 'Peakapsas lühikeseks küpsetusajaks.', 'img' => '/img/veg/broccoli.jpg'],
            ['name' => 'Porgandid (500 g)', 'desc' => 'Krõmpsud juurviljad supile.', 'img' => '/img/veg/carrot.jpg'],
            ['name' => 'Salatilehed', 'desc' => 'Segu kiireks lõunaks.', 'img' => '/img/veg/lettuce.svg'],
            ['name' => 'Kurk', 'desc' => 'Krõbe kurk dipiga.', 'img' => '/img/veg/cucumber.svg'],
        ];

        $row = fake()->randomElement($rows);

        return [
            'name' => $row['name'],
            'description' => $row['desc'],
            'image' => $row['img'],
            'price' => fake()->randomFloat(2, 0.49, 3.99),
            'sku' => 'FCT-'.fake()->unique()->numerify('######'),
            'stock_quantity' => fake()->numberBetween(15, 80),
        ];
    }
}
