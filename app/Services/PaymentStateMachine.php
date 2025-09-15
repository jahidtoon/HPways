<?php
namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Application;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentStateMachine
{
    /**
     * Allowed transitions map
     */
    protected array $transitions = [
        'pending' => ['succeeded','failed'],
        'succeeded' => ['refunded'],
        'failed' => [],
        'refunded' => [],
    ];

    public function canTransition(Payment $payment, PaymentStatus $to): bool
    {
        $current = $payment->status;
        return in_array($to->value, $this->transitions[$current] ?? [], true);
    }

    public function transition(Payment $payment, PaymentStatus $to, array $meta = []): Payment
    {
        if(!$this->canTransition($payment,$to)) {
            throw new InvalidArgumentException("Invalid payment status transition {$payment->status} -> {$to->value}");
        }
        DB::transaction(function() use ($payment,$to,$meta){
            $update = ['status'=>$to->value];
            if($to === PaymentStatus::Succeeded){
                $update['paid_at'] = now();
            }
            $payload = $payment->payload ?? [];
            $payload['events'][] = [
                'to' => $to->value,
                'meta' => $meta,
                'at' => now()->toIso8601String(),
            ];
            $update['payload'] = $payload;
            $payment->update($update);
            // Side effects
            if($to === PaymentStatus::Succeeded){
                /** @var Application $app */
                $app = $payment->application;
                $app->update([
                    'payment_status' => 'paid',
                    'progress_pct' => max($app->progress_pct, 20),
                ]);
            }
            if($to === PaymentStatus::Refunded){
                $payment->application->update(['payment_status'=>'refunded']);
            }
        });
        return $payment->refresh();
    }
}
