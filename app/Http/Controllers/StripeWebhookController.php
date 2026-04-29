<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe as StripeApi;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        $webhookSecret = config('services.stripe.webhook_secret');
        if (!is_string($webhookSecret) || $webhookSecret === '') {
            return response()->json(['error' => 'Stripe webhook secret missing'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid Stripe signature'], 400);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Webhook error'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $sessionId = (string) ($session->id ?? '');

            $order = Order::query()
                ->where('stripe_checkout_session_id', $sessionId)
                ->first();

            if ($order) {
                $paymentStatus = (string) ($session->payment_status ?? '');

                if ($paymentStatus === 'paid' && $order->payment_status !== 'paid') {
                    $stripePaymentIntentId = (string) ($session->payment_intent ?? '');

                    $order->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                        'stripe_payment_intent_id' => $stripePaymentIntentId !== '' ? $stripePaymentIntentId : null,
                    ]);
                }
            }
        }

        return response()->json(['received' => true]);
    }
}

