<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class AttorneyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('attorney')) {
            return redirect()->route('login');
        }

        // Get applications assigned to current attorney
        $assignedCases = Application::where('attorney_id', $user->id)
            ->with(['user', 'caseManager', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get cases pending attorney review (not yet assigned)
        $pendingCases = Application::where('status', 'ready_for_attorney_review')
            ->whereNull('attorney_id')
            ->with(['user', 'caseManager'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Statistics
        $activeCases = $assignedCases->whereIn('status', [
            'assigned_to_attorney', 
            'under_attorney_review', 
            'attorney_feedback_provided'
        ])->count();
        
        $pendingReview = $assignedCases->where('status', 'assigned_to_attorney')->count();
        $approvedThisMonth = $assignedCases->where('status', 'approved')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();
        $feedbacksProvided = Feedback::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        return view('dashboard.attorney.index', compact(
            'assignedCases', 
            'pendingCases',
            'activeCases', 
            'pendingReview', 
            'approvedThisMonth', 
            'feedbacksProvided'
        ));
    }

    public function reviewCase($id)
    {
        $case = Application::with(['user', 'caseManager', 'documents', 'feedback.user', 'payments'])
            ->where('id', $id)
            ->firstOrFail();
            
        // Check if current user is assigned attorney or if case is pending assignment
        if ($case->attorney_id && $case->attorney_id != auth()->id()) {
            abort(403, 'Unauthorized access to this case.');
        }
        
        return view('dashboard.attorney.review-case', compact('case'));
    }
    
    public function acceptCase(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Check if attorney role
        if (!auth()->user()->hasRole('attorney')) {
            return back()->with('error', 'Only attorneys can accept cases.');
        }
        
        // Check if case is available for assignment
        if ($application->attorney_id && $application->attorney_id != auth()->id()) {
            return back()->with('error', 'This case is already assigned to another attorney.');
        }
        
        // Assign current user as attorney
        $application->update([
            'attorney_id' => auth()->id(),
            'status' => 'under_attorney_review'
        ]);
        
        return redirect()->route('attorney.review-case', $id)
            ->with('success', 'You have accepted this case for review.');
    }

    public function provideFeedback(Request $request, $id)
    {
        $request->validate([
            'feedback_message' => 'required|string|min:10',
            'feedback_type' => 'required|in:general,document_issue,legal_advice,rfe'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned attorney
        if ($application->attorney_id != auth()->id()) {
            return back()->with('error', 'You can only provide feedback for your assigned cases.');
        }
        
        // Create feedback entry
        $application->feedback()->create([
            'user_id' => auth()->id(),
            'message' => $request->feedback_message,
            'type' => $request->feedback_type
        ]);
        
        // Update application status based on feedback type
        $newStatus = match($request->feedback_type) {
            'rfe' => 'rfe_issued',
            'document_issue' => 'documents_required',
            default => 'attorney_feedback_provided'
        };
        
        $application->update([
            'status' => $newStatus
        ]);
        
        return back()->with('success', 'Feedback provided successfully.');
    }
    
    public function approveApplication(Request $request, $id)
    {
        $request->validate([
            'approval_notes' => 'nullable|string'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned attorney
        if ($application->attorney_id != auth()->id()) {
            return back()->with('error', 'You can only approve your assigned cases.');
        }
        
        // Update application status
        $application->update([
            'status' => 'approved'
        ]);
        
        // Add approval feedback
        $application->feedback()->create([
            'user_id' => auth()->id(),
            'message' => $request->approval_notes ?? 'Application approved by attorney.',
            'type' => 'approval'
        ]);
        
        return back()->with('success', 'Application approved successfully.');
    }
    
    public function rejectApplication(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned attorney
        if ($application->attorney_id != auth()->id()) {
            return back()->with('error', 'You can only reject your assigned cases.');
        }
        
        // Update application status
        $application->update([
            'status' => 'rejected'
        ]);
        
        // Add rejection feedback
        $application->feedback()->create([
            'user_id' => auth()->id(),
            'message' => $request->rejection_reason,
            'type' => 'rejection'
        ]);
        
        return back()->with('success', 'Application rejected with reason provided.');
    }
    
    public function requestMoreInfo(Request $request, $id)
    {
        $request->validate([
            'info_request' => 'required|string|min:10',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned attorney
        if ($application->attorney_id != auth()->id()) {
            return back()->with('error', 'You can only request information for your assigned cases.');
        }
        
        // Update missing documents if provided
        if ($request->required_documents) {
            $currentMissing = $application->missing_documents ?? [];
            $newMissing = array_unique(array_merge($currentMissing, $request->required_documents));
            
            $application->update([
                'missing_documents' => $newMissing
            ]);
        }
        
        // Update status
        $application->update([
            'status' => 'rfe_issued'
        ]);
        
        // Add RFE feedback
        $application->feedback()->create([
            'user_id' => auth()->id(),
            'message' => $request->info_request,
            'type' => 'rfe'
        ]);
        
        return back()->with('success', 'Request for Evidence (RFE) sent to applicant.');
    }
    
    public function history()
    {
        $allCases = Application::where('attorney_id', auth()->id())
            ->with(['user', 'caseManager'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('dashboard.attorney.history', compact('allCases'));
    }
    
    public function responses()
    {
        $feedbacks = Feedback::where('user_id', auth()->id())
            ->with(['application.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('dashboard.attorney.responses', compact('feedbacks'));
    }
}
