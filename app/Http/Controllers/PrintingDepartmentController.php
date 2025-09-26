<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use App\Services\CarrierTrackingService;

class PrintingDepartmentController extends Controller
{
    /**
     * Printer-only application detail view (documents and status only).
     * Access control: printing_department role AND assigned_printer_id == current user.
     */
    public function viewApplication($id)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('printing_department')) {
            abort(403, 'Unauthorized');
        }

        $application = Application::with(['user', 'documents'])
            ->findOrFail($id);

        // Restrict to cases assigned to this printer
        if ((int)($application->assigned_printer_id ?? 0) !== (int)$user->id) {
            abort(403, 'This application is not assigned to you.');
        }

        // Compute required docs similar to admin detail
        $requiredDocuments = collect();
        if ($application->selected_package_id) {
            $requiredDocuments = \App\Models\PackageRequiredDocument::where('package_id', $application->selected_package_id)
                ->where('active', 1)
                ->get();
        }
        if ($requiredDocuments->isEmpty()) {
            $requiredDocuments = \App\Models\RequiredDocument::where(function($q) use ($application) {
                    $q->where('visa_type', $application->visa_type)
                      ->orWhere('visa_type', 'all');
                })
                ->where('active', 1)
                ->get();
        }

        $requiredCodes = $requiredDocuments->pluck('code')->filter()->values();
        $uploadedCodes = collect($application->documents)->pluck('type')->filter()->unique();
        $submitted = $uploadedCodes->intersect($requiredCodes)->count();
        $totalRequired = $requiredCodes->count();
        $completionPercentage = $totalRequired > 0 ? round(($submitted / $totalRequired) * 100) : 0;

        return view('dashboard.printing.application-detail', compact(
            'application',
            'requiredDocuments',
            'completionPercentage'
        ));
    }
    public function index()
    {
        // Role middleware handles authorization
        $userId = auth()->id();
        
        // Get applications in different stages
        $queueApplications = Application::where('status', 'in_print_queue')
            ->where('assigned_printer_id', $userId)
            ->with(['user', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $printingApplications = Application::where('status', 'printing')
            ->where('assigned_printer_id', $userId)
            ->with(['user', 'documents', 'assignedPrinter'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $printedApplications = Application::where('status', 'printed')
            ->where('assigned_printer_id', $userId)
            ->with(['user', 'documents', 'assignedPrinter'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $readyToShipApplications = Application::where('status', 'ready_to_ship')
            ->where('assigned_printer_id', $userId)
            ->with(['user', 'shipment'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Recent shipments for visibility (in-transit / delivered)
        $recentShipments = Shipment::with(['application.user'])
            ->whereHas('application', function($q) use ($userId) {
                $q->where('assigned_printer_id', $userId);
            })
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        // Statistics for cards
        $stats = [
            'in_queue' => $queueApplications->count(),
            'printing' => $printingApplications->count(),
            'printed' => $printedApplications->count(),
            'ready_to_ship' => $readyToShipApplications->count()
        ];

        return view('dashboard.printing.main', compact(
            'queueApplications',
            'printingApplications', 
            'printedApplications',
            'readyToShipApplications',
            'recentShipments',
            'stats'
        ));
    }

    public function documents()
    {
        $documents = Document::with(['application.user'])
            ->whereHas('application', function($query) {
                $query->whereIn('status', ['approved', 'ready_for_printing', 'in_print_queue']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('dashboard.printing.documents', compact('documents'));
    }
    
    // Back-compat route target used in some navs
    public function printQueue()
    {
        // Reuse main dashboard which already includes the queue section
        return $this->index();
    }
    
    public function management()
    {
        // Get print jobs (applications in various print stages)
        $printJobs = Application::whereIn('status', [
            'approved', 
            'ready_for_printing', 
            'in_print_queue', 
            'printing', 
            'printed_ready_to_ship'
        ])
        ->with(['user', 'documents'])
        ->orderByRaw("FIELD(status, 'printing', 'in_print_queue', 'ready_for_printing', 'approved', 'printed_ready_to_ship')")
        ->orderBy('created_at', 'asc')
        ->get();
        
        return view('dashboard.printing.management', compact('printJobs'));
    }
    
    public function shipping()
    {
        $shipments = Shipment::with(['application.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('dashboard.printing.shipping', compact('shipments'));
    }
    
    public function analytics()
    {
        // Role middleware handles authorization
        
        // Overall statistics  
        $stats = [
            'total_in_queue' => Application::where('status', 'in_print_queue')->count(),
            'currently_printing' => Application::where('status', 'printing')->count(),
            'printed_today' => Application::where('status', 'printed')
                ->whereDate('updated_at', today())->count(),
            'shipped_today' => Application::where('status', 'shipped')
                ->whereDate('updated_at', today())->count(),
            'delivered_this_week' => Application::where('status', 'delivered')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
        
        // Performance metrics
        $performance = [
            'avg_print_time' => $this->getAveragePrintTime(),
            'avg_ship_time' => $this->getAverageShipTime(),
            'completion_rate' => $this->getCompletionRate(),
            'on_time_delivery' => $this->getOnTimeDeliveryRate()
        ];
        
        // Recent activity
        $recentActivity = TrackingEvent::with(['application', 'user'])
            ->whereIn('event_type', ['printed', 'shipped', 'delivered'])
            ->orderBy('occurred_at', 'desc')
            ->limit(20)
            ->get();
        
        // Queue analysis
        $queueAnalysis = Application::where('status', 'in_print_queue')
            ->selectRaw('visa_type, COUNT(*) as count')
            ->groupBy('visa_type')
            ->get();
            
        return view('dashboard.printing.analytics', compact('stats', 'performance', 'recentActivity', 'queueAnalysis'));
    }
    
    private function getAveragePrintTime()
    {
        $applications = Application::whereNotNull('printed_at')
            ->whereNotNull('printing_started_at')
            ->get();
            
        if ($applications->isEmpty()) return 0;
        
        $totalMinutes = $applications->sum(function($app) {
            return $app->printed_at->diffInMinutes($app->printing_started_at);
        });
        
        return round($totalMinutes / $applications->count(), 2);
    }
    
    private function getAverageShipTime()
    {
        $shipments = Shipment::whereNotNull('shipped_at')
            ->whereNotNull('prepared_at')
            ->get();
            
        if ($shipments->isEmpty()) return 0;
        
        $totalHours = $shipments->sum(function($shipment) {
            return $shipment->shipped_at->diffInHours($shipment->prepared_at);
        });
        
        return round($totalHours / $shipments->count(), 2);
    }
    
    private function getCompletionRate()
    {
        $total = Application::whereIn('status', ['in_print_queue', 'printing', 'printed', 'ready_to_ship', 'shipped', 'delivered'])->count();
        $completed = Application::where('status', 'delivered')->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
    
    private function getOnTimeDeliveryRate()
    {
        $deliveredShipments = Shipment::whereNotNull('delivered_at')
            ->whereNotNull('estimated_delivery')
            ->get();
            
        if ($deliveredShipments->isEmpty()) return 0;
        
        $onTime = $deliveredShipments->filter(function($shipment) {
            return $shipment->delivered_at <= $shipment->estimated_delivery;
        })->count();
        
        return round(($onTime / $deliveredShipments->count()) * 100, 2);
    }

    public function addToPrintQueue(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Check if application is approved
        if ($application->status !== 'approved') {
            return back()->with('error', 'Only approved applications can be added to print queue.');
        }
        
        $application->update([
            'status' => 'in_print_queue',
            'assigned_printer_id' => auth()->id()
        ]);
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'queued_for_printing',
            'description' => 'Application added to print queue by ' . auth()->user()->name,
            'user_id' => auth()->id(),
            'event_time' => now(),
            'occurred_at' => now()
        ]);
        
        return back()->with('success', 'Application #' . $application->id . ' added to print queue successfully.');
    }
    
    public function autoAddToQueue()
    {
        // Automatically add all approved applications to print queue
        $approvedApplications = Application::where('status', 'approved')
            ->whereNull('assigned_printer_id')
            ->get();
            
        $addedCount = 0;
        foreach ($approvedApplications as $application) {
            $application->update([
                'status' => 'in_print_queue',
                'assigned_printer_id' => auth()->id()
            ]);
            
            TrackingEvent::create([
                'application_id' => $application->id,
                'event_type' => 'auto_queued_for_printing',
                'description' => 'Automatically added to print queue',
                'user_id' => auth()->id(),
                'occurred_at' => now()
            ]);
            
            $addedCount++;
        }
        
        return back()->with('success', $addedCount . ' applications automatically added to print queue.');
    }
    
    public function markAsPrinting(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Validate that application is in print queue
        if ($application->status !== 'in_print_queue') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be in print queue to start printing.'
            ], 422);
        }
        
        $application->update([
            'status' => 'printing',
            'assigned_printer_id' => auth()->id(),
            'printing_started_at' => now(),
        ]);
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'printing_started',
            'description' => 'Printing started by ' . auth()->user()->name,
            'user_id' => auth()->id(),
            'event_time' => now(),
            'occurred_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Application #' . $application->id . ' marked as currently printing.'
        ]);
    }
    
    public function markAsPrinted(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Validate that application is currently printing
        if ($application->status !== 'printing') {
            return response()->json([
                'success' => false,
                'message' => 'Application must be currently printing to mark as printed.'
            ], 422);
        }
        
        $application->update([
            'status' => 'printed',
            'updated_at' => now()
        ]);
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'printing_completed',
            'description' => 'Printing completed by ' . auth()->user()->name . ', ready for shipping',
            'user_id' => auth()->id(),
            'event_time' => now(),
            'occurred_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Application #' . $application->id . ' marked as printed and ready to ship.'
        ]);
    }
    
    public function prepareShipment(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'recipient_name' => 'required|string|max:255',
            'recipient_address' => 'required|string',
            'recipient_city' => 'required|string|max:255',
            'recipient_state' => 'required|string|max:50',
            'recipient_zip' => 'required|string|max:20',
            'recipient_phone' => 'nullable|string|max:20',
            'carrier' => 'required|string|max:100',
            'service' => 'required|string|max:100',
            'special_instructions' => 'nullable|string'
        ]);

        $application = Application::findOrFail($request->application_id);
        
        // Validate that application is printed
        if ($application->status !== 'printed') {
            return response()->json(['success' => false, 'message' => 'Application must be printed before preparing shipment.'], 422);
        }
        
        // Compute Lockbox address suggestion based on visa type and recipient state
        $lockbox = $this->resolveLockboxAddress(strtoupper($application->visa_type), strtoupper($request->recipient_state));
        
        // Generate tracking number
        $trackingNumber = $this->generateTrackingNumber($request->carrier);
        
        // Create shipment
        $shipment = Shipment::create([
            'application_id' => $application->id,
            'tracking_number' => $trackingNumber,
            'recipient_name' => $request->recipient_name,
            'recipient_address' => $request->recipient_address,
            'recipient_city' => $request->recipient_city,
            'recipient_state' => $request->recipient_state,
            'recipient_zip' => $request->recipient_zip,
            'recipient_phone' => $request->recipient_phone,
            'carrier' => $request->carrier,
            'service' => $request->service,
            'special_instructions' => $request->special_instructions,
            'status' => 'prepared',
            'prepared_at' => now(),
            'prepared_by' => auth()->id(),
            'estimated_delivery' => now()->addDays($this->getEstimatedDeliveryDays($request->service))
        ]);
        
        $application->update(['status' => 'ready_to_ship']);
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'shipment_prepared',
            'description' => 'Shipment prepared for delivery. Tracking: ' . $trackingNumber,
            'user_id' => auth()->id(),
            'event_time' => now(),
            'occurred_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'tracking_number' => $trackingNumber,
            'shipment_id' => $shipment->id,
            'lockbox' => $lockbox
        ]);
    }
    
    private function generateTrackingNumber($carrier)
    {
        $prefix = strtoupper(substr($carrier, 0, 3));
        $timestamp = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }
    
    private function getEstimatedDeliveryDays($service)
    {
        $deliveryDays = [
            'overnight' => 1,
            'express' => 2,
            'standard' => 5,
            'ground' => 7,
            'economy' => 10
        ];
        
        $serviceKey = strtolower($service);
        foreach ($deliveryDays as $key => $days) {
            if (strpos($serviceKey, $key) !== false) {
                return $days;
            }
        }
        
        return 5; // Default to 5 days
    }

    private function resolveLockboxAddress($visaType, $state)
    {
        $config = config('lockbox');
        if (!isset($config[$visaType])) return null;
        $entry = $config[$visaType];
        $groups = $entry['groups'] ?? [];
        // Direct state group like 'TX'
        if (isset($groups[$state])) return $groups[$state];
        // ALL
        if (isset($groups['ALL'])) return $groups['ALL'];
        // OUTSIDE_US
        if ($state === 'OUTSIDE_US' && isset($groups['OUTSIDE_US'])) return $groups['OUTSIDE_US'];
        // Find by states array
        foreach ($groups as $g) {
            if (isset($g['states']) && in_array($state, $g['states'])) return $g;
        }
        return null;
    }
    
    public function ship(Request $request, $shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $application = $shipment->application;
        
        // Validate that shipment is ready to ship
        if ($shipment->status !== 'prepared') {
            return back()->with('error', 'Shipment must be prepared before shipping.');
        }
        
        $request->validate([
            'tracking_number' => 'nullable|string|max:100',
            'actual_carrier' => 'nullable|string|max:100',
            'actual_service' => 'nullable|string|max:100',
            'shipped_at' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        $shipment->update([
            'tracking_number' => $request->tracking_number ?: $shipment->tracking_number,
            'actual_carrier' => $request->actual_carrier ?: $shipment->carrier,
            'actual_service' => $request->actual_service ?: $shipment->service,
            'status' => 'shipped',
            'shipped_at' => $request->shipped_at,
            'shipped_by' => auth()->id(),
            'shipping_notes' => $request->notes
        ]);
        
        $application->update(['status' => 'shipped']);
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'shipped',
            'description' => 'Package shipped with tracking number: ' . $shipment->tracking_number . 
                           ' via ' . ($request->actual_carrier ?: $shipment->carrier),
            'user_id' => auth()->id(),
            'event_time' => now(),
            'occurred_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Package shipped successfully. Tracking: ' . $shipment->tracking_number
        ]);
    }
    
    public function updateTrackingStatus(Request $request, $shipmentId)
    {
        $request->validate([
            'status' => 'required|string|in:in_transit,out_for_delivery,delivered,exception,returned',
            'location' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'event_date' => 'nullable|date'
        ]);
        
        $shipment = Shipment::findOrFail($shipmentId);
        $application = $shipment->application;
        
        // Create tracking event
        TrackingEvent::create([
            'application_id' => $application->id,
            'event_type' => 'tracking_update',
            'description' => $request->description,
            'user_id' => auth()->id(),
            'event_time' => $request->event_date ? $request->event_date : now(),
            'occurred_at' => $request->event_date ? $request->event_date : now(),
            'metadata' => json_encode([
                'tracking_status' => $request->status,
                'location' => $request->location,
                'shipment_id' => $shipment->id
            ])
        ]);
        
        // Update shipment status
        $shipment->update([
            'status' => $request->status,
            'last_tracking_update' => now()
        ]);
        
        // Update application status based on tracking status
        if ($request->status == 'delivered') {
            $application->update(['status' => 'delivered']);
            
            $shipment->update([
                'delivered_at' => $request->event_date ?: now()
            ]);
            
            // Create delivery confirmation event
            TrackingEvent::create([
                'application_id' => $application->id,
                'event_type' => 'delivered',
                'description' => 'Package delivered successfully',
                'user_id' => auth()->id(),
                'event_time' => now(),
                'occurred_at' => now()
            ]);
        } elseif ($request->status == 'exception') {
            // Create exception event
            TrackingEvent::create([
                'application_id' => $application->id,
                'event_type' => 'shipping_exception',
                'description' => 'Shipping exception: ' . $request->description,
                'user_id' => auth()->id(),
                'event_time' => now(),
                'occurred_at' => now()
            ]);
        }
        
        return back()->with('success', 'Tracking status updated successfully.');
    }

    /**
     * Return shipment tracking timeline as JSON for modal rendering.
     */
    public function tracking($shipmentId)
    {
        $shipment = Shipment::with('application')->findOrFail($shipmentId);
        // Prefer events by application_id as most events are recorded that way
        $events = TrackingEvent::where('application_id', $shipment->application_id)
            ->orderBy('event_time', 'desc')
            ->get(['event_type','description','location','event_time','occurred_at','metadata']);
        return response()->json([
            'success' => true,
            'shipment' => [
                'id' => $shipment->id,
                'tracking_number' => $shipment->tracking_number,
                'carrier' => $shipment->actual_carrier ?: $shipment->carrier,
                'status' => $shipment->status,
                'shipped_at' => optional($shipment->shipped_at)->toDateTimeString(),
                'delivered_at' => optional($shipment->delivered_at)->toDateTimeString(),
            ],
            'events' => $events,
        ]);
    }

    /**
     * Manually refresh tracking status for a specific shipment (printing staff only).
     */
    public function refreshTracking(Shipment $shipment, CarrierTrackingService $service)
    {
        // Optional: authorize role
        $user = auth()->user();
        if (!$user || (!$user->hasRole('printing_department') && !$user->hasRole('admin'))) {
            abort(403);
        }

        $summary = $service->fetchAndUpdate($shipment);
        return response()->json([
            'success' => true,
            'status' => $summary['status'] ?? $shipment->status,
            'events_added' => count($summary['events'] ?? []),
        ]);
    }
    
    public function autoAddApproved()
    {
        // Role middleware handles authorization
        
        // Find all approved applications assigned to the current printer
        // This prevents pulling in other printers' work
        $approvedApplications = Application::where('status', 'approved')
            ->where('assigned_printer_id', auth()->id())
            ->get();
        
        $count = 0;
        foreach ($approvedApplications as $application) {
            // Move only if not already queued/printing/printed
            if (!in_array($application->status, ['in_print_queue','printing','printed','ready_to_ship','shipped','delivered'])) {
                $application->update(['status' => 'in_print_queue']);
            }
            
            // Create tracking event
            TrackingEvent::create([
                'application_id' => $application->id,
                'event_type' => 'added_to_print_queue',
                'description' => 'Application automatically added to print queue',
                'user_id' => auth()->id(),
                'event_time' => now(),
                'occurred_at' => now()
            ]);
            
            $count++;
        }
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "Added {$count} approved applications to print queue"
        ]);
    }

    /**
     * Move all applications assigned to the current printer into the print queue
     * if they are not already in the print/shipping pipeline.
     */
    public function syncAssignedToQueue(Request $request)
    {
        $userId = auth()->id();
        $pipeline = ['in_print_queue','printing','printed','ready_to_ship','shipped','delivered'];
        $apps = Application::where('assigned_printer_id', $userId)
            ->whereNotIn('status', $pipeline)
            ->get();

        $count = 0;
        foreach ($apps as $application) {
            $application->update(['status' => 'in_print_queue']);
            // Log event
            TrackingEvent::create([
                'application_id' => $application->id,
                'event_type' => 'queued_for_printing',
                'description' => 'Assigned to printer and moved to print queue',
                'user_id' => $userId,
                'event_time' => now(),
                'occurred_at' => now(),
            ]);
            $count++;
        }

        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "$count application(s) synced to print queue"
        ]);
    }
    


    public function bulkPrint(Request $request)
    {
        $request->validate([
            'applications' => 'required|array',
            'applications.*' => 'exists:applications,id'
        ]);
        
        $applications = Application::whereIn('id', $request->applications)
            ->whereIn('status', ['approved', 'ready_for_printing', 'in_print_queue'])
            ->get();
            
        $count = 0;
        foreach ($applications as $application) {
            $application->update([
                'status' => 'printing',
                'assigned_printer_id' => auth()->id(),
            ]);
            
            // Create tracking event
            $this->createTrackingEvent(
                $application->id,
                'printing_started',
                'Bulk printing started',
                ['started_by' => auth()->user()->name]
            );
            
            $count++;
        }
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "$count applications started printing"
        ]);
    }
    
    /**
     * Helper method to create tracking events with required fields
     */
    private function createTrackingEvent($applicationId, $eventType, $description, $metadata = null)
    {
        $now = now();
        return TrackingEvent::create([
            'application_id' => $applicationId,
            'event_type' => $eventType,
            'description' => $description,
            'user_id' => auth()->id(),
            'event_time' => $now,
            'occurred_at' => $now,
            'metadata' => $metadata
        ]);
    }
}
