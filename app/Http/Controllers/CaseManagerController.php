<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
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

        // Get applications assigned to current case manager
        $assignedCases = Application::where('case_manager_id', $user->id)
            ->with(['user', 'attorney', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get unassigned applications that need case manager
        $unassignedCases = Application::whereNull('case_manager_id')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Statistics
        $totalCases = $assignedCases->count();
        $documentsReady = $assignedCases->filter(function($case) {
            return empty($case->missing_documents);
        })->count();
        $pendingReview = $assignedCases->whereIn('status', ['pending_review', 'pending_attorney_review'])->count();
        $needsAttention = $assignedCases->where('status', 'rfe_issued')->count();

        return view('dashboard.case-manager.index', compact(
            'assignedCases', 
            'unassignedCases', 
            'totalCases', 
            'documentsReady', 
            'pendingReview', 
            'needsAttention'
        ));
    }

    public function applications()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('case_manager')) {
            return redirect()->route('login');
        }

        // Get all applications that this case manager can see
        $applications = Application::where(function($query) use ($user) {
                $query->where('case_manager_id', $user->id)
                      ->orWhereNull('case_manager_id');
            })
            ->with(['user', 'attorney', 'documents', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.case-manager.applications', compact('applications'));
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
        $case = Application::with(['user', 'attorney', 'documents', 'feedback', 'payments'])
            ->where('id', $id)
            ->firstOrFail();
            
        // Check if current user is assigned case manager
        if ($case->case_manager_id && $case->case_manager_id != auth()->id()) {
            abort(403, 'Unauthorized access to this case.');
        }
        
        // Get available attorneys for assignment
        $availableAttorneys = User::role('attorney')->get();
        
        return view('dashboard.case-manager.view-case', compact('case', 'availableAttorneys'));
    }
    
    public function assignSelf(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Check if case manager role
        if (!auth()->user()->hasRole('case-manager')) {
            return back()->with('error', 'Only case managers can assign themselves to cases.');
        }
        
        // Assign current user as case manager
        $application->update([
            'case_manager_id' => auth()->id(),
            'status' => 'under_case_manager_review'
        ]);
        
        return redirect()->route('case-manager.view-case', $id)
            ->with('success', 'You have been assigned as case manager for this application.');
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
