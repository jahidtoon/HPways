<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Package;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Services\RequiredDocumentService;

class UserApplicationController extends Controller
{
    public function current()
    {
        $user = Auth::user();
        if(!$user) return response()->json(['error'=>'Unauthenticated'],401);

    $application = Application::with(['selectedPackage','documents'])->where('user_id',$user->id)->latest()->first();
        if(!$application){
            return response()->json(['application'=>null]);
        }
        $packages = [];
        if(!$application->selected_package_id){
            $packages = Package::query()
                ->where(function($q) use ($application){ $q->whereNull('visa_type')->orWhere('visa_type',$application->visa_type); })
                ->where('active',true)
                ->orderByRaw("CASE WHEN visa_type IS NULL THEN 1 ELSE 0 END, price_cents ASC")
                ->get()
                ->map(fn($p)=>[
                    'id'=>$p->id,
                    'code'=>$p->code,
                    'name'=>$p->name,
                    'price_cents'=>$p->price_cents,
                    'features'=>$p->features,
                ]);
        }
        $latestPayment = Payment::where('application_id',$application->id)->latest()->first();

        // Recalculate missing documents (on the fly) and cache into application record if changed
        $missing = [];
        $progressBreakdown = null;
        if($application->visa_type){
            $svc = new RequiredDocumentService();
            $missing = $svc->missingFor($application);
            $stored = $application->missing_documents ?? [];
            // Simple diff check by count or content mismatch
            if(json_encode($stored) !== json_encode($missing)){
                $application->missing_documents = $missing;
                $application->save();
            }
            $progressBreakdown = $svc->progressBreakdown($application);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'application' => [
                'id' => $application->id,
                'visa_type' => $application->visa_type,
                'status' => $application->status,
                'progress_pct' => $application->progress_pct,
                'progress_breakdown' => $progressBreakdown,
                'payment_status' => $application->payment_status,
                'missing_documents' => $missing,
                'required_documents_total' => $progressBreakdown['documents_pct'] !== null ? \App\Models\RequiredDocument::where('visa_type',$application->visa_type)->where('active',true)->where('required',true)->count() : null,
                'selected_package' => $application->selectedPackage ? [
                    'id'=>$application->selectedPackage->id,
                    'name'=>$application->selectedPackage->name,
                    'code'=>$application->selectedPackage->code,
                    'price_cents'=>$application->selectedPackage->price_cents,
                ] : null,
                'packages' => $packages,
                'latest_payment' => $latestPayment ? [
                    'id'=>$latestPayment->id,
                    'status'=>$latestPayment->status,
                    'amount_cents'=>$latestPayment->amount_cents,
                ] : null,
            ]
        ]);
    }
}
