<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Package;
use App\Models\Payment;
use App\Services\PaymentStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentIntentController extends Controller
{
    // Create Stripe checkout session
    public function create(Application $application)
    {
        $this->authorize('view', $application);
        if (!$application->selected_package_id) {
            return response()->json(['error' => 'No package selected'], 422);
        }
        $package = Package::find($application->selected_package_id);
        if (!$package) {
            return response()->json(['error' => 'Package missing'], 500);
        }

        // Get active Stripe settings
        $stripeSettings = \App\Models\PaymentSetting::where('gateway', 'stripe')
            ->where('is_active', true)
            ->first();

        if (!$stripeSettings || !isset($stripeSettings->credentials['secret_key'])) {
            return response()->json(['error' => 'Stripe not configured'], 500);
        }

        // Set Stripe API key and version
        \Stripe\Stripe::setApiKey($stripeSettings->credentials['secret_key']);
        \Stripe\Stripe::setApiVersion('2024-06-20'); // Use stable API version

        try {
            // Check if payment already exists
            $payment = Payment::where('application_id', $application->id)
                ->where('status', 'pending')
                ->latest()->first();

            if (!$payment) {
                // Create local payment record first to get the ID
                $payment = Payment::create([
                    'application_id' => $application->id,
                    'provider' => 'stripe',
                    'provider_ref' => 'temp_' . uniqid(), // Temporary ref, will update after Stripe session creation
                    'amount_cents' => $package->price_cents,
                    'currency' => 'usd',
                    'status' => 'pending',
                    'payload' => [],
                ]);

                                // Create Stripe Checkout Session with correct URLs
                $checkoutSession = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => $package->name,
                                'description' => "Payment for {$application->visa_type} Application",
                            ],
                            'unit_amount' => $package->price_cents,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => route('payments.success', ['payment' => $payment->id]),
                    'cancel_url' => route('dashboard.applicant.application.view', $application->id),
                    'metadata' => [
                        'application_id' => $application->id,
                        'package_id' => $package->id,
                        'user_email' => $application->user->email,
                        'payment_id' => $payment->id,
                    ],
                ]);

                // Update payment record with Stripe session details
                $payment->update([
                    'provider_ref' => $checkoutSession->id,
                    'payload' => [
                        'checkout_session_id' => $checkoutSession->id,
                        'url' => $checkoutSession->url,
                    ],
                ]);
            } else {
                // Get existing Checkout Session
                $checkoutSession = \Stripe\Checkout\Session::retrieve($payment->provider_ref);
            }

            return response()->json([
                'payment_id' => $payment->id,
                'provider' => $payment->provider,
                'checkout_url' => $checkoutSession->url,
                'amount_cents' => $payment->amount_cents,
                'currency' => $payment->currency,
                'status' => $payment->status,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => 'Stripe API error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment creation failed: ' . $e->getMessage()], 500);
        }
    }

    // Handle successful payment return from Stripe
    public function success(Payment $payment, Request $request)
    {
        $application = $payment->application;
        $this->authorize('view', $application);

        // Check if payment was actually completed
        if ($payment->status === 'succeeded') {
            return redirect()->route('dashboard.applicant.index')
                ->with('success', 'Payment completed successfully!');
        }

        // For Checkout Sessions, we need to verify the payment status
        // This would typically be handled by webhooks, but for now we'll check the session
        try {
            $stripeSettings = \App\Models\PaymentSetting::where('gateway', 'stripe')
                ->where('is_active', true)
                ->first();

            if ($stripeSettings && isset($stripeSettings->credentials['secret_key'])) {
                \Stripe\Stripe::setApiKey($stripeSettings->credentials['secret_key']);
                \Stripe\Stripe::setApiVersion('2024-06-20'); // Use stable API version

                $checkoutSession = \Stripe\Checkout\Session::retrieve($payment->provider_ref);

                if ($checkoutSession->payment_status === 'paid') {
                    $sm = new PaymentStateMachine();
                    if ($sm->canTransition($payment, \App\Enums\PaymentStatus::Succeeded)) {
                        $sm->transition($payment, \App\Enums\PaymentStatus::Succeeded, ['source' => 'stripe_checkout']);
                    }
                    return redirect()->route('dashboard.applicant.index')
                        ->with('success', 'Payment completed successfully!');
                }
            }
        } catch (\Exception $e) {
            // Log error but continue
            \Log::error('Stripe checkout verification failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard.applicant.index')
            ->with('warning', 'Payment status is being verified. Please check back in a few minutes.');
    }

    // Ownership now enforced by ApplicationPolicy
}
