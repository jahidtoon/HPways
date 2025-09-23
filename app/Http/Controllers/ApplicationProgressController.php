<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Application;
use App\Models\Shipment;
use App\Models\TrackingEvent;

class ApplicationProgressController extends Controller
{
    private function userCanStaffAction(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasRole('admin') || $user->hasRole('case_manager') || $user->hasRole('attorney') || $user->hasRole('printing_department');
    }

    public function markPrinted(Request $request, $applicationId)
    {
        if (!$this->userCanStaffAction()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $application = Application::findOrFail($applicationId);

        // Update to printed if not already at or beyond
        if (!in_array($application->status, ['printed','ready_to_ship','shipped','delivered'])) {
            $application->status = 'printed';
        }
        $now = now();
        if (!$application->printing_started_at) {
            $application->printing_started_at = $now;
        }
        $application->printed_at = $now;
        $application->save();

        // Tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'printing_completed',
            'description' => 'Printing marked completed',
            'user_id' => auth()->id(),
            'event_time' => $now,
            'occurred_at' => $now,
        ]);

        return response()->json(['success' => true, 'message' => 'Marked as printed']);
    }

    public function markDelivered(Request $request, $applicationId)
    {
        if (!$this->userCanStaffAction()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $application = Application::findOrFail($applicationId);
        $now = now();

        // Update shipment if present
        $shipment = Shipment::where('application_id', $application->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($shipment) {
            $shipment->status = 'delivered';
            $shipment->delivered_at = $now;
            $shipment->save();
        }

        $application->status = 'delivered';
        $application->save();

        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'delivered',
            'description' => 'Delivery confirmed by staff',
            'user_id' => auth()->id(),
            'event_time' => $now,
            'occurred_at' => $now,
        ]);

        return response()->json(['success' => true, 'message' => 'Marked as delivered']);
    }

    public function confirmReceived(Request $request, $applicationId)
    {
        $application = Application::findOrFail($applicationId);
        $user = auth()->user();
        if (!$user || $user->id !== (int)$application->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only allow once
        $exists = TrackingEvent::where('application_id', $application->id)
            ->where('event_type', 'received_confirmed')
            ->exists();
        if ($exists) {
            return response()->json(['success' => true, 'message' => 'Already confirmed']);
        }

        $now = now();
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'received_confirmed',
            'description' => 'Applicant confirmed receiving documents',
            'user_id' => auth()->id(),
            'event_time' => $now,
            'occurred_at' => $now,
        ]);

        return response()->json(['success' => true, 'message' => 'Thanks for confirming receipt']);
    }
}
