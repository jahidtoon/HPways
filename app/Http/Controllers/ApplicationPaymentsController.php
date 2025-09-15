<?php
namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ApplicationPaymentsController extends Controller
{
    public function index(Application $application)
    {
        $this->authorize('view',$application);
        $payments = $application->payments()->latest()->get()->map(fn($p)=>[
            'id'=>$p->id,
            'amount_cents'=>$p->amount_cents,
            'currency'=>$p->currency,
            'status'=>$p->status,
            'paid_at'=>$p->paid_at?->toIso8601String(),
            'created_at'=>$p->created_at->toIso8601String(),
        ]);
        return response()->json([
            'application_id'=>$application->id,
            'payments'=>$payments,
        ]);
    }
}
