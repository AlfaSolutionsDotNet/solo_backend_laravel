<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
    public function createPaymentSheet(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // 1. Create Customer
            $customer = \Stripe\Customer::create();

            // 2. Create Ephemeral Key
            $ephemeralKey = \Stripe\EphemeralKey::create(
                ['customer' => $customer->id],
                ['stripe_version' => env('STRIPE_API_VERSION')]
            );

            // 3. Create Payment Intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => 20 * 100, // $100 in cents
                'currency' => 'usd',
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'paymentIntent' => $paymentIntent->client_secret,
                'ephemeralKey' => $ephemeralKey->secret,
                'customer' => $customer->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}