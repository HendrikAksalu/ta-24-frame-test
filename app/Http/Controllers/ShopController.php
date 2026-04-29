<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->orderBy('id')
            ->take(12)
            ->get(['id', 'name', 'description', 'price', 'sku', 'stock_quantity', 'image']);

        $cart = session()->get('cart', []);
        $cartCount = collect($cart)->sum(fn ($item) => (int) ($item['quantity'] ?? 0));

        return Inertia::render('shop/Index', [
            'products' => $products,
            'cartCount' => $cartCount,
        ]);
    }
}

