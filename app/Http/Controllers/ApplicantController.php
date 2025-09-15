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
            ->where('status', 'completed')
            ->exists();
            
        // Get tracking information if available
        $trackingInfo = null;
        if ($currentApplication && $currentApplication->shipment) {
            $trackingInfo = $currentApplication->shipment->tracking_number;
        }

        return view('dashboard.applicant.index', compact(
            'user',
            'currentApplication', 
            'applications',
            'recentFeedback',
            'pendingDocuments',
            'hasPayments',
            'trackingInfo'
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

        // Determine required docs per package or visa type, then compute pending
        $pendingDocuments = [];
        if ($currentApplication) {
            $uploadedTypes = $currentApplication->documents->pluck('type')->filter()->map(fn($t)=>strtoupper($t))->unique();

            // Prefer package-specific required documents
            $requiredFromPackage = collect();
            if ($currentApplication->selectedPackage && Schema::hasTable('package_required_documents')) {
                try {
                    $requiredFromPackage = $currentApplication->selectedPackage
                        ->requiredDocuments()
                        ->where('active', true)
                        ->get();
                } catch (\Throwable $e) {
                    // ignore and fall back to visa_type rules
                    $requiredFromPackage = collect();
                }
            }

            if ($requiredFromPackage->count() > 0) {
                foreach ($requiredFromPackage as $req) {
                    $code = strtoupper($req->code);
                    if ($req->required && !$uploadedTypes->contains($code)) {
                        $pendingDocuments[] = [
                            'code' => $code,
                            'label' => $req->label,
                            'translation_possible' => (bool)$req->translation_possible,
                        ];
                    }
                }
            } else {
                // Fall back to visa_type-level required documents table if present
                try {
                    $visaType = $currentApplication->visa_type;
                    if ($visaType) {
                        $reqs = collect();
                        if (Schema::hasTable('required_documents')) {
                            $reqs = \App\Models\RequiredDocument::query()
                                ->where('visa_type', $visaType)
                                ->where('active', true)
                                ->get()
                                ->map(fn($r)=>[
                                    'code'=>strtoupper($r->code),
                                    'label'=>$r->label,
                                    'required'=>(bool)$r->required,
                                    'translation_possible'=>(bool)$r->translation_possible
                                ]);
                        }
                        // If DB has none, use config fallback
                        if ($reqs->isEmpty()) {
                            $defaults = config('required_documents.'.strtoupper($visaType), []);
                            $reqs = collect($defaults);
                        }
                        foreach ($reqs as $req) {
                            $code = strtoupper($req['code'] ?? '');
                            if (($req['required'] ?? false) && $code && !$uploadedTypes->contains($code)) {
                                $pendingDocuments[] = [
                                    'code' => $code,
                                    'label' => $req['label'] ?? $code,
                                    'translation_possible' => (bool)($req['translation_possible'] ?? false),
                                ];
                            }
                        }
                    } else {
                        // Last resort: use saved missing_documents field if present
                        $pendingDocuments = $currentApplication->missing_documents ?? [];
                    }
                } catch (\Throwable $e) {
                    // On any error, do not block the page; use legacy field
                    $pendingDocuments = $currentApplication->missing_documents ?? [];
                }
            }
        }

        return view('dashboard.applicant.documents', compact('applications', 'currentApplication', 'pendingDocuments', 'user'));
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
            
        return view('dashboard.applicant.payments', compact('payments'));
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
