<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Document;
use App\Models\QuizNode;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        // Authentication middleware disabled for development
        // $this->middleware('auth');
        // $this->middleware(['role:admin|super-admin']);
    }

    public function index()
    {
        // Get real statistics from database
        $totalUsers = User::count();
        
        // Count users by role (using a subquery for performance)
        $roleUserCount = DB::table('model_has_roles')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
            ->groupBy('roles.name')
            ->get()
            ->pluck('count', 'name')
            ->toArray();
            
        $totalApplicants = $roleUserCount['applicant'] ?? User::whereHas('roles', function($query) {
            $query->where('name', 'applicant');
        })->count();
        $totalCaseManagers = $roleUserCount['case_manager'] ?? 0;
        $totalAttorneys = $roleUserCount['attorney'] ?? 0;
        $totalAdmins = ($roleUserCount['admin'] ?? 0) + ($roleUserCount['big_admin'] ?? 0);
        
        // Get recent applicants
        $recentUsers = User::whereHas('roles', function($query) {
                $query->where('name', 'applicant');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get application statistics
        $totalApplications = Application::count();
        $pendingApplications = Application::whereIn('status', ['pending', 'pending_review', 'pending_attorney_review'])->count();
        $approvedApplications = Application::where('status', 'approved')->count();
        $rejectedApplications = Application::where('status', 'rejected')->count();

        // Get recent applications: include all statuses, prefer submitted_at, then created_at, then newest id
        $recentApplications = Application::with('user')
            ->orderByRaw('COALESCE(submitted_at, created_at) DESC')
            ->orderByDesc('id')
            ->take(5)
            ->get();
        
        return view('dashboard.admin.index', compact(
            'totalUsers', 
            'totalApplicants', 
            'totalCaseManagers', 
            'totalAttorneys',
            'totalAdmins',
            'recentUsers',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'recentApplications'
        ));
    }
    
    public function userManagement()
    {
        try {
            // Get applicants (users with 'applicant' role)
            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'applicant');
            })->with('applications')->paginate(15);
            
            return view('dashboard.admin.users', compact('users'));
        } catch (\Exception $e) {
            // Fallback for when database might not be accessible
            return view('dashboard.admin.users', [
                'users' => [], 
                'error' => 'Could not load applicants: ' . $e->getMessage()
            ]);
        }
    }
    
    public function applications()
    {
        try {
            // Get all applications with user info
            $applications = Application::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            return view('dashboard.admin.applications', compact('applications'));
        } catch (\Exception $e) {
            // Fallback for when database might not be accessible
            return view('dashboard.admin.applications', [
                'applications' => [],
                'error' => 'Could not load applications: ' . $e->getMessage()
            ]);
        }
    }
    
    public function reports()
    {
        try {
            // Generate reports based on actual data
            $visaTypeDistribution = Application::select('type', DB::raw('count(*) as total'))
                ->groupBy('type')
                ->get();
                
            $monthlyApplications = Application::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as total')
            )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get();
                
            $documentStats = Document::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get();
                
            return view('dashboard.admin.reports', compact(
                'visaTypeDistribution',
                'monthlyApplications',
                'documentStats'
            ));
        } catch (\Exception $e) {
            // Fallback with empty data if database access fails
            return view('dashboard.admin.reports');
        }
    }    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);
        
        // Remove existing roles and assign new one
        $user->syncRoles([$request->role]);
        
        return back()->with('success', 'Role assigned successfully');
    }
    
    public function showUser($id)
    {
        $user = User::with('applications')->findOrFail($id);
        return view('dashboard.admin.user-profile', compact('user'));
    }
    
    public function createUser()
    {
        $roles = Role::where('name', '!=', 'applicant')->get();
        return view('dashboard.admin.create-user', compact('roles'));
    }

    public function editUser(User $user)
    {
        $roles = Role::where('name', '!=', 'applicant')->get();
        return view('dashboard.admin.edit-user', compact('user', 'roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ]);
        
        $user->assignRole($request->role);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }
    
    public function showApplication($id)
    {
        try {
            // Load application with all related data
            $application = Application::with([
                'user', 
                'documents', 
                'feedback.attorney', 
                'payments',
                'caseManager', 
                'attorney',
                'assignedPrinter',
                'selectedPackage'
            ])->findOrFail($id);
            
            // Get all available staff for assignment
            $availableCaseManagers = User::role('case_manager')->get();
            $availableAttorneys = User::role('attorney')->get(); 
            $availablePrinters = User::role('printing_department')->get();
            
            // Determine required documents - prefer package-specific, fallback to visa-type config/table
            $requiredDocuments = collect();
            if ($application->selected_package_id) {
                $requiredDocuments = \App\Models\PackageRequiredDocument::where('package_id', $application->selected_package_id)
                    ->where('active', 1)
                    ->get();
            }
            if ($requiredDocuments->isEmpty()) {
                // Fallback to RequiredDocument table matching visa_type or 'all'
                $requiredDocuments = \App\Models\RequiredDocument::where(function($q) use ($application) {
                        $q->where('visa_type', $application->visa_type)
                          ->orWhere('visa_type', 'all');
                    })
                    ->where('active', 1)
                    ->get();
            }

            // Compute completion based on required codes mapped to uploaded Document.type
            $requiredCodes = $requiredDocuments->pluck('code')->filter()->values();
            $uploadedCodes = collect($application->documents)->pluck('type')->filter()->unique();
            $submitted = $uploadedCodes->intersect($requiredCodes)->count();
            $totalRequired = $requiredCodes->count();
            $completionPercentage = $totalRequired > 0 ? round(($submitted / $totalRequired) * 100) : 0;
            
            return view('dashboard.admin.application-detail', compact(
                'application', 
                'availableCaseManagers', 
                'availableAttorneys', 
                'availablePrinters',
                'requiredDocuments',
                'completionPercentage'
            ));
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error("Application detail error: " . $e->getMessage());
            
            // Create a mock application object if database access fails
            $application = (object)[
                'id' => $id,
                'status' => 'unknown',
                'visa_type' => 'Demo Visa',
                'created_at' => now(),
                'user' => (object)['name' => 'Demo User', 'email' => 'demo@example.com'],
                'documents' => [],
                'feedback' => [],
                'payments' => [],
                'caseManager' => null,
                'attorney' => null,
                'assignedPrinter' => null
            ];
            
            $availableCaseManagers = [];
            $availableAttorneys = [];
            $availablePrinters = [];
            $requiredDocuments = [];
            $completionPercentage = 0;
            
            return view('dashboard.admin.application-detail', compact(
                'application', 
                'availableCaseManagers', 
                'availableAttorneys', 
                'availablePrinters',
                'requiredDocuments',
                'completionPercentage'
            ));
        }
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ]);

        if ($request->password) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        // Prevent deleting super admin
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Cannot delete super admin user.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        // Generate a random password
        $newPassword = 'TempPass' . rand(1000, 9999);

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Here you could send an email to the user with the new password
        // For now, we'll return it in the response

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ]);
    }

    public function updateApplicationStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,under_review,completed'
        ]);

        $application->update(['status' => $request->status]);

        return back()->with('success', 'Application status updated successfully.');
    }

    public function systemSettings()
    {
        return view('dashboard.admin.settings');
    }
    
    public function caseManagers()
    {
        try {
            $caseManagers = User::role('case_manager')->with('managedCases')->paginate(15);
            $totalCases = Application::whereNotNull('case_manager_id')->count();
            
            return view('dashboard.admin.case-managers', compact('caseManagers', 'totalCases'));
        } catch (\Exception $e) {
            // Create empty collection for fallback
            $caseManagers = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 15, 1, ['path' => request()->url()]
            );
            return view('dashboard.admin.case-managers', [
                'caseManagers' => $caseManagers, 
                'totalCases' => 0,
                'error' => 'Could not load case managers: ' . $e->getMessage()
            ]);
        }
    }
    
    public function attorneys()
    {
        try {
            // Define active statuses - cases that are not completed, rejected, or on hold
            $activeStatuses = [
                'draft', 'pending', 'in_progress', 'document_review', 'pending_review', 
                'ready_for_attorney_review', 'assigned_to_attorney', 'under_attorney_review',
                'attorney_feedback_provided', 'rfe_issued', 'ready_to_file', 'filed',
                'ready', 'ready_to_submit', 'to_print', 'printing', 'printed'
            ];

            $attorneys = User::role('attorney')
                ->withCount([
                    // Total assigned cases to the attorney
                    'assignedCases as total_assigned_cases',
                    // Active cases = cases that are not completed/rejected/closed
                    'assignedCases as active_cases_count' => function ($q) use ($activeStatuses) {
                        $q->whereIn('status', $activeStatuses);
                    },
                    // Pending review cases specifically assigned to attorney
                    'assignedCases as pending_review_count' => function ($q) {
                        $q->whereIn('status', ['assigned_to_attorney', 'under_attorney_review']);
                    },
                    // Completed cases (approved/rejected)
                    'assignedCases as completed_cases_count' => function ($q) {
                        $q->whereIn('status', ['approved', 'rejected', 'closed']);
                    }
                ])
                ->paginate(15);

            // Totals for stats cards
            $totalAssignedCases = Application::whereNotNull('attorney_id')->count();
            $totalActiveCases = Application::whereNotNull('attorney_id')
                ->whereIn('status', $activeStatuses)
                ->count();
            
            return view('dashboard.admin.attorneys', compact('attorneys', 'totalAssignedCases', 'totalActiveCases'));
        } catch (\Exception $e) {
            // Create empty collection for fallback
            $attorneys = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 15, 1, ['path' => request()->url()]
            );
            return view('dashboard.admin.attorneys', [
                'attorneys' => $attorneys,
                'totalAssignedCases' => 0,
                'totalActiveCases' => 0,
                'error' => 'Could not load attorneys: ' . $e->getMessage()
            ]);
        }
    }

    public function viewAttorney(User $user)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        $activeStatuses = [
            'draft', 'pending', 'in_progress', 'document_review', 'pending_review', 
            'ready_for_attorney_review', 'assigned_to_attorney', 'under_attorney_review',
            'attorney_feedback_provided', 'rfe_issued', 'ready_to_file', 'filed',
            'ready', 'ready_to_submit', 'to_print', 'printing', 'printed'
        ];

        $assignedCases = $user->assignedCases()
            ->with(['user', 'caseManager', 'documents'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_cases' => $user->assignedCases()->count(),
            'active_cases' => $user->assignedCases()->whereIn('status', $activeStatuses)->count(),
            'pending_review' => $user->assignedCases()->whereIn('status', ['assigned_to_attorney', 'under_attorney_review'])->count(),
            'completed_cases' => $user->assignedCases()->whereIn('status', ['approved', 'rejected', 'closed'])->count(),
            'this_month_completed' => $user->assignedCases()->whereIn('status', ['approved', 'rejected'])
                ->where('updated_at', '>=', now()->startOfMonth())->count(),
        ];

        return view('dashboard.admin.attorneys.view', compact('user', 'assignedCases', 'stats'));
    }

    public function attorneyCases(User $user, Request $request)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        $query = $user->assignedCases()
            ->with(['user', 'caseManager', 'documents', 'payments']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dashboard.admin.attorneys.cases', compact('user', 'cases'));
    }

    public function attorneyPerformance(User $user)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        // Performance metrics for the last 12 months
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'approved' => $user->assignedCases()
                    ->where('status', 'approved')
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->count(),
                'rejected' => $user->assignedCases()
                    ->where('status', 'rejected')
                    ->whereYear('updated_at', $month->year)
                    ->whereMonth('updated_at', $month->month)
                    ->count(),
            ];
        }

        $overallStats = [
            'total_approved' => $user->assignedCases()->where('status', 'approved')->count(),
            'total_rejected' => $user->assignedCases()->where('status', 'rejected')->count(),
            'average_resolution_days' => 0, // TODO: Calculate based on created_at vs updated_at
            'approval_rate' => 0,
        ];

        $totalCompleted = $overallStats['total_approved'] + $overallStats['total_rejected'];
        if ($totalCompleted > 0) {
            $overallStats['approval_rate'] = round(($overallStats['total_approved'] / $totalCompleted) * 100, 1);
        }

        return view('dashboard.admin.attorneys.performance', compact('user', 'monthlyStats', 'overallStats'));
    }

    public function suspendAttorney(User $user)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        $user->update(['is_suspended' => true]);
        
        return redirect()->route('admin.attorneys')
            ->with('success', 'Attorney has been suspended successfully.');
    }

    public function activateAttorney(User $user)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        $user->update(['is_suspended' => false]);
        
        return redirect()->route('admin.attorneys')
            ->with('success', 'Attorney has been activated successfully.');
    }

    public function resetAttorneyPassword(User $user)
    {
        if (!$user->hasRole('attorney')) {
            abort(404, 'Attorney not found');
        }

        $newPassword = 'password123'; // In production, generate a secure random password
        $user->update(['password' => bcrypt($newPassword)]);
        
        return redirect()->route('admin.attorneys.view', $user)
            ->with('success', "Password reset successfully. New password: {$newPassword}");
    }
    
    public function printingStaff()
    {
        try {
            $printingStaff = User::role('printing_department')->paginate(15);
            $totalShipments = 0; // Add shipment count logic if needed
            
            return view('dashboard.admin.printing-staff', compact('printingStaff', 'totalShipments'));
        } catch (\Exception $e) {
            // Create empty collection for fallback
            $printingStaff = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 15, 1, ['path' => request()->url()]
            );
            return view('dashboard.admin.printing-staff', [
                'printingStaff' => $printingStaff, 
                'totalShipments' => 0,
                'error' => 'Could not load printing staff: ' . $e->getMessage()
            ]);
        }
    }
    
    public function createCaseManager(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => 'nullable|string|max:255|unique:users,username',
        ]);

        // Create full name from first and last name
        $fullName = trim($request->first_name . ' ' . $request->last_name);

        $user = User::create([
            'name' => $fullName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ]);

        $user->assignRole('case_manager');

        return redirect()->route('admin.case-managers')->with('success', 'Case Manager created successfully.');
    }
    
    // Staff Assignment Methods
    public function assignCaseManager(Request $request, $applicationId)
    {
        try {
            $application = Application::findOrFail($applicationId);
            $application->case_manager_id = $request->case_manager_id;
            $application->save();
            return redirect()->back()->with('success', 'Case Manager assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign Case Manager.');
        }
    }
    
    public function assignAttorney(Request $request, $applicationId)
    {
        try {
            $application = Application::findOrFail($applicationId);
            $application->attorney_id = $request->attorney_id;
            $application->save();
            
            return redirect()->back()->with('success', 'Attorney assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign Attorney.');
        }
    }
    
    public function assignPrinter(Request $request, $applicationId)
    {
        try {
            $application = Application::findOrFail($applicationId);
            $application->assigned_printer_id = $request->printer_id;
            // If not already in or beyond the print pipeline, auto-queue it
            $pipelineStatuses = ['in_print_queue','printing','printed','ready_to_ship','shipped','delivered'];
            $shouldQueue = !in_array($application->status, $pipelineStatuses, true);
            if ($shouldQueue) {
                $application->status = 'in_print_queue';
            }
            $application->save();

            // Create tracking event noting assignment (and queueing if applicable)
            try {
                \App\Models\TrackingEvent::create([
                    'application_id' => $application->id,
                    'event_type' => $shouldQueue ? 'assigned_printer_and_queued' : 'assigned_printer',
                    'description' => ($shouldQueue
                        ? 'Printer assigned and application moved to print queue.'
                        : 'Printer assigned to application.') . ' Printer ID: ' . $request->printer_id,
                    'user_id' => auth()->id(),
                    'event_time' => now(),
                    'occurred_at' => now()
                ]);
            } catch (\Throwable $e) {
                // Non-fatal if tracking event fails
            }
            
            return redirect()->back()->with('success', 'Printer assigned successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to assign Printer.');
        }
    }
    
    public function createAttorney(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ]);

        $user->assignRole('attorney');

        return redirect()->route('admin.attorneys')->with('success', 'Attorney created successfully.');
    }
    
    public function createPrintingStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ]);

        $user->assignRole('printing_department');

        return redirect()->route('admin.printing-staff')->with('success', 'Printing Staff created successfully.');
    }

    public function editPrintingStaff($id)
    {
        try {
            $staff = User::findOrFail($id);
            
            // Verify user has printing_department role
            if (!$staff->hasRole('printing_department')) {
                return response()->json(['error' => 'User is not a printing department staff member.'], 400);
            }

            return response()->json([
                'success' => true,
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'username' => $staff->username ?? '',
                'first_name' => $staff->first_name ?? '',
                'last_name' => $staff->last_name ?? '',
                'status' => $staff->is_active ?? true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load staff information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePrintingStaff(Request $request, $id)
    {
        try {
            $staff = User::findOrFail($id);

            // Verify user has printing_department role
            if (!$staff->hasRole('printing_department')) {
                return response()->json(['error' => 'User is not a printing department staff member.'], 400);
            }

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255|unique:users,username,' . $id,
                'status' => 'required|in:active,inactive'
            ];

            // Add password validation only if password is provided
            if ($request->filled('password')) {
                $rules['password'] = 'required|confirmed|min:8';
            }

            $validated = $request->validate($rules);

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'username' => $validated['username'],
                'is_active' => $validated['status'] === 'active'
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $staff->update($updateData);

            // Return JSON response for AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Printing Staff updated successfully.',
                    'staff' => [
                        'id' => $staff->id,
                        'name' => $staff->name,
                        'email' => $staff->email,
                        'username' => $staff->username,
                        'is_active' => $staff->is_active
                    ]
                ]);
            }

            return redirect()->route('admin.printing-staff')->with('success', 'Printing Staff updated successfully.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Failed to update staff: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to update staff: ' . $e->getMessage());
        }
    }

    public function togglePrintingStaffStatus($id)
    {
        $staff = User::findOrFail($id);
        
        if (!$staff->hasRole('printing_department')) {
            return response()->json(['error' => 'User is not a printing department staff member.'], 400);
        }

        $newStatus = !($staff->is_active ?? true);
        $staff->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus ? 'active' : 'inactive',
            'message' => 'Status updated successfully.'
        ]);
    }

    public function deletePrintingStaff($id)
    {
        $staff = User::findOrFail($id);
        
        if (!$staff->hasRole('printing_department')) {
            return response()->json(['error' => 'User is not a printing department staff member.'], 400);
        }

        // Check if staff has any pending print jobs
        $pendingJobs = Application::where('assigned_printer_id', $staff->id)
            ->whereIn('status', ['in_print_queue', 'printing'])
            ->count();

        if ($pendingJobs > 0) {
            return response()->json([
                'error' => 'Cannot delete staff member with pending print jobs. Please reassign jobs first.'
            ], 400);
        }

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Printing staff member deleted successfully.'
        ]);
    }

    public function assignPrintingJobs(Request $request, $staffId)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id'
        ]);

        $staff = User::findOrFail($staffId);
        
        if (!$staff->hasRole('printing_department')) {
            return response()->json(['error' => 'User is not a printing department staff member.'], 400);
        }

        $updated = Application::whereIn('id', $request->application_ids)
            ->whereIn('status', ['in_print_queue', 'approved'])
            ->update(['assigned_printer_id' => $staffId]);

        return response()->json([
            'success' => true,
            'message' => "Assigned {$updated} print jobs to {$staff->name}."
        ]);
    }
    
    // =================== Packages Management ===================
    public function packagesIndex(Request $request)
    {
        $query = \App\Models\Package::with('visaCategory');
        if ($filterVt = $request->get('visa_type')) {
            if ($filterVt === 'GLOBAL') { $query->whereNull('visa_type'); } else { $query->where('visa_type',$filterVt); }
        }
        if ($search = $request->get('q')) {
            $query->where(function($q) use ($search){
                $q->where('name','like',"%{$search}%")
                  ->orWhere('code','like',"%{$search}%")
                  ->orWhere('visa_type','like',"%{$search}%")
                  ->orWhereHas('visaCategory', function($qq) use ($search){
                      $qq->where('name','like',"%{$search}%");
                  });
            });
        }
        // Group later in view; still paginate to avoid huge lists.
        $packages = $query
            ->orderByRaw('COALESCE(visa_type, "ZZZ") asc')
            ->orderBy('visa_category_id')
            ->orderBy('price_cents')
            ->paginate(100)
            ->withQueryString();
        // Build structure: [visa_type or 'GLOBAL'][category_id][tier]
        $tiersExpected = ['basic','advanced','premium'];
        $grouped = [];
        foreach ($packages as $p) {
            $vt = $p->visa_type ?? 'GLOBAL';
            $catKey = $p->visa_category_id ?? 0;
            $tierKey = strtolower($p->code); // using code as tier indicator
            $grouped[$vt][$catKey]['category'] = $p->visaCategory;
            $grouped[$vt][$catKey]['packages'][$tierKey] = $p;
            $grouped[$vt][$catKey]['missing'] = array_values(array_diff($tiersExpected, array_keys($grouped[$vt][$catKey]['packages'] ?? [])));
        }
        $visaTypes = \App\Models\Package::select('visa_type')->distinct()->pluck('visa_type')->map(fn($v)=>$v ?: 'GLOBAL')->unique()->sort()->values();
        return view('dashboard.admin.packages.index', [
            'packages' => $packages,
            'grouped' => $grouped,
            'tiersExpected' => $tiersExpected,
            'visaTypes' => $visaTypes,
        ]);
    }

    public function packagesCreate()
    {
        $visaTypes = \App\Models\Package::select('visa_type')->distinct()->pluck('visa_type')->filter()->sort()->values();
        $visaCategories = \App\Models\VisaCategory::all();
        return view('dashboard.admin.packages.create', compact('visaTypes', 'visaCategories'));
    }

    public function packagesStore(Request $request)
    {
        $data = $request->validate([
            'visa_type' => 'nullable|string|max:40',
            'code' => 'required|string|max:30|unique:packages,code,NULL,id,visa_type,' . ($request->visa_type ?? ''),
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'active' => 'nullable|boolean',
            'visa_category_id' => 'nullable|integer|exists:visa_categories,id'
        ]);
        $package = new \App\Models\Package();
    // Determine target grouping using incoming values if provided, otherwise keep existing
    $targetVisaType = array_key_exists('visa_type', $data) ? ($data['visa_type'] ?? null) : $package->visa_type;
    $targetVisaCategoryId = array_key_exists('visa_category_id', $data) ? ($data['visa_category_id'] ?? $package->visa_category_id) : $package->visa_category_id;
        // Normalize tier code (support common misspelling 'promium')
        $tierCode = strtolower($data['code']);
        if ($tierCode === 'promium') { $tierCode = 'premium'; }
        if (!in_array($tierCode, ['basic','advanced','premium'])) {
            return back()->withInput()->with('error','Code must be one of: basic, advanced, premium');
        }
        // Ensure uniqueness within same grouping
        $vt = $data['visa_type'] ?? null;
        $vcid = $data['visa_category_id'] ?? null;
        $duplicate = \App\Models\Package::where('code',$tierCode)
            ->where(function($q) use ($vt){
                $vt ? $q->where('visa_type',$vt) : $q->whereNull('visa_type');
            })
            ->where(function($q) use ($vcid){
                $vcid ? $q->where('visa_category_id',$vcid) : $q->whereNull('visa_category_id');
            })
            ->exists();
        if ($duplicate) {
            return back()->withInput()->with('error','Tier already exists for this visa type & category');
        }
        $package->code = $tierCode;
        $package->name = $data['name'];
        $package->price_cents = (int) round($data['price'] * 100);
        $package->features = $data['features'] ?? [];
        $package->active = $request->boolean('active', true);
        $package->save();
        $this->refreshTierCompleteness($package->visa_type, $package->visa_category_id);
        return redirect()->route('admin.packages.index')->with('success','Package created');
    }

    public function packagesEdit(\App\Models\Package $package)
    {
    $visaTypes = Application::select('visa_type')->distinct()->pluck('visa_type')->filter()->values();
    $package->load('requiredDocuments');
    return view('dashboard.admin.packages.edit', compact('package','visaTypes'));
    }

    public function packagesUpdate(Request $request, \App\Models\Package $package)
    {
        $data = $request->validate([
            'visa_type' => 'nullable|string|max:40',
            'code' => 'required|string|max:30|unique:packages,code,' . $package->id . ',id,visa_type,' . ($request->visa_type ?? ''),
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'active' => 'nullable|boolean',
            'visa_category_id' => 'nullable|integer|exists:visa_categories,id',
            // documents array is optional; validate minimally per item
            'documents' => 'sometimes|array',
            'documents.*.id' => 'nullable|integer|exists:package_required_documents,id',
            'documents.*.code' => 'required_with:documents|string|max:50',
            'documents.*.label' => 'required_with:documents|string|max:255',
        ]);
        // Determine target grouping using incoming values if provided; otherwise keep existing
        $targetVisaType = array_key_exists('visa_type', $data)
            ? ($data['visa_type'] ?? null)
            : $package->visa_type;
        $targetVisaCategoryId = array_key_exists('visa_category_id', $data)
            ? ($data['visa_category_id'] ?? null)
            : $package->visa_category_id;
        $tierCode = strtolower($data['code']);
        if ($tierCode === 'promium') { $tierCode = 'premium'; }
        if (!in_array($tierCode, ['basic','advanced','premium'])) {
            return back()->withInput()->with('error','Code must be one of: basic, advanced, premium');
        }
        $duplicate = \App\Models\Package::where('code',$tierCode)
            ->where('id','!=',$package->id)
            ->where(function($q) use ($targetVisaType){
                $targetVisaType ? $q->where('visa_type',$targetVisaType) : $q->whereNull('visa_type');
            })
            ->where(function($q) use ($targetVisaCategoryId){
                $targetVisaCategoryId ? $q->where('visa_category_id',$targetVisaCategoryId) : $q->whereNull('visa_category_id');
            })
            ->exists();
        if ($duplicate) {
            return back()->withInput()->with('error','Tier already exists for this visa type & category');
        }
        // Persist grouping only if explicitly provided to avoid overwriting NOT NULL with null
        if (array_key_exists('visa_type', $data)) {
            $package->visa_type = $targetVisaType;
        }
        if (array_key_exists('visa_category_id', $data)) {
            $package->visa_category_id = $targetVisaCategoryId;
        }
        $package->code = $tierCode;
        $package->name = $data['name'];
        $package->price_cents = (int) round($data['price'] * 100);
        $package->features = $data['features'] ?? [];
        $package->active = $request->boolean('active');
        $package->save();
        // Sync required documents (add/update/delete)
        $docsInput = $request->input('documents', []);
        if (is_array($docsInput)) {
            $existing = $package->requiredDocuments()->get()->keyBy('id');
            $keptIds = [];
            foreach ($docsInput as $doc) {
                if (!is_array($doc)) continue;
                $code = strtoupper(trim($doc['code'] ?? ''));
                $label = trim($doc['label'] ?? '');
                if ($code === '' || $label === '') continue;
                $payload = [
                    'code' => $code,
                    'label' => $label,
                    'required' => !empty($doc['required']),
                    'translation_possible' => !empty($doc['translation_possible']),
                    'active' => !empty($doc['active']),
                ];
                if (!empty($doc['id']) && $existing->has((int)$doc['id'])) {
                    $model = $existing[(int)$doc['id']];
                    $model->update($payload);
                    $keptIds[] = $model->id;
                } else {
                    $model = $package->requiredDocuments()->create($payload);
                    $keptIds[] = $model->id;
                }
            }
            // Delete removed docs
            $toDelete = $existing->keys()->diff($keptIds);
            if ($toDelete->isNotEmpty()) {
                $package->requiredDocuments()->whereIn('id', $toDelete)->delete();
            }
        }
        $this->refreshTierCompleteness($package->visa_type, $package->visa_category_id);
        return redirect()->route('admin.packages.index')->with('success','Package updated');
    }

    public function packagesToggleActive(\App\Models\Package $package)
    {
        $package->active = !$package->active;
        $package->save();
        return back()->with('success','Status toggled');
    }

    public function packagesDestroy(\App\Models\Package $package)
    {
        // Soft delete not enabled; ensure not selected by any application
        $inUse = Application::where('selected_package_id',$package->id)->exists();
        if ($inUse) {
            return back()->with('error','Cannot delete: package selected by existing applications');
        }
        $vt = $package->visa_type; $cat = $package->visa_category_id;
        $package->delete();
        $this->refreshTierCompleteness($vt, $cat);
        return redirect()->route('admin.packages.index')->with('success','Package deleted');
    }

    // =================== Bulk Package Management ===================
    public function packagesBulkEdit(Request $request)
    {
        $visaType = $request->get('visa_type');
        if (!$visaType) {
            return redirect()->route('admin.packages.index')->with('error', 'Visa type is required for bulk edit');
        }

        // Get all packages for this visa type
        $packages = \App\Models\Package::where('visa_type', $visaType)
            ->orderBy('price_cents')
            ->get()
            ->keyBy('code'); // Key by code (basic, advanced, premium)

        $visaTypes = \App\Models\Package::select('visa_type')->distinct()->pluck('visa_type')->filter()->sort()->values();

        return view('dashboard.admin.packages.bulk-edit', compact('packages', 'visaType', 'visaTypes'));
    }

    public function packagesBulkUpdate(Request $request)
    {
        $visaType = $request->get('visa_type');
        if (!$visaType) {
            return redirect()->route('admin.packages.index')->with('error', 'Visa type is required');
        }

        $validated = $request->validate([
            'packages' => 'required|array',
            'packages.basic.id' => 'nullable|exists:packages,id',
            'packages.basic.name' => 'nullable|string|max:255',
            'packages.basic.price_cents' => 'nullable|integer|min:0',
            'packages.basic.features' => 'nullable|array',
            'packages.basic.features.*' => 'string|max:255',
            'packages.basic.active' => 'nullable|boolean',
            'packages.advanced.id' => 'nullable|exists:packages,id',
            'packages.advanced.name' => 'nullable|string|max:255',
            'packages.advanced.price_cents' => 'nullable|integer|min:0',
            'packages.advanced.features' => 'nullable|array',
            'packages.advanced.features.*' => 'string|max:255',
            'packages.advanced.active' => 'nullable|boolean',
            'packages.premium.id' => 'nullable|exists:packages,id',
            'packages.premium.name' => 'nullable|string|max:255',
            'packages.premium.price_cents' => 'nullable|integer|min:0',
            'packages.premium.features' => 'nullable|array',
            'packages.premium.features.*' => 'string|max:255',
            'packages.premium.active' => 'nullable|boolean',
        ]);

        $updatedCount = 0;
        $tiers = ['basic', 'advanced', 'premium'];

        foreach ($tiers as $tier) {
            if (isset($validated['packages'][$tier]['id'])) {
                $package = \App\Models\Package::find($validated['packages'][$tier]['id']);
                if ($package && $package->visa_type === $visaType) {
                    $package->update([
                        'name' => $validated['packages'][$tier]['name'],
                        'price_cents' => $validated['packages'][$tier]['price_cents'],
                        'features' => $validated['packages'][$tier]['features'] ?? [],
                        'active' => $validated['packages'][$tier]['active'] ?? true,
                    ]);
                    $updatedCount++;
                }
            }
        }

        return redirect()->route('admin.packages.index', ['visa_type' => $visaType])
            ->with('success', "Successfully updated {$updatedCount} packages for {$visaType}");
    }

    public function packagesBulkCreate(Request $request)
    {
        $visaType = $request->get('visa_type');
        if (!$visaType) {
            return redirect()->route('admin.packages.index')->with('error', 'Visa type is required');
        }

        $validated = $request->validate([
            'visa_type' => 'required|string|max:40',
            'packages' => 'required|array|min:1',
            'packages.basic.code' => 'nullable|string|max:30',
            'packages.basic.name' => 'nullable|string|max:255',
            'packages.basic.price' => 'nullable|numeric|min:0',
            'packages.basic.price_cents' => 'nullable|numeric|min:0',
            'packages.basic.features' => 'nullable|array',
            'packages.basic.features.*' => 'string|max:255',
            'packages.advanced.code' => 'nullable|string|max:30',
            'packages.advanced.name' => 'nullable|string|max:255',
            'packages.advanced.price' => 'nullable|numeric|min:0',
            'packages.advanced.price_cents' => 'nullable|numeric|min:0',
            'packages.advanced.features' => 'nullable|array',
            'packages.advanced.features.*' => 'string|max:255',
            'packages.premium.code' => 'nullable|string|max:30',
            'packages.premium.name' => 'nullable|string|max:255',
            'packages.premium.price' => 'nullable|numeric|min:0',
            'packages.premium.price_cents' => 'nullable|numeric|min:0',
            'packages.premium.features' => 'nullable|array',
            'packages.premium.features.*' => 'string|max:255',
        ]);

        $createdCount = 0;
        $tiers = ['basic', 'advanced', 'premium'];

        foreach ($tiers as $tier) {
            if (isset($validated['packages'][$tier]['code']) && isset($validated['packages'][$tier]['name'])) {
                $price = $validated['packages'][$tier]['price'] ?? $validated['packages'][$tier]['price_cents'] ?? 0;

                // Check for duplicates
                $duplicate = \App\Models\Package::where('code', $validated['packages'][$tier]['code'])
                    ->where('visa_type', $visaType)
                    ->exists();

                if ($duplicate) {
                    return redirect()->back()->withInput()->with('error', "Package with code '{$validated['packages'][$tier]['code']}' already exists for visa type '{$visaType}'");
                }

                \App\Models\Package::create([
                    'visa_type' => $visaType,
                    'visa_category_id' => null, // Set to null for bulk created packages
                    'code' => $validated['packages'][$tier]['code'],
                    'name' => $validated['packages'][$tier]['name'],
                    'price_cents' => (int) round($price * 100), // Convert dollars to cents
                    'features' => $validated['packages'][$tier]['features'] ?? [],
                    'active' => true,
                ]);
                $createdCount++;
            }
        }

        return redirect()->route('admin.packages.index', ['visa_type' => $visaType])
            ->with('success', "Successfully created {$createdCount} packages for {$visaType}");
    }

    // Import defaults removed per request

    private function refreshTierCompleteness(?string $visaType, $categoryId): void
    {
        $tiers = ['basic','advanced','premium'];
        $query = \App\Models\Package::query();
        $visaType ? $query->where('visa_type',$visaType) : $query->whereNull('visa_type');
        $categoryId ? $query->where('visa_category_id',$categoryId) : $query->whereNull('visa_category_id');
        $existing = $query->get()->keyBy('code');
        $base = $existing['basic'] ?? $existing->first();
        foreach ($tiers as $tier) {
            if (!$existing->has($tier)) {
                \App\Models\Package::create([
                    'visa_type' => $visaType,
                    'visa_category_id' => $categoryId,
                    'code' => $tier,
                    'name' => ucfirst($tier),
                    'price_cents' => $base?->price_cents ?? 0,
                    'features' => $base?->features ?? [],
                    'active' => false,
                ]);
            }
        }
    }

    // ================= Quiz Management Methods =================
    
    public function quizzesIndex()
    {
        $quizNodes = QuizNode::orderBy('node_id')->paginate(15);
        // Full list for flowchart canvas and accurate stats
        $allQuizNodes = QuizNode::orderBy('node_id')->get();
        $statsTotal = $allQuizNodes->count();
        $statsSingle = $allQuizNodes->where('type', 'single')->count();
        $statsMulti = $allQuizNodes->where('type', 'multi')->count();
        $statsOptions = $allQuizNodes->sum(function ($node) { return is_array($node->options) ? count($node->options) : 0; });

        return view('dashboard.admin.quizzes.index', compact('quizNodes','allQuizNodes','statsTotal','statsSingle','statsMulti','statsOptions'));
    }

    public function quizzesCreate()
    {
        $nextOptions = $this->getNextOptions();
        return view('dashboard.admin.quizzes.create', compact('nextOptions'));
    }

    public function quizzesStore(Request $request)
    {
        $validated = $request->validate([
            'node_id' => 'required|string|unique:quiz_nodes,node_id',
            'title' => 'required|string|max:255',
            'question' => 'required|string',
            'type' => 'required|in:single,multi',
            'options' => 'required|array|min:1',
            'options.*.code' => 'required|string',
            'options.*.label' => 'required|string',
            'options.*.next' => 'nullable|string',
            'options.*.packages' => 'nullable|array',
            'options.*.packages.*' => 'string',
            'x' => 'integer|min:0',
            'y' => 'integer|min:0'
        ]);

        QuizNode::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz node created successfully!');
    }

    protected function getNextOptions()
    {
        // Get all node IDs from database
        $nodeIds = QuizNode::pluck('node_id')->toArray();
        
        // Get all terminal codes from config
        $terminals = config('quiz.terminals', []);
        
        // Combine and sort
        $allOptions = array_unique(array_merge($nodeIds, $terminals));
        sort($allOptions);
        
        return $allOptions;
    }

    public function quizzesEdit(QuizNode $quizNode)
    {
        $nextOptions = $this->getNextOptions();
        return view('dashboard.admin.quizzes.edit', compact('quizNode', 'nextOptions'));
    }

    public function quizzesUpdate(Request $request, QuizNode $quizNode)
    {
        $validated = $request->validate([
            'node_id' => 'required|string|unique:quiz_nodes,node_id,' . $quizNode->id,
            'title' => 'required|string|max:255',
            'question' => 'required|string',
            'type' => 'required|in:single,multi',
            'options' => 'required|array|min:1',
            'options.*.code' => 'required|string',
            'options.*.label' => 'required|string',
            'options.*.next' => 'nullable|string',
            'options.*.packages' => 'nullable|array',
            'options.*.packages.*' => 'string',
            'x' => 'integer|min:0',
            'y' => 'integer|min:0'
        ]);

        $quizNode->update($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz node updated successfully!');
    }

    public function quizzesDestroy(QuizNode $quizNode)
    {
        $quizNode->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz node deleted successfully!');
    }

    public function quizzesFlowchart()
    {
        return view('dashboard.admin.flowchart.builder');
    }

    public function getPackagesByVisaType(Request $request)
    {
        $visaType = $request->get('visa_type');
        
        if (!$visaType) {
            return response()->json([]);
        }
        
        $packages = \App\Models\Package::where('visa_type', $visaType)
            ->where('active', true)
            ->orderBy('price_cents')
            ->get(['id', 'code', 'name', 'price_cents']);
            
        return response()->json($packages);
    }

    public function saveQuizFlowchart(Request $request)
    {
        $nodes = $request->input('nodes', []);

        foreach ($nodes as $nodeData) {
            // Accept either 'id' or 'node_id' from client
            $nodeId = $nodeData['id'] ?? $nodeData['node_id'] ?? null;
            if (!$nodeId) { continue; }
            $model = QuizNode::where('node_id', $nodeId)->first();
            if (!$model) { continue; }

            // Normalize options if present
            $incomingOptions = $nodeData['options'] ?? null;
            $normalizedOptions = null;
            if (is_array($incomingOptions)) {
                $normalizedOptions = [];
                foreach ($incomingOptions as $opt) {
                    if (!is_array($opt)) { continue; }
                    $code = $opt['code'] ?? $opt['value'] ?? null;
                    $label = $opt['label'] ?? '';
                    $next = $opt['next'] ?? null;
                    if ($code === null && $label === '' && $next === null) { continue; }
                    $entry = [
                        'code' => $code,
                        'label' => $label,
                        'next' => $next,
                    ];
                    // Preserve legacy flags if provided by UI
                    if (array_key_exists('eligible', $opt)) { $entry['eligible'] = (bool) $opt['eligible']; }
                    if (array_key_exists('ineligible', $opt)) { $entry['ineligible'] = $opt['ineligible']; }
                    $normalizedOptions[] = $entry;
                }
            }

            // Build update payload; only set keys that are present to avoid unintended nulling
            $payload = [
                'x' => $nodeData['x'] ?? $model->x ?? 100,
                'y' => $nodeData['y'] ?? $model->y ?? 100,
            ];
            if (array_key_exists('title', $nodeData)) { $payload['title'] = (string) $nodeData['title']; }
            if (array_key_exists('question', $nodeData)) { $payload['question'] = (string) $nodeData['question']; }
            if (array_key_exists('type', $nodeData)) { $payload['type'] = (string) $nodeData['type']; }
            if ($normalizedOptions !== null) { $payload['options'] = $normalizedOptions; }

            $model->update($payload);
        }

        return response()->json(['success' => true, 'message' => 'Flowchart saved successfully!']);
    }
    
    // Case Manager Management Methods
    public function viewCaseManager(User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        // Get case manager's statistics using managedCases relationship
        $totalCases = $user->managedCases()->count();
        $activeCases = $user->managedCases()->whereIn('status', ['pending', 'in_progress', 'document_review', 'pending_review'])->count();
        $completedCases = $user->managedCases()->where('status', 'completed')->count();
        $recentCases = $user->managedCases()->with('user')->latest()->limit(10)->get();
        
        // Additional statistics for the dashboard
        $pendingCases = $user->managedCases()->where('status', 'pending')->count();
        $thisMonthCases = $user->managedCases()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $completedThisMonth = $user->managedCases()
            ->where('status', 'completed')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        // For processed count, consider any status change this month
        $processedThisMonth = $user->managedCases()
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        $avgProcessingDays = $this->calculateAverageProcessingTime($user->id);
        
        // Recent activity - get recently updated/created cases
        $recentActivity = $user->managedCases()
            ->select('id', 'status', 'updated_at', 'created_at')
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->map(function($case) {
                $action = 'Updated case';
                $timeAgo = $case->updated_at->diffForHumans();
                $color = 'info';
                
                if ($case->status === 'completed') {
                    $action = 'Completed case';
                    $color = 'success';
                } elseif ($case->created_at->eq($case->updated_at)) {
                    $action = 'Assigned new case';
                    $color = 'warning';
                } elseif ($case->status === 'in_progress') {
                    $action = 'Started processing';
                    $color = 'primary';
                }
                
                return [
                    'id' => $case->id,
                    'action' => $action,
                    'time' => $timeAgo,
                    'color' => $color
                ];
            });
        
        return view('dashboard.admin.case-managers.view', compact(
            'user', 'totalCases', 'activeCases', 'completedCases', 'recentCases', 
            'pendingCases', 'thisMonthCases', 'completedThisMonth', 'processedThisMonth',
            'avgProcessingDays', 'recentActivity'
        ));
    }
    
    private function calculateAverageProcessingTime($caseManagerId)
    {
        $completedCases = Application::where('case_manager_id', $caseManagerId)
            ->where('status', 'completed')
            ->whereNotNull('submitted_at')
            ->get();
        
        if ($completedCases->count() === 0) {
            return 0;
        }
        
        $totalDays = 0;
        foreach ($completedCases as $case) {
            $submittedAt = $case->submitted_at;
            $completedAt = $case->updated_at;
            $days = $submittedAt->diffInDays($completedAt);
            $totalDays += $days;
        }
        
        return round($totalDays / $completedCases->count(), 1);
    }
    
    public function editCaseManager(User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        return view('dashboard.admin.case-managers.edit', compact('user'));
    }
    
    public function updateCaseManager(Request $request, User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        $message = 'Case Manager updated successfully.';
        if ($request->filled('password')) {
            $message .= ' Password has been changed.';
        }
        
        return redirect()->route('admin.case-managers.view', $user)->with('success', $message);
    }
    
    public function caseManagerCases(User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        $cases = $user->applications()->with(['user', 'attorney'])->paginate(15);
        
        return view('dashboard.admin.case-managers.cases', compact('user', 'cases'));
    }
    
    public function suspendCaseManager(User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        // Check if there are migration columns for suspension
        try {
            $user->update(['is_suspended' => true]);
            $message = 'Case Manager suspended successfully.';
        } catch (\Exception $e) {
            // If no is_suspended column, we can still show the action worked
            $message = 'Case Manager suspension recorded.';
        }
        
        return redirect()->route('admin.case-managers')->with('success', $message);
    }
    
    public function activateCaseManager(User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        // Check if there are migration columns for suspension
        try {
            $user->update(['is_suspended' => false]);
            $message = 'Case Manager activated successfully.';
        } catch (\Exception $e) {
            // If no is_suspended column, we can still show the action worked
            $message = 'Case Manager activation recorded.';
        }
        
        return redirect()->route('admin.case-managers')->with('success', $message);
    }
    
    public function resetCaseManagerPassword(Request $request, User $user)
    {
        // Ensure the user is a case manager
        if (!$user->hasRole('case_manager')) {
            return redirect()->route('admin.case-managers')->with('error', 'User is not a case manager.');
        }
        
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'notify_user' => 'nullable|boolean',
        ]);
        
        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        // Optional: Send email notification (implement if needed)
        if ($request->filled('notify_user') && $request->notify_user) {
            // TODO: Send email notification to user about password change
            // You can implement this using Laravel's Mail functionality
        }
        
        return redirect()->route('admin.case-managers.view', $user)->with('success', 'Password reset successfully for ' . $user->name);
    }
}
