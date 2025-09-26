<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;
use App\Models\Payment;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ApplicantController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get current application
        $currentApplication = Application::where('user_id', $user->id)
            ->with(['caseManager', 'attorney', 'documents', 'feedback', 'payments', 'shipment'])
            ->latest()
            ->first();
            
        // Get all applications
        $applications = Application::where('user_id', $user->id)
            ->with(['caseManager', 'attorney'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get recent feedback from attorney
        $recentFeedback = Feedback::whereHas('application', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['application', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get pending documents
        $pendingDocuments = $currentApplication ? ($currentApplication->missing_documents ?? []) : [];
        
        // Check payment status
        $hasPayments = $currentApplication && Payment::where('application_id', $currentApplication->id)
            ->where('status', 'succeeded')
            ->exists();
            
        // Get tracking information if available (status + number)
        $trackingInfo = null;         // e.g., tracking number
        $trackingStatus = null;       // e.g., prepared/shipped/delivered
        if ($currentApplication && $currentApplication->shipment) {
            $trackingInfo = $currentApplication->shipment->tracking_number;
            $trackingStatus = $currentApplication->shipment->status ?: null;
        } else {
            // If not yet shipped, summarize based on application status
            $map = [
                'in_print_queue' => 'Queued for printing',
                'printing' => 'Printing in progress',
                'printed' => 'Printed (awaiting shipment)',
                'ready_to_ship' => 'Ready to ship'
            ];
            $trackingStatus = $currentApplication?->status && isset($map[$currentApplication->status]) ? $map[$currentApplication->status] : null;
        }

        return view('dashboard.applicant.index', compact(
            'user',
            'currentApplication', 
            'applications',
            'recentFeedback',
            'pendingDocuments',
            'hasPayments',
            'trackingInfo',
            'trackingStatus'
        ));
    }

    public function applications()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get applications for the logged in user
        $applications = Application::where('user_id', $user->id)
            ->with(['caseManager', 'attorney', 'documents', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dashboard.applicant.applications', compact('applications', 'user'));
    }

    public function documents()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user applications with documents
        $applications = Application::where('user_id', $user->id)
            ->with(['documents','selectedPackage'])
            ->get();

        $currentApplication = $applications->first();

        // Build a list of uploaded documents grouped by type with latest status
        $uploadedDocuments = [];
        if ($currentApplication) {
            $docs = $currentApplication->documents()->orderBy('created_at','desc')->get();

            // Build label map for codes from package/visa requirements
            $labelsByCode = [];
            try {
                if ($currentApplication->selectedPackage && Schema::hasTable('package_required_documents')) {
                    $rows = $currentApplication->selectedPackage->requiredDocuments()->where('active',true)->get();
                    foreach($rows as $r){ $labelsByCode[strtoupper($r->code)] = $r->label; }
                }
            } catch (\Throwable $e) { /* ignore */ }
            if (empty($labelsByCode)) {
                try {
                    $visa = $currentApplication->visa_type;
                    if ($visa && Schema::hasTable('required_documents')) {
                        $rows = \App\Models\RequiredDocument::where('visa_type',$visa)->where('active',true)->get();
                        foreach($rows as $r){ $labelsByCode[strtoupper($r->code)] = $r->label; }
                    } else if ($visa) {
                        foreach (config('required_documents.'.strtoupper($visa), []) as $r) {
                            if (!empty($r['code']) && !empty($r['label'])) $labelsByCode[strtoupper($r['code'])] = $r['label'];
                        }
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }

            // Group by type (code) and collapse duplicates, keeping latest
            $grouped = $docs->groupBy(function($d){ return strtoupper($d->type ?? 'GENERAL'); });
            foreach($grouped as $code => $list){
                $latest = $list->first();
                $uploadedDocuments[] = [
                    'code' => $code,
                    'label' => $labelsByCode[$code] ?? $code,
                    'latest_id' => $latest->id,
                    'latest_name' => $latest->original_name,
                    'status' => $latest->status ?? 'pending',
                    'count' => $list->count(),
                    'created_at' => optional($latest->created_at)->format('M d, Y'),
                ];
            }

            // Sort by label for stable UI
            usort($uploadedDocuments, fn($a,$b)=>strcmp($a['label'],$b['label']));
        }

        return view('dashboard.applicant.documents', compact('applications', 'currentApplication', 'uploadedDocuments', 'user'));
    }

    public function uploadDocuments($application = null)
    {
        $userId = auth()->id();
        
        // Handle route parameter - could be Application model or ID
        if ($application) {
            if (is_numeric($application)) {
                $currentApplication = Application::find($application);
            } else {
                $currentApplication = $application;
            }
        } else {
            $currentApplication = Application::where('user_id', $userId)->latest()->first();
        }
        
        if (!$currentApplication || $currentApplication->user_id !== $userId) {
            return redirect()->route('dashboard.applicant.documents')->with('error','No application found or access denied.');
        }

        // Load the selectedPackage relationship if not already loaded
        if (!$currentApplication->relationLoaded('selectedPackage')) {
            $currentApplication->load('selectedPackage');
        }

        // Build a unified required list (required + optional) for clarity
        $uploadedTypes = $currentApplication->documents()->pluck('type')->filter()->map(fn($t)=>strtoupper($t))->unique();
        $required = [];
        $optional = [];

        // Package-specific first
        if (Schema::hasTable('package_required_documents') && $currentApplication->selectedPackage) {
            try {
                $rows = $currentApplication->selectedPackage->requiredDocuments()->where('active',true)->get();
                foreach($rows as $r){
                    $item = [
                        'code'=>strtoupper($r->code),
                        'label'=>$r->label,
                        'required'=>(bool)$r->required,
                        'translation_possible'=>(bool)($r->translation_possible ?? false),
                        'uploaded'=>$uploadedTypes->contains(strtoupper($r->code)),
                    ];
                    if ($r->required) {
                        $required[] = $item;
                    } else {
                        $optional[] = $item;
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Visa-type DB or config fallback
        if (empty($required) && empty($optional)) {
            $visa = $currentApplication->visa_type;
            $rows = collect();
            if ($visa && Schema::hasTable('required_documents')) {
                try {
                    $rows = \App\Models\RequiredDocument::where('visa_type',$visa)->where('active',true)->get()
                        ->map(fn($r)=>[
                            'code'=>strtoupper($r->code),
                            'label'=>$r->label,
                            'required'=>(bool)$r->required,
                            'translation_possible'=>(bool)($r->translation_possible ?? false),
                        ]);
                } catch (\Throwable $e) { $rows = collect(); }
            }
            if ($rows->isEmpty() && $visa) {
                $rows = collect(config('required_documents.'.strtoupper($visa), []));
            }
            foreach($rows as $r){
                $code = strtoupper($r['code'] ?? '');
                if(!$code) continue;
                $item = [
                    'code'=>$code,
                    'label'=>$r['label'] ?? $code,
                    'required'=>(bool)($r['required'] ?? false),
                    'translation_possible'=>(bool)($r['translation_possible'] ?? false),
                    'uploaded'=>$uploadedTypes->contains($code),
                ];
                if ($item['required']) {
                    $required[] = $item;
                } else {
                    $optional[] = $item;
                }
            }
        }

        return view('dashboard.applicant.upload-documents', compact('currentApplication','required','optional'));
    }

    public function payments()
    {
        // Get user payments
        $payments = Payment::whereHas('application', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->with('application')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get applications with selected packages but no completed payments
        $pendingPayments = Application::where('user_id', auth()->id())
            ->whereNotNull('selected_package_id')
            ->whereDoesntHave('payments', function($query) {
                $query->where('status', 'succeeded');
            })
            ->with('selectedPackage')
            ->get();

        return view('dashboard.applicant.payments', compact('payments', 'pendingPayments'));
    }

    public function resources()
    {
        return view('dashboard.applicant.resources');
    }

    public function support()
    {
        $userApplications = Application::where('user_id', auth()->id())
            ->with(['caseManager', 'attorney'])
            ->get();
            
        return view('dashboard.applicant.support', compact('userApplications'));
    }

    public function reports()
    {
        $user = auth()->user() ?? User::find(16);
        $applications = Application::where('user_id', $user->id)
            ->with(['documents', 'payments'])
            ->get();
            
        return view('dashboard.applicant.reports', compact('applications', 'user'));
    }

    public function settings()
    {
        $user = auth()->user();
        return view('dashboard.applicant.settings', compact('user'));
    }
    
    public function viewApplication($id)
    {
        $application = Application::where('user_id', auth()->id())
            ->where('id', $id)
            ->with(['caseManager', 'attorney', 'documents', 'feedback.user', 'payments', 'shipment.trackingEvents'])
            ->firstOrFail();
            
        return view('dashboard.applicant.view-application', compact('application'));
    }
}
