<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe as StripeApi;

class CheckoutController extends Controller
{
    public function index(): Response
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('checkout/Index', [
            'cart' => array_values($cart),
        ]);
    }

    public function stripeCheckout(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Ostukorv on tühi.');
        }

        $stripeSecret = config('services.stripe.secret');
        $currency = config('services.stripe.currency', 'eur');

        if (!is_string($stripeSecret) || $stripeSecret === '') {
            return redirect()->back()->with('error', 'Stripe pole seadistatud (puudub STRIPE_SECRET_KEY).');
        }

        $total = collect($cart)->sum(function ($item) {
            return (float) $item['price'] * (int) $item['quantity'];
        });

        $order = Order::query()->create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'total' => $total,
            'payment_status' => 'pending',
            'payment_provider' => 'stripe',
        ]);

        foreach ($cart as $item) {
            OrderItem::query()->create([
                'order_id' => $order->id,
                'product_id' => $item['id'] ?? null,
                'product_name' => (string) ($item['name'] ?? 'Toode'),
                'price' => (float) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
            ]);
        }

        StripeApi::setApiKey($stripeSecret);

        $lineItems = [];
        foreach ($cart as $item) {
            $unitAmount = (int) round(((float) $item['price']) * 100);
            $lineItems[] = [
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => (string) ($item['name'] ?? ''),
                    ],
                ],
                'quantity' => (int) $item['quantity'],
            ];
        }

        $session = CheckoutSession::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'client_reference_id' => (string) $order->id,
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ]);

        $order->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return Inertia::location($session->url);
    }

    public function success(Request $request): Response
    {
        $sessionId = $request->query('session_id');
        if (!is_string($sessionId) || $sessionId === '') {
            abort(404);
        }

        $order = Order::query()
            ->where('stripe_checkout_session_id', $sessionId)
            ->with('items')
            ->firstOrFail();

        // Try to sync status from Stripe; if Stripe keys are not set, rely on DB value.
        $stripeSecret = config('services.stripe.secret');
        if (is_string($stripeSecret) && $stripeSecret !== '' && $order->payment_status !== 'paid') {
            try {
                StripeApi::setApiKey($stripeSecret);
                $stripeSession = CheckoutSession::retrieve($sessionId);
                if (isset($stripeSession->payment_status) && $stripeSession->payment_status === 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'stripe_payment_intent_id' => $stripeSession->payment_intent ?? null,
                    ]);
                }
            } catch (\Throwable) {
                // Ignore Stripe sync errors; user will see status based on DB.
            }
        }

        if ($order->fresh()->payment_status === 'paid') {
            session()->forget('cart');
        }

        return Inertia::render('checkout/Success', [
            'order' => [
                'id' => $order->id,
                'order_number' => 'ORD-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'total' => (string) $order->total,
                'payment_status' => $order->fresh()->payment_status,
                'paid_at' => $order->paid_at?->toISOString(),
                'items' => $order->items->map(fn ($i) => [
                    'product_name' => $i->product_name,
                    'price' => (string) $i->price,
                    'quantity' => (int) $i->quantity,
                ])->values(),
            ],
        ]);
    }

    public function cancel(): Response
    {
        return Inertia::render('checkout/Cancel');
    }
}

