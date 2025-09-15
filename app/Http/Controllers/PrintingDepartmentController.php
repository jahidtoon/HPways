<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class PrintingDepartmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('printing_department')) {
            return redirect()->route('login');
        }

        // Get applications ready for printing
        $readyForPrinting = Application::whereIn('status', ['approved', 'ready_for_printing'])
            ->with(['user', 'documents'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Get applications in print queue
        $printQueue = Application::where('status', 'in_print_queue')
            ->with(['user', 'documents'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Get applications ready for shipping
        $readyForShipping = Application::where('status', 'printed_ready_to_ship')
            ->with(['user', 'documents'])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Get active shipments
        $activeShipments = Shipment::whereNotIn('status', ['delivered', 'cancelled'])
            ->with(['application.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $totalInQueue = $printQueue->count();
        $totalReadyToPrint = $readyForPrinting->count();
        $totalReadyToShip = $readyForShipping->count();
        $totalActiveShipments = $activeShipments->count();

        return view('dashboard.printing.index', compact(
            'readyForPrinting',
            'printQueue', 
            'readyForShipping',
            'activeShipments',
            'totalInQueue', 
            'totalReadyToPrint', 
            'totalReadyToShip', 
            'totalActiveShipments'
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
        // Get printing analytics data
        $printStats = [
            'documents_printed_today' => Application::where('status', 'printed_ready_to_ship')
                ->whereDate('updated_at', today())
                ->count(),
            'documents_printed_this_week' => Application::where('status', 'printed_ready_to_ship')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'documents_printed_this_month' => Application::where('status', 'printed_ready_to_ship')
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'average_print_time' => '2.5 hours', // This would be calculated from actual data
        ];
        
        $shippingStats = [
            'packages_shipped_today' => Shipment::whereDate('shipped_at', today())->count(),
            'packages_shipped_this_week' => Shipment::whereBetween('shipped_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'packages_shipped_this_month' => Shipment::whereMonth('shipped_at', now()->month)->count(),
            'average_delivery_time' => '3-5 business days',
        ];
        
        return view('dashboard.printing.analytics', compact('printStats', 'shippingStats'));
    }

    public function addToPrintQueue(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        // Check if application is ready for printing
        if (!in_array($application->status, ['approved', 'ready_for_printing'])) {
            return back()->with('error', 'Application is not ready for printing.');
        }
        
        $application->update([
            'status' => 'in_print_queue'
        ]);
        
        return back()->with('success', 'Application added to print queue.');
    }
    
    public function markAsPrinting(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        $application->update([
            'status' => 'printing'
        ]);
        
        return back()->with('success', 'Application marked as printing.');
    }
    
    public function markAsPrinted(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        $application->update([
            'status' => 'printed_ready_to_ship'
        ]);
        
        return back()->with('success', 'Application marked as printed and ready to ship.');
    }
    
    public function prepareShipment(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'shipping_method' => 'required|string',
            'recipient_name' => 'required|string',
            'recipient_address' => 'required|string',
            'recipient_city' => 'required|string',
            'recipient_state' => 'required|string',
            'recipient_zip' => 'required|string',
            'special_instructions' => 'nullable|string'
        ]);
        
        // Create shipment
        $shipment = Shipment::create([
            'tracking_number' => 'HP' . now()->format('YmdHis') . rand(100, 999),
            'carrier' => $request->shipping_method,
            'recipient_name' => $request->recipient_name,
            'recipient_address' => $request->recipient_address,
            'recipient_city' => $request->recipient_city,
            'recipient_state' => $request->recipient_state,
            'recipient_zip' => $request->recipient_zip,
            'special_instructions' => $request->special_instructions,
            'status' => 'prepared',
            'prepared_at' => now(),
            'prepared_by' => auth()->id()
        ]);
        
        // Update applications
        foreach ($request->application_ids as $appId) {
            $application = Application::find($appId);
            if ($application && $application->status == 'printed_ready_to_ship') {
                $application->update([
                    'status' => 'package_prepared'
                ]);
                
                // Link application to shipment
                $shipment->applications()->attach($appId);
            }
        }
        
        return redirect()->route('printing.shipping')
            ->with('success', 'Shipment prepared successfully. Tracking number: ' . $shipment->tracking_number);
    }
    
    public function ship(Request $request, $shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        
        $shipment->update([
            'status' => 'shipped',
            'shipped_at' => now(),
            'shipped_by' => auth()->id()
        ]);
        
        // Update related applications
        foreach ($shipment->applications as $application) {
            $application->update([
                'status' => 'shipped'
            ]);
        }
        
        // Create tracking event
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => 'shipped',
            'description' => 'Package shipped from HP Ways facility',
            'location' => 'HP Ways Processing Center',
            'event_date' => now()
        ]);
        
        return back()->with('success', 'Shipment marked as shipped.');
    }
    
    public function updateTrackingStatus(Request $request, $shipmentId)
    {
        $request->validate([
            'status' => 'required|string',
            'location' => 'required|string',
            'description' => 'required|string'
        ]);
        
        $shipment = Shipment::findOrFail($shipmentId);
        
        // Create tracking event
        TrackingEvent::create([
            'shipment_id' => $shipment->id,
            'event_type' => $request->status,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => now()
        ]);
        
        // Update shipment status
        $shipment->update([
            'status' => $request->status
        ]);
        
        // If delivered, update applications
        if ($request->status == 'delivered') {
            foreach ($shipment->applications as $application) {
                $application->update([
                    'status' => 'delivered'
                ]);
            }
            
            $shipment->update([
                'delivered_at' => now()
            ]);
        }
        
        return back()->with('success', 'Tracking status updated successfully.');
    }
    
    public function bulkPrint(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id'
        ]);
        
        Application::whereIn('id', $request->application_ids)
            ->whereIn('status', ['approved', 'ready_for_printing', 'in_print_queue'])
            ->update(['status' => 'printed_ready_to_ship']);
            
        return back()->with('success', count($request->application_ids) . ' applications marked as printed.');
    }
}
