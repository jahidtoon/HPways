<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\PaymentStateMachine;
use App\Enums\PaymentStatus;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Simplified demo (real implementation: verify signature)
        $data = $request->validate([
            'provider_ref' => ['required','string'],
            'event' => ['required','string'],
        ]);
        $payment = Payment::where('provider_ref',$data['provider_ref'])->first();
        if(!$payment){
            return response()->json(['error'=>'payment_not_found'],404);
        }
        $sm = new PaymentStateMachine();
        $map = [
            'payment_intent.succeeded' => PaymentStatus::Succeeded,
            'payment_intent.payment_failed' => PaymentStatus::Failed,
            'charge.refunded' => PaymentStatus::Refunded,
        ];
        if(!isset($map[$data['event']])){
            return response()->json(['ignored'=>true]);
        }
        $target = $map[$data['event']];
        if($sm->canTransition($payment,$target)){
            $sm->transition($payment,$target,['source'=>'webhook','raw_event'=>$data['event']]);
        }
        return response()->json(['status'=>$payment->refresh()->status]);
    }
}
