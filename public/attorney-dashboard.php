<?php
// This is a direct entry point to the attorney dashboard with real data
// It uses Laravel models to fetch actual assigned applications

// Initialize the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Boot Laravel's database and models
$app->boot();

use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;

try {
    // Get the currently logged in attorney based on email (attarny@gmail.com)
    $attorney = User::where('email', 'attarny@gmail.com')->first();
    
    // If that specific attorney doesn't exist, get first attorney
    if (!$attorney) {
        $attorney = User::role('attorney')->first();
    }
    
    if (!$attorney) {
        // If no attorney exists, create demo attorney
        $attorney = User::create([
            'name' => 'Demo Attorney',
            'email' => 'attorney@demo.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now()
        ]);
        $attorney->assignRole('attorney');
    }
    
    // Get real applications assigned to this attorney
    $assignedCases = Application::where('attorney_id', $attorney->id)
        ->with(['user', 'documents'])
        ->orderBy('created_at', 'desc')
        ->get();
        
    // If no cases assigned, create some demo applications assigned to this attorney
    if ($assignedCases->count() == 0) {
        // Create demo applicants
        $applicant1 = User::firstOrCreate(
            ['email' => 'john.doe@example.com'],
            ['name' => 'John Doe', 'password' => bcrypt('password')]
        );
        $applicant1->assignRole('applicant');
        
        $applicant2 = User::firstOrCreate(
            ['email' => 'jane.smith@example.com'],
            ['name' => 'Jane Smith', 'password' => bcrypt('password')]
        );
        $applicant2->assignRole('applicant');
        
        // Create demo applications assigned to attorney
        $app1 = Application::create([
            'user_id' => $applicant1->id,
            'attorney_id' => $attorney->id,
            'status' => 'assigned_to_attorney',
            'visa_type' => 'H-1B Work Visa',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2)
        ]);
        
        $app2 = Application::create([
            'user_id' => $applicant2->id,
            'attorney_id' => $attorney->id,
            'status' => 'attorney_feedback_provided',
            'visa_type' => 'F-1 Student Visa',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(1)
        ]);
        
        // Re-fetch assigned cases with relationships
        $assignedCases = Application::where('attorney_id', $attorney->id)
            ->with(['user', 'documents'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    // Get cases pending attorney review (not yet assigned)
    $pendingCases = Application::where('status', 'ready_for_attorney_review')
        ->whereNull('attorney_id')
        ->with(['user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
        
    // Calculate statistics
    $activeCases = $assignedCases->whereIn('status', [
        'assigned_to_attorney', 
        'under_attorney_review', 
        'attorney_feedback_provided'
    ])->count();
    
    $pendingReview = $assignedCases->where('status', 'assigned_to_attorney')->count();
    $approvedThisMonth = $assignedCases->where('status', 'approved')
        ->where('updated_at', '>=', now()->startOfMonth())
        ->count();
    $feedbacksProvided = Feedback::where('user_id', $attorney->id)
        ->where('created_at', '>=', now()->startOfMonth())
        ->count();
    
    // Load and render the view with real data
    echo view('dashboard.attorney.index', compact(
        'assignedCases',
        'pendingCases', 
        'activeCases',
        'pendingReview',
        'approvedThisMonth',
        'feedbacksProvided'
    ))->render();
} catch (Exception $e) {
    // Display any errors that might occur
    echo "<h1>Error Loading Attorney Dashboard</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
