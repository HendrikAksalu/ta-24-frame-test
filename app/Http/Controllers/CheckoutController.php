<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\CartSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Stripe as StripeApi;

class CheckoutController extends Controller
{
    public function index(): Response|RedirectResponse
    {
        CartSync::refreshSessionCart();

        $cart = session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('checkout/Index', [
            'cart' => array_values($cart),
        ]);
    }

    /**
     * PayPal „turvaline“ voog arenduses: server teeb päris PayPal API külge mockitud kinnituse
     * (sarnane K-Siim/RalfiHarjutus simulatsioonile). Kui PAYPAL_SIMULATE_FAILURE=true, näidatakse ebaõnnestumist
     * ja ostukorv jääb alles.
     *
     * @see https://github.com/K-Siim/RalfiHarjutus
     */
    public function paypalCheckout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
        ]);

        CartSync::refreshSessionCart();

        $cart = session()->get('cart', []);

        if ($cart === []) {
            return redirect()->back()->withErrors(['payment' => 'Ostukorv on tühi.']);
        }

        if (config('services.paypal.simulate_failure')) {
            return redirect()->back()->withErrors([
                'payment' => 'PayPal simulatsioon: makse lükati tagasi (PAYPAL_SIMULATE_FAILURE). Ostukorv jäi alles.',
            ]);
        }

        $total = collect($cart)->sum(fn ($item) => (float) $item['price'] * (int) $item['quantity']);

        try {
            $this->confirmPayPalSimulation($validated['email'], $total);
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'payment' => 'PayPal ühenduse viga: '.$e->getMessage(),
            ]);
        }

        $paypalTransactionId = 'PP-'.strtoupper(Str::random(16));

        $storesPaypalId = Schema::hasColumn('orders', 'paypal_transaction_id');

        $order = DB::transaction(function () use ($validated, $cart, $total, $paypalTransactionId, $storesPaypalId) {
            $attributes = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'total' => $total,
                'payment_status' => 'paid',
                'payment_provider' => 'paypal',
                'paid_at' => now(),
            ];

            if ($storesPaypalId) {
                $attributes['paypal_transaction_id'] = $paypalTransactionId;
            }

            $order = Order::query()->create($attributes);

            foreach ($cart as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'] ?? null,
                    'product_name' => (string) ($item['name'] ?? 'Toode'),
                    'price' => (float) ($item['price'] ?? 0),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.pay', ['order' => $order->id]);
    }

    /**
     * Koht tootmise jaoks: siia tuleks OAuth + PayPal Orders/Capture REST kõned.
     * Praegu tagastatakse kohe „approved“, et demonstreerida õnnestunud makseahelat.
     */
    private function confirmPayPalSimulation(string $email, float $total): void
    {
        // Placeholder päris API jaoks (sandbox: api-m.sandbox.paypal.com).
        if ($total <= 0) {
            throw new \InvalidArgumentException('Summa peab olema positiivne.');
        }

        $email = trim($email);
        if ($email === '') {
            throw new \InvalidArgumentException('E-post puudub.');
        }
    }

    public function stripeCheckout(Request $request): RedirectResponse|Response
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
        ]);

        CartSync::refreshSessionCart();

        $cart = session()->get('cart', []);

        if ($cart === []) {
            return redirect()->back()->withErrors(['payment' => 'Ostukorv on tühi.']);
        }

        $stripeSecret = config('services.stripe.secret');
        $currency = config('services.stripe.currency', 'eur');

        if (! is_string($stripeSecret) || $stripeSecret === '') {
            return redirect()->back()->withErrors(['payment' => 'Stripe pole seadistatud (puudub STRIPE_SECRET_KEY).']);
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
            'success_url' => url('/checkout/pay').'?session_id={CHECKOUT_SESSION_ID}',
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
        if ($request->filled('order')) {
            $order = Order::query()
                ->with('items')
                ->findOrFail((int) $request->query('order'));

            if ($order->payment_provider !== 'paypal' || $order->payment_status !== 'paid') {
                abort(404);
            }

            return Inertia::render('checkout/Success', [
                'order' => $this->orderPayload($order),
            ]);
        }

        $sessionId = $request->query('session_id');
        if (! is_string($sessionId) || $sessionId === '') {
            abort(404);
        }

        $order = Order::query()
            ->where('stripe_checkout_session_id', $sessionId)
            ->with('items')
            ->firstOrFail();

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
            'order' => $this->orderPayload($order->fresh(['items'])),
        ]);
    }

    private function orderPayload(Order $order): array
    {
        $order->loadMissing('items');

        $productIds = $order->items->pluck('product_id')->filter()->unique()->values();
        $images = $productIds->isEmpty()
            ? collect()
            : Product::query()->whereIn('id', $productIds)->pluck('image', 'id');

        $paymentId = $order->paypal_transaction_id
            ?? $order->stripe_payment_intent_id
            ?? $order->stripe_checkout_session_id
            ?? '—';

        return [
            'id' => $order->id,
            'order_number' => 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'order_short' => '#'.$order->id,
            'first_name' => $order->first_name,
            'email' => $order->email,
            'total' => (string) $order->total,
            'payment_status' => $order->payment_status,
            'payment_provider' => $order->payment_provider,
            'payment_id' => $paymentId,
            'paid_at' => $order->paid_at?->toISOString(),
            'items' => $order->items->map(fn ($i) => [
                'product_name' => $i->product_name,
                'price' => (string) $i->price,
                'quantity' => (int) $i->quantity,
                'image' => $i->product_id ? ($images[$i->product_id] ?? null) : null,
            ])->values(),
        ];
    }

    public function cancel(): Response
    {
        return Inertia::render('checkout/Cancel');
    }
}
