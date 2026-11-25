<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripePaymentController extends Controller
{
//     public function createPaymentSheet(Request $request)
// {
//     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

//     try {
//         // ✅ 1. Validate input
//         $request->validate([
//             'amount' => 'required|numeric|min:1', // must be at least $1
//             'currency' => 'nullable|string|size:3' // optional, default USD
//         ]);

//         // // ✅ 2. Get amount and currency from request
//         // $amount = $request->amount; // amount in dollars
//         // $currency = $request->currency ?? 'usd'; // default to USD
//         // $amountInCents = $amount * 100; // convert dollars to cents

//          // ✅ Handle empty or null amount
//         $amount = $request->amount;
//         if (empty($amount) || $amount <= 0) {
//             $amount = 1; // Default to $1
//         }

//         $currency = $request->currency ?? 'usd';
//         $amountInCents = $amount * 100;

//         // ✅ 3. Create Stripe customer
//         $customer = \Stripe\Customer::create();

//         // ✅ 4. Create Ephemeral Key
//         $ephemeralKey = \Stripe\EphemeralKey::create(
//             ['customer' => $customer->id],
//             ['stripe_version' => env('STRIPE_API_VERSION')]
//         );

//         // ✅ 5. Create PaymentIntent with dynamic amount
//         $paymentIntent = \Stripe\PaymentIntent::create([
//             'amount' => $amountInCents,
//             'currency' => $currency,
//             'customer' => $customer->id,
//             'payment_method_types' => ['card'],
//         ]);

//         // ✅ 6. Return success response
//         return response()->json([
//             'paymentIntent' => $paymentIntent->client_secret,
//             'ephemeralKey' => $ephemeralKey->secret,
//             'customer' => $customer->id,
//             'amount' => $amount,
//             'currency' => $currency,
//         ]);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }


public function createPaymentSheet(Request $request)
{
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        // ✅ Validate input (but make 'amount' optional here)
        $request->validate([
            'amount' => 'nullable|numeric|min:1',
            'currency' => 'nullable|string|size:3'
        ]);

        // ✅ Handle empty or null amount
        $amount = $request->amount;
        if (empty($amount) || $amount <= 0) {
            $amount = 1; // Default to $1
        }

        $currency = $request->currency ?? 'usd';
        $amountInCents = $amount * 100;

        // ✅ Create Stripe customer
        $customer = \Stripe\Customer::create();

        // ✅ Create ephemeral key
        $ephemeralKey = \Stripe\EphemeralKey::create(
            ['customer' => $customer->id],
            ['stripe_version' => env('STRIPE_API_VERSION')]
        );

        // ✅ Create payment intent
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amountInCents,
            'currency' => $currency,
            'customer' => $customer->id,
            'payment_method_types' => ['card'],
        ]);

        // ✅ Return response
        return response()->json([
            'paymentIntent' => $paymentIntent->client_secret,
            'ephemeralKey' => $ephemeralKey->secret,
            'customer' => $customer->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



}