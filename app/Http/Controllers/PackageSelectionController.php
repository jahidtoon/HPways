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
        // If the client expects JSON (API/AJAX), serve JSON; otherwise render the UI
        if (request()->wantsJson() || str_contains(request()->header('Accept',''), 'application/json')) {
            // Return only the three tiers for this visa (basic, advanced, premium).
            // Prefer visa-specific definitions; fall back to global (visa_type = null).
            $tierCodes = ['basic','advanced','premium'];

            $visaPackages = Package::query()
                ->where('visa_type', $application->visa_type)
                ->whereIn('code', $tierCodes)
                ->where('active', true)
                ->get()
                ->keyBy('code');

            $globalPackages = Package::query()
                ->whereNull('visa_type')
                ->whereIn('code', $tierCodes)
                ->where('active', true)
                ->get()
                ->keyBy('code');

            // Merge with preference to visa-specific
            $packages = collect($tierCodes)
                ->map(function($code) use ($visaPackages, $globalPackages) {
                    return $visaPackages->get($code) ?: $globalPackages->get($code);
                })
                ->filter() // drop any missing tiers
                ->values();

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
        // Render Blade UI
        return view('dashboard.applicant.packages', [
            'application' => $application,
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
        // Ensure package is applicable and is one of the allowed three tiers
        $allowedCodes = ['basic','advanced','premium'];
        if (!in_array($package->code, $allowedCodes, true)) {
            return response()->json(['error' => 'Only standard tiers are selectable'], 422);
        }
        if (!$package->active) {
            return response()->json(['error' => 'Selected package is not active'], 422);
        }
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
