<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class CaseManagerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Get ONLY applications assigned to current case manager - NO unassigned cases for Case Managers
        $assignedCases = $user->managedCases()
            ->with(['user', 'attorney', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Statistics - only for assigned cases
        $totalCases = $assignedCases->count();
        $documentsReady = $assignedCases->filter(function($case) {
            return $case->documents->count() > 0 && empty($case->missing_documents);
        })->count();
        $pendingReview = $assignedCases->whereIn('status', ['pending_review', 'pending_attorney_review'])->count();
        $needsAttention = $assignedCases->where('status', 'rfe_issued')->count();
        $awaitingAttorney = $assignedCases->whereNull('attorney_id')->count();

        // Get available attorneys for assignment
        $availableAttorneys = User::role('attorney')->get();

        return view('dashboard.case-manager.dashboard', compact(
            'assignedCases', 
            'totalCases', 
            'documentsReady', 
            'pendingReview', 
            'needsAttention',
            'awaitingAttorney',
            'availableAttorneys'
        ));
    }

    public function applications()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Case Manager can ONLY see applications assigned to them
        $applications = $user->managedCases()
            ->with(['user', 'attorney', 'documents', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.case-manager.applications', compact('applications'));
    }

    public function allApplications(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Build query with filters
        $query = Application::with(['user', 'attorney', 'documents', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by case manager
        if ($request->filled('case_manager')) {
            if ($request->case_manager === 'unassigned') {
                $query->whereNull('case_manager_id');
            } else {
                $query->where('case_manager_id', $request->case_manager);
            }
        }

        // Filter by attorney
        if ($request->filled('attorney')) {
            if ($request->attorney === 'unassigned') {
                $query->whereNull('attorney_id');
            } else {
                $query->where('attorney_id', $request->attorney);
            }
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get available case managers and attorneys for assignment
        $caseManagers = User::role('case_manager')->get();
        $attorneys = User::role('attorney')->get();

        return view('dashboard.case-manager.all-applications', compact('applications', 'caseManagers', 'attorneys'));
    }

    public function attorneys()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Get all attorneys
        $attorneys = User::role('attorney')->with(['applications' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        return view('dashboard.case-manager.attorneys', compact('attorneys'));
    }

    public function viewCase($id)
    {
        // Case Manager can ONLY view cases assigned to them
        $case = Application::with(['user', 'attorney', 'documents', 'feedback', 'payments', 'selectedPackage'])
            ->where('id', $id)
            ->where('case_manager_id', auth()->id()) // Strict check - only assigned cases
            ->firstOrFail();
        
        // Get available attorneys for assignment
        $availableAttorneys = User::role('attorney')->get();
        
        // Build document status like ApplicantController does
        $uploadedTypes = $case->documents()->pluck('type')->filter()->map(fn($t)=>strtoupper($t))->unique();
        $required = [];
        $optional = [];

        // Package-specific first
        if (\Illuminate\Support\Facades\Schema::hasTable('package_required_documents') && $case->selectedPackage) {
            try {
                $rows = $case->selectedPackage->requiredDocuments()->where('active',true)->get();
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
            $visa = $case->visa_type;
            $rows = collect();
            if ($visa && \Illuminate\Support\Facades\Schema::hasTable('required_documents')) {
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
        
        return view('dashboard.case-manager.view-case', compact('case', 'availableAttorneys', 'required', 'optional'));
    }
    
    public function assignSelf($id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        $application = Application::findOrFail($id);
        $application->case_manager_id = $user->id;
        $application->status = 'under_case_manager_review';
        $application->save();

        return redirect()->back()->with('success', 'Case assigned to you successfully!');
    }

    public function assignCaseManager(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        $request->validate([
            'case_manager_id' => 'required|exists:users,id'
        ]);

        $application = Application::findOrFail($id);
        $caseManager = User::findOrFail($request->case_manager_id);
        
        // Verify the selected user is actually a case manager
        if (!$caseManager->hasRole('case_manager')) {
            return redirect()->back()->with('error', 'Selected user is not a case manager.');
        }

        $application->case_manager_id = $request->case_manager_id;
        $application->status = 'under_case_manager_review';
        $application->save();

        return redirect()->back()->with('success', "Case assigned to {$caseManager->name} successfully!");
    }

    public function reports()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Generate reports data
        $totalApplications = Application::count();
        $myApplications = $user->managedCases()->count();
        $approvedApplications = Application::where('status', 'approved')->count();
        $pendingApplications = Application::whereIn('status', ['under_case_manager_review', 'under_attorney_review'])->count();
        
        $monthlyStats = Application::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        return view('dashboard.case-manager.reports', compact(
            'totalApplications', 'myApplications', 'approvedApplications', 
            'pendingApplications', 'monthlyStats'
        ));
    }

    public function analytics()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Analytics data
        $statusDistribution = Application::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        $applicationsByManager = Application::join('users', 'applications.case_manager_id', '=', 'users.id')
            ->selectRaw('users.name, COUNT(*) as count')
            ->groupBy('users.name')
            ->get();

        return view('dashboard.case-manager.analytics', compact('statusDistribution', 'applicationsByManager'));
    }

    public function documents()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Get documents from assigned cases
        $documents = Document::whereHas('application', function($query) use ($user) {
            $query->where('case_manager_id', $user->id);
        })->with(['application.user'])->paginate(20);

        return view('dashboard.case-manager.documents', compact('documents'));
    }

    public function notifications()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Mock notifications - you can implement actual notification system later
        $notifications = [
            ['type' => 'new_application', 'message' => 'New application assigned to you', 'time' => '2 hours ago'],
            ['type' => 'document_uploaded', 'message' => 'Document uploaded for Case #123', 'time' => '5 hours ago'],
            ['type' => 'attorney_assigned', 'message' => 'Attorney assigned to Case #456', 'time' => '1 day ago'],
        ];

        return view('dashboard.case-manager.notifications', compact('notifications'));
    }

    public function settings()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        return view('dashboard.case-manager.settings', compact('user'));
    }

    public function assignAttorney(Request $request, $id)
    {
        $request->validate([
            'attorney_id' => 'required|exists:users,id'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned case manager
        if ($application->case_manager_id != auth()->id()) {
            return back()->with('error', 'You can only assign attorneys to your own cases.');
        }
        
        // Verify the selected user is an attorney
        $attorney = User::findOrFail($request->attorney_id);
        if (!$attorney->hasRole('attorney')) {
            return back()->with('error', 'Selected user is not an attorney.');
        }
        
        // Assign attorney
        $application->update([
            'attorney_id' => $request->attorney_id,
            'status' => 'assigned_to_attorney'
        ]);
        
        return back()->with('success', 'Attorney assigned successfully.');
    }
    
    public function requestDocuments(Request $request, $id)
    {
        $request->validate([
            'document_list' => 'required|array',
            'document_list.*' => 'string',
            'message' => 'nullable|string'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned case manager
        if ($application->case_manager_id != auth()->id()) {
            return back()->with('error', 'You can only request documents for your own cases.');
        }
        
        // Update missing documents
        $application->update([
            'missing_documents' => $request->document_list,
            'status' => 'documents_requested'
        ]);
        
        // Add feedback entry
        $application->feedback()->create([
            'user_id' => auth()->id(),
            'message' => $request->message ?? 'Additional documents requested.',
            'type' => 'document_request'
        ]);
        
        return back()->with('success', 'Document request sent to applicant.');
    }
    
    public function markReady(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned case manager
        if ($application->case_manager_id != auth()->id()) {
            return back()->with('error', 'You can only mark your own cases as ready.');
        }
        
        $application->update([
            'status' => 'ready_for_attorney_review'
        ]);
        
        return back()->with('success', 'Case marked as ready for attorney review.');
    }
}
