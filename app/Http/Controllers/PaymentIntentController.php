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
    // Create a fake payment intent (placeholder for Stripe integration)
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
        // Reuse pending payment or create new
        $payment = Payment::where('application_id',$application->id)
            ->where('status','pending')
            ->latest()->first();
        if (!$payment) {
            $payment = Payment::create([
                'application_id' => $application->id,
                'provider' => 'stripe',
                'provider_ref' => 'pi_'.uniqid(),
                'amount_cents' => $package->price_cents,
                'currency' => 'usd',
                'status' => 'pending',
                'payload' => ['demo' => true],
            ]);
        }
        return response()->json([
            'payment_id' => $payment->id,
            'provider' => $payment->provider,
            'client_secret' => 'demo_client_secret_'.$payment->id,
            'amount_cents' => $payment->amount_cents,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'confirm_endpoint' => route('payments.confirm',['payment'=>$payment->id]),
        ]);
    }

    // Confirm payment (simulate success)
    public function confirm(Payment $payment, Request $request)
    {
        $application = $payment->application;
    $this->authorize('view', $application);
        $sm = new PaymentStateMachine();
        if(!$sm->canTransition($payment, \App\Enums\PaymentStatus::Succeeded)) {
            return response()->json(['error'=>'Invalid transition','status'=>$payment->status],422);
        }
        $sm->transition($payment, \App\Enums\PaymentStatus::Succeeded, ['source'=>'manual_confirm']);
        return response()->json([
            'payment_id'=>$payment->id,
            'status'=>$payment->status,
            'paid_at'=>$payment->paid_at,
            'application_id'=>$application->id,
            'application_payment_status'=>$application->payment_status,
            'next' => route('dashboard'),
        ]);
    }

    // Ownership now enforced by ApplicationPolicy
}
