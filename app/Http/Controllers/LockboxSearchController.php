<?php

namespace App\Http\Controllers;

use App\Services\LockboxService;
use Illuminate\Http\Request;

class LockboxSearchController extends Controller
{
    protected $lockboxService;

    public function __construct(LockboxService $lockboxService)
    {
        $this->lockboxService = $lockboxService;
    }

    public function index()
    {
        $states = $this->lockboxService->getAllStates();
        $visaTypes = $this->lockboxService->getSupportedVisaTypes();
        
        return view('printing.lockbox-search', compact('states', 'visaTypes'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'visa_type' => 'required|string',
            'state' => 'required|string'
        ]);

        $result = $this->lockboxService->findLockbox(
            $request->visa_type,
            $request->state
        );

        if (!$result) {
            return response()->json([
                'error' => 'No lockbox found for the selected visa type and state combination.'
            ], 404);
        }

        return response()->json($result);
    }
}
