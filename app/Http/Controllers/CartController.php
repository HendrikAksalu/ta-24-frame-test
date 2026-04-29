<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(): Response
    {
        $cart = session()->get('cart', []);

        return Inertia::render('cart/Index', [
            'cart' => array_values($cart),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Toode lisati ostukorvi.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            if ($quantity <= 0) {
                unset($cart[$product->id]);
            } else {
                $cart[$product->id]['quantity'] = $quantity;
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->back()->with('success', 'Ostukorv tühjendati.');
    }
}

