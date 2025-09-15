<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageSelectionController extends Controller
{
    // List packages relevant to an application (visa specific + global fallback)
    public function index(Application $application)
    {
        $this->authorize('view', $application);
        $packages = Package::query()
            ->where(function($q) use ($application){
                $q->whereNull('visa_type')->orWhere('visa_type',$application->visa_type);
            })
            ->where('active',true)
            ->orderByRaw("CASE WHEN visa_type IS NULL THEN 1 ELSE 0 END, price_cents ASC")
            ->get();

        return response()->json([
            'application_id' => $application->id,
            'visa_type' => $application->visa_type,
            'selected_package_id' => $application->selected_package_id,
            'packages' => $packages->map(fn($p)=>[
                'id'=>$p->id,
                'code'=>$p->code,
                'name'=>$p->name,
                'price_cents'=>$p->price_cents,
                'features'=>$p->features,
                'selected' => $application->selected_package_id === $p->id,
            ]),
        ]);
    }

    // Select package
    public function store(Request $request, Application $application)
    {
    $this->authorize('update', $application);
        $data = $request->validate([
            'package_id' => ['required','integer','exists:packages,id'],
        ]);
        $package = Package::findOrFail($data['package_id']);
        // Ensure package is applicable
        if ($package->visa_type && $package->visa_type !== $application->visa_type) {
            return response()->json(['error' => 'Package not valid for this visa type'], 422);
        }
        $application->update(['selected_package_id'=>$package->id]);
        return response()->json([
            'application_id' => $application->id,
            'selected_package_id' => $application->selected_package_id,
            'next' => route('payments.intent', ['application'=>$application->id]),
        ]);
    }

    // Ownership now enforced by ApplicationPolicy
}
