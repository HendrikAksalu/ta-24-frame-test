<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Köögiviljad — fotod ja vektorgraafika ainult /public/img/veg (välis-CDN puudub).
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Tomatid (500 g)',
                'description' => 'Punased küpse külmikus hoitavad tomatid — sobib salati või pasteeti põhjaks.',
                'image' => '/img/veg/tomato.jpg',
                'price' => 1.99,
                'sku' => 'VEG-TMT-500',
                'stock_quantity' => 48,
            ],
            [
                'name' => 'Brokkoli (1 tk)',
                'description' => 'Roheline peakapsas — lühike küpsetusaeg säilitab krõmpsuva tekstuuri.',
                'image' => '/img/veg/broccoli.jpg',
                'price' => 1.49,
                'sku' => 'VEG-BRK-001',
                'stock_quantity' => 36,
            ],
            [
                'name' => 'Salaatilehed mix (150 g)',
                'description' => 'Mitme lehesordi segu — pestud; tarbi paari päeva jooksul.',
                'image' => '/img/veg/lettuce.svg',
                'price' => 1.79,
                'sku' => 'VEG-SLT-MIX',
                'stock_quantity' => 40,
            ],
            [
                'name' => 'Porgandid (1 kg)',
                'description' => 'Oranž ja mahlane sort — hõõru koore või küpseta koos ürtidega.',
                'image' => '/img/veg/carrot.jpg',
                'price' => 1.09,
                'sku' => 'VEG-PRG-1KG',
                'stock_quantity' => 55,
            ],
            [
                'name' => 'Kurk (1 tk)',
                'description' => 'Värskelt lõhnatu kurk viilude või marineerimise jaoks.',
                'image' => '/img/veg/cucumber.svg',
                'price' => 0.59,
                'sku' => 'VEG-KRK-001',
                'stock_quantity' => 72,
            ],
            [
                'name' => 'Paprika punane (1 tk)',
                'description' => 'Magusama maitsega paprika — hautised ja fajitad sõbralik valik.',
                'image' => '/img/veg/pepper.svg',
                'price' => 0.99,
                'sku' => 'VEG-PPR-RED',
                'stock_quantity' => 44,
            ],
            [
                'name' => 'Kartul pestud (1,5 kg)',
                'description' => 'Mitmeotstarbeline kartul ahju- või pudrutoitude jaoks.',
                'image' => '/img/veg/potato.svg',
                'price' => 1.49,
                'sku' => 'VEG-KRT-15',
                'stock_quantity' => 60,
            ],
            [
                'name' => 'Sibul kollane (500 g)',
                'description' => 'Klassikaline maitseaine — hoida kuivas ja ventileeritavas kohas.',
                'image' => '/img/veg/onion.svg',
                'price' => 0.79,
                'sku' => 'VEG-SBL-500',
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Šampinjonid (250 g)',
                'description' => 'Hele kübaraga šampinjon — kuivalt pühkida enne praadimist.',
                'image' => '/img/veg/mushroom.svg',
                'price' => 1.99,
                'sku' => 'VEG-SHP-250',
                'stock_quantity' => 30,
            ],
        ];

        $keepSkus = array_column($products, 'sku');

        Product::query()->whereNotIn('sku', $keepSkus)->delete();

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }
    }
}
