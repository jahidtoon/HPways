<?php
// Attorney Cases Page - Shows all cases assigned to the attorney
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$app->boot();

use App\Models\Application;
use App\Models\User;

try {
    // Get the currently logged in attorney (attarny@gmail.com)
    $attorney = User::where('email', 'attarny@gmail.com')->first();
    
    // If that specific attorney doesn't exist, get first attorney
    if (!$attorney) {
        $attorney = User::role('attorney')->first();
    }
    
    if (!$attorney) {
        throw new Exception('No attorney found in the system');
    }
    
    // Get all applications assigned to this attorney
    $assignedCases = Application::where('attorney_id', $attorney->id)
        ->with(['user', 'documents', 'feedback'])
        ->orderBy('created_at', 'desc')
        ->get();
        
    echo view('dashboard.attorney.cases', compact('assignedCases'))->render();
} catch (Exception $e) {
    echo "<h1>Error Loading Attorney Cases</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>