<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingsController extends Controller
{
    // Applicant meetings list
    public function applicantIndex(Request $request)
    {
        $user = Auth::user();
        $this->authorizeRole(['applicant', 'user']);

        $meetings = Meeting::with(['application', 'attorney', 'caseManager'])
            ->where('applicant_id', $user->id)
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.applicant.meetings.index', compact('meetings'));
    }

    // Attorney meetings list
    public function attorneyIndex(Request $request)
    {
        $user = Auth::user();
        $this->authorizeRole(['attorney']);

        $meetings = Meeting::with(['application', 'applicant', 'caseManager'])
            ->where('attorney_id', $user->id)
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.attorney.meetings.index', compact('meetings'));
    }

    // Case Manager meetings list
    public function caseManagerIndex(Request $request)
    {
        $user = Auth::user();
        $this->authorizeRole(['case_manager']);

        $meetings = Meeting::with(['application', 'applicant', 'attorney'])
            ->where('case_manager_id', $user->id)
            ->orWhereHas('application', function ($q) use ($user) {
                $q->where('case_manager_id', $user->id);
            })
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.case-manager.meetings.index', compact('meetings'));
    }

    // Admin meetings list
    public function adminIndex(Request $request)
    {
        $this->authorizeRole(['admin', 'big_admin']);

        $meetings = Meeting::with(['application', 'applicant', 'attorney', 'caseManager'])
            ->orderByDesc('scheduled_for')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dashboard.admin.meetings.index', compact('meetings'));
    }

    // Attorney requests a meeting for a case
    public function storeRequest(Request $request, $id)
    {
        $this->authorizeRole(['attorney']);
        $request->validate([
            'topic' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $attorney = Auth::user();
        $application = Application::with('user')
            ->where('id', $id)
            ->where('attorney_id', $attorney->id)
            ->firstOrFail();

        $meeting = Meeting::create([
            'application_id' => $application->id,
            'attorney_id' => $attorney->id,
            'applicant_id' => $application->user_id,
            'case_manager_id' => $application->case_manager_id,
            'status' => Meeting::STATUS_REQUESTED,
            'requested_by' => 'attorney',
            'topic' => $request->input('topic'),
            'notes' => $request->input('notes'),
            'provider' => 'zoom',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Meeting request sent to case manager.');
    }

    // Case manager opens schedule form
    public function editSchedule($meetingId)
    {
        $this->authorizeRole(['case_manager']);
        $meeting = Meeting::with(['application', 'applicant', 'attorney'])
            ->findOrFail($meetingId);

        $this->authorizeCaseManagerForMeeting($meeting);

        return view('dashboard.case-manager.meetings.schedule', compact('meeting'));
    }

    // Case manager schedules/approves meeting
    public function updateSchedule(Request $request, $meetingId)
    {
        $this->authorizeRole(['case_manager']);
        $request->validate([
            'scheduled_for' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:240',
            'join_url' => 'nullable|url|max:2048',
            'start_url' => 'nullable|url|max:2048',
            'provider' => 'nullable|string|max:64',
        ]);

        $meeting = Meeting::findOrFail($meetingId);
        $this->authorizeCaseManagerForMeeting($meeting);

        $meeting->update([
            'scheduled_for' => $request->scheduled_for,
            'duration_minutes' => $request->duration_minutes,
            'join_url' => $request->join_url,
            'start_url' => $request->start_url,
            'provider' => $request->provider ?? 'zoom',
            'status' => Meeting::STATUS_SCHEDULED,
        ]);

        return redirect()->route('dashboard.case-manager.meetings.index')->with('success', 'Meeting scheduled successfully.');
    }

    public function approve($meetingId)
    {
        $this->authorizeRole(['case_manager']);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorizeCaseManagerForMeeting($meeting);
        $meeting->update(['status' => Meeting::STATUS_APPROVED]);
        return back()->with('success', 'Meeting approved.');
    }

    public function decline($meetingId)
    {
        $this->authorizeRole(['case_manager']);
        $meeting = Meeting::findOrFail($meetingId);
        $this->authorizeCaseManagerForMeeting($meeting);
        $meeting->update(['status' => Meeting::STATUS_DECLINED]);
        return back()->with('success', 'Meeting declined.');
    }

    public function cancel($meetingId)
    {
        $user = Auth::user();
        $meeting = Meeting::findOrFail($meetingId);

        // Allow cancel by case manager on the case, the assigned attorney, or the applicant
        $canCancel = false;
        if ($user->hasRole('case_manager')) {
            $canCancel = ($meeting->case_manager_id === $user->id) || ($meeting->application && $meeting->application->case_manager_id === $user->id);
        }
        if ($user->hasRole('attorney')) {
            $canCancel = $canCancel || ($meeting->attorney_id === $user->id);
        }
        if ($user->hasRole('applicant') || !$user->getRoleNames()->count()) {
            $canCancel = $canCancel || ($meeting->applicant_id === $user->id);
        }
        if (!$canCancel) abort(403);

        $meeting->update(['status' => Meeting::STATUS_CANCELED]);
        return back()->with('success', 'Meeting canceled.');
    }

    private function authorizeRole(array $roles)
    {
        $user = Auth::user();
        foreach ($roles as $role) {
            if ($user->hasRole($role)) return true;
        }
        // treat applicants as default role when no explicit role
        if (in_array('applicant', $roles) && $user && $user->getRoleNames()->isEmpty()) return true;
        abort(403);
    }

    private function authorizeCaseManagerForMeeting(Meeting $meeting)
    {
        $user = Auth::user();
        if (!$user->hasRole('case_manager')) abort(403);
        if ($meeting->case_manager_id && $meeting->case_manager_id !== $user->id) abort(403);
        if ($meeting->application && $meeting->application->case_manager_id && $meeting->application->case_manager_id !== $user->id) abort(403);
    }
}
