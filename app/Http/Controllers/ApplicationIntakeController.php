<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\VisaTypeMapper;
use App\Services\StaffAssignmentService;

class ApplicationIntakeController extends Controller
{
    /**
     * Create (or reuse) an application from quiz terminal result.
     * Expects: terminal_code (string), history (array of {node,choice,timestamp})
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'terminal_code' => ['required','string','max:120'],
            'history' => ['required','array','min:1'],
            'history.*.node' => ['required'],
            'history.*.choice' => ['required','string','max:40'],
            'history.*.timestamp' => ['required','string'],
        ]);

        $visaType = $this->mapTerminalToVisa($data['terminal_code']);
        if (!$visaType) {
            return response()->json(['error' => 'Terminal not mappable to application'], 422);
        }

        // Reuse draft if same visa_type exists for this user
        $application = Application::where('user_id',$user->id)
            ->where('visa_type',$visaType)
            ->where('status','draft')
            ->first();

        if (!$application) {
            $application = DB::transaction(function() use ($user,$visaType,$data){
                $app = Application::create([
                    'user_id' => $user->id,
                    'visa_type' => $visaType,
                    'status' => 'draft',
                    'progress_pct' => 5, // initial
                    'payment_status' => 'unpaid',
                    'intake_history' => $data['history'],
                ]);

                // Auto-assign case manager
                StaffAssignmentService::assignCaseManager($app);

                return $app;
            });
        } else {
            $application->update(['intake_history' => $data['history']]);
        }

        return response()->json([
            'created' => (bool) $application->wasRecentlyCreated,
            'application_id' => $application->id,
            'visa_type' => $application->visa_type,
            'status' => $application->status,
            'next' => route('dashboard'),
        ]);
    }

    protected function mapTerminalToVisa(string $terminal): ?string
    {
    return VisaTypeMapper::map($terminal);
    }
}
