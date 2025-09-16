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
            
        $totalApplicants = $roleUserCount['applicant'] ?? User::whereDoesntHave('roles')->count();
        $totalCaseManagers = $roleUserCount['case_manager'] ?? 0;
        $totalAttorneys = $roleUserCount['attorney'] ?? 0;
        $totalAdmins = ($roleUserCount['admin'] ?? 0) + ($roleUserCount['big_admin'] ?? 0);
        
        // Get recent users
        $recentUsers = User::with('roles')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get application statistics
        $totalApplications = Application::count();
        $pendingApplications = Application::whereIn('status', ['pending', 'pending_review', 'pending_attorney_review'])->count();
        $approvedApplications = Application::where('status', 'approved')->count();
        $rejectedApplications = Application::where('status', 'rejected')->count();

        // Get recent applications
        $recentApplications = Application::with('user')
            ->orderBy('created_at', 'desc')
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
            $users = User::with('roles')->paginate(15);
            $roles = Role::all();
            
            return view('dashboard.admin.users', compact('users', 'roles'));
        } catch (\Exception $e) {
            // Fallback for when database might not be accessible
            return view('dashboard.admin.users', [
                'users' => [], 
                'roles' => [],
                'error' => 'Could not load users: ' . $e->getMessage()
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
    
    public function createUser()
    {
        $roles = Role::where('name', '!=', 'applicant')->get();
        return view('dashboard.admin.create-user', compact('roles'));
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
                'feedback', 
                'payments',
                'caseManager', 
                'attorney',
                'assignedPrinter'
            ])->findOrFail($id);
            
            // Get all available staff for assignment
            $availableCaseManagers = User::role('case_manager')->get();
            $availableAttorneys = User::role('attorney')->get(); 
            $availablePrinters = User::role('printing_department')->get();
            
            // Get required documents for this visa type
            $requiredDocuments = \App\Models\RequiredDocument::where('visa_type', $application->visa_type)
                ->orWhere('visa_type', 'all')
                ->where('active', 1)
                ->get();
            
            // Calculate completion percentage
            $totalRequired = $requiredDocuments->count();
            $submitted = $application->documents->count();
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
            $caseManagers = User::role('case_manager')->with('applications')->paginate(15);
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
            $attorneys = User::role('attorney')->with('applications')->paginate(15);
            $totalCases = Application::whereNotNull('attorney_id')->count();
            
            return view('dashboard.admin.attorneys', compact('attorneys', 'totalCases'));
        } catch (\Exception $e) {
            // Create empty collection for fallback
            $attorneys = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]), 0, 15, 1, ['path' => request()->url()]
            );
            return view('dashboard.admin.attorneys', [
                'attorneys' => $attorneys, 
                'totalCases' => 0,
                'error' => 'Could not load attorneys: ' . $e->getMessage()
            ]);
        }
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
            $application->save();
            
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
        $visaTypes = Application::select('visa_type')->distinct()->pluck('visa_type')->filter()->values();
        return view('dashboard.admin.packages.create', compact('visaTypes'));
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
        return view('dashboard.admin.quizzes.create');
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
            'x' => 'integer|min:0',
            'y' => 'integer|min:0'
        ]);

        QuizNode::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz node created successfully!');
    }

    public function quizzesEdit(QuizNode $quizNode)
    {
        return view('dashboard.admin.quizzes.edit', compact('quizNode'));
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

    public function getQuizNodes()
    {
        $nodes = QuizNode::all();
        return response()->json(['nodes' => $nodes]);
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
}
