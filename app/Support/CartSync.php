<?php

namespace App\Support;

use App\Models\Product;

class CartSync
{
    /**
     * Värskendab sessiooni ostukorvi read praeguste toodete nime/hinna/pildi järgi.
     */
    public static function refreshSessionCart(): void
    {
        $cart = session()->get('cart', []);

        if ($cart === []) {
            return;
        }

        $updated = [];

        foreach ($cart as $productId => $line) {
            $product = Product::query()->find((int) $productId);

            if ($product === null) {
                continue;
            }

            $updated[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => max(1, (int) ($line['quantity'] ?? 1)),
            ];
        }

        session()->put('cart', $updated);
    }
}
