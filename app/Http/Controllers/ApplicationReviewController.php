<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Feedback;
use App\Services\ApplicationStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationReviewController extends Controller
{
    public function requestReview(Application $application, ApplicationStatusService $svc)
    {
        $this->authorizeApplicant($application);
        if($application->status !== 'draft'){
            return response()->json(['error'=>'Cannot request review from current status'],422);
        }
        $svc->transition($application,'intake_complete');
        return response()->json(['status'=>$application->status,'progress_pct'=>$application->progress_pct]);
    }

    public function startReview(Application $application, ApplicationStatusService $svc)
    {
        $this->authorizeStaff();
        if(!in_array($application->status,['intake_complete','waiting_applicant'],true)){
            return response()->json(['error'=>'Cannot start review'],422);
        }
        $svc->transition($application,'under_review');
        return response()->json(['status'=>$application->status]);
    }

    public function issueRfe(Application $application, Request $request, ApplicationStatusService $svc)
    {
        $this->authorizeStaff();
        if($application->status !== 'under_review'){
            return response()->json(['error'=>'Not reviewing'],422);
        }
        $data = $request->validate([
            'content' => ['required','string','min:5'],
            'requested_documents' => ['array'],
            'requested_documents.*' => ['string']
        ]);
        Feedback::create([
            'application_id' => $application->id,
            'attorney_id' => Auth::id(),
            'type' => 'rfe',
            'content' => $data['content'],
            'requested_documents' => $data['requested_documents'] ?? [],
        ]);
        $svc->transition($application,'rfe_needed');
        return response()->json(['status'=>$application->status]);
    }

    public function applicantRespond(Application $application, ApplicationStatusService $svc)
    {
        $this->authorizeApplicant($application);
        if($application->status !== 'rfe_needed'){
            return response()->json(['error'=>'No RFE outstanding'],422);
        }
        $svc->transition($application,'waiting_applicant'); // moves to waiting_applicant for internal re-review
        return response()->json(['status'=>$application->status]);
    }

    public function markReadyToFile(Application $application, ApplicationStatusService $svc)
    {
        $this->authorizeStaff();
        if(!in_array($application->status,['under_review','waiting_applicant'],true)){
            return response()->json(['error'=>'Cannot mark ready'],422);
        }
        $svc->transition($application,'ready_to_file');
        return response()->json(['status'=>$application->status]);
    }

    public function markFiled(Application $application, ApplicationStatusService $svc)
    {
        $this->authorizeStaff();
        if($application->status !== 'ready_to_file'){
            return response()->json(['error'=>'Not ready to file'],422);
        }
        $svc->transition($application,'filed');
        return response()->json(['status'=>$application->status]);
    }

    protected function authorizeApplicant(Application $application): void
    {
        $user = Auth::user();
        if(!$user || $application->user_id !== $user->id){ abort(403); }
    }

    protected function authorizeStaff(): void
    {
        $user = Auth::user();
        if(!$user || !$user->hasAnyRole(['admin','attorney','case_manager'])){ abort(403); }
    }
}
