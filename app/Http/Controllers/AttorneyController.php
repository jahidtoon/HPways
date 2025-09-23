<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;
use App\Models\RequiredDocument;
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
        $feedbacksProvided = Feedback::where('attorney_id', $user->id)
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

    public function cases()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('attorney')) {
            return redirect()->route('login');
        }

        $assignedCases = Application::where('attorney_id', $user->id)
            ->with(['user', 'caseManager', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.attorney.cases', compact('assignedCases'));
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

        // Build document requirements status from config and uploaded documents
        // Normalize visa type to canonical codes like I751, I130, etc.
        $visa = $this->normalizeVisaType($case->visa_type);
        $requiredDocs = config('required_documents.' . ($visa ?? ''), []);

        // Fallback to DB-driven required documents if config has none
        if (empty($requiredDocs)) {
            $requiredDocs = RequiredDocument::where('visa_type', $visa)
                ->where('active', true)
                ->get()
                ->map(function ($row) {
                    return [
                        'code' => $row->code,
                        'label' => $row->label,
                        'required' => (bool) $row->required,
                        'translation_possible' => (bool) $row->translation_possible,
                    ];
                })->all();
        }
        $documentStatus = [];
        $uploadedByType = $case->documents->keyBy(function ($doc) {
            return strtoupper(preg_replace('/[^A-Z0-9_]/', '', (string) $doc->type));
        });
        foreach ($requiredDocs as $reqDoc) {
            $code = $reqDoc['code'] ?? null;
            if (!$code) { continue; }
            $normalizedCode = strtoupper(preg_replace('/[^A-Z0-9_]/', '', (string) $code));
            $uploaded = $uploadedByType->get($normalizedCode);
            $documentStatus[] = [
                'code' => $normalizedCode,
                'label' => $reqDoc['label'] ?? $code,
                'required' => (bool)($reqDoc['required'] ?? false),
                'uploaded_document' => $uploaded,
                'status' => $uploaded?->status ?? 'missing',
                'uploaded' => (bool)$uploaded,
            ];
        }

        // Friendly display fields used by the Blade
        $case->applicant_name = $case->user->name ?? 'Unknown Applicant';
        $case->submitted_at = optional($case->created_at)->format('Y-m-d');
        $case->notes = $case->notes ?? '';

        return view('dashboard.attorney.review-case', compact('case', 'documentStatus'));
    }

    /**
     * Normalize free-form visa type strings (e.g., "I-751", "Form I 130")
     * into canonical codes expected by config/visas.php (e.g., "I751", "I130").
     */
    private function normalizeVisaType(?string $visa): ?string
    {
        if (!$visa) return null;
        $norm = strtoupper($visa);
        // Remove common prefixes and non-alphanumerics
        $norm = preg_replace('/\bFORM\b/', '', $norm);
        $norm = preg_replace('/[^A-Z0-9]/', '', $norm);
        // Some known aliases
        $aliases = [
            'I751' => ['I751', 'I-751', 'FORMI751'],
            'I130' => ['I130', 'I-130', 'FORMI130'],
            'I485' => ['I485', 'I-485', 'FORMI485', 'AOS'],
            'I90'  => ['I90', 'I-90', 'FORMI90'],
            'K1'   => ['K1', 'K-1', 'FIANCEVISA'],
            'DACA' => ['DACA'],
            'N400' => ['N400', 'N-400', 'FORMN400'],
        ];
        foreach ($aliases as $canon => $variants) {
            foreach ($variants as $v) {
                if ($norm === preg_replace('/[^A-Z0-9]/', '', $v)) {
                    return $canon;
                }
            }
        }
        // If already clean alphanumeric, return as-is
        return $norm;
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
        
    return redirect()->route('dashboard.attorney.case.review', $id)
            ->with('success', 'You have accepted this case for review.');
    }

    public function provideFeedback(Request $request, $id)
    {
        // Debug logging
        \Log::info('Feedback submission attempt', [
            'case_id' => $id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'feedback_message' => 'required|string|min:10',
            'feedback_type' => 'required|in:general,document_issue,legal_advice,rfe'
        ]);
        
        $application = Application::findOrFail($id);
        
        // Check if current user is the assigned attorney
        if ($application->attorney_id != auth()->id()) {
            \Log::warning('Unauthorized feedback attempt', [
                'case_id' => $id,
                'case_attorney_id' => $application->attorney_id,
                'current_user_id' => auth()->id()
            ]);
            return back()->with('error', 'You can only provide feedback for your assigned cases.');
        }
        
        // Create feedback entry
        $feedback = $application->feedback()->create([
            'attorney_id' => auth()->id(),
            'content' => $request->feedback_message,
            'type' => $request->feedback_type
        ]);
        
        \Log::info('Feedback created successfully', [
            'feedback_id' => $feedback->id,
            'case_id' => $id,
            'attorney_id' => auth()->id()
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
            'attorney_id' => auth()->id(),
            'content' => $request->approval_notes ?? 'Application approved by attorney.',
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
            'attorney_id' => auth()->id(),
            'content' => $request->rejection_reason,
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
            'attorney_id' => auth()->id(),
            'content' => $request->info_request,
            'requested_documents' => $request->required_documents,
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
        $feedbacks = Feedback::where('attorney_id', auth()->id())
            ->with(['application.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('dashboard.attorney.responses', compact('feedbacks'));
    }
}
