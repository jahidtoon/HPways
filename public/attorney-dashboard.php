<?php
// This is a direct entry point to the attorney dashboard
// It bypasses the regular Laravel routing

// Initialize the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Force Laravel to load the attorney dashboard view
try {
    // Set any variables that the view might need
    $user = (object)[
        'name' => 'Demo Attorney',
        'email' => 'attorney@example.com'
    ];
    
    // Load and render the view
    echo view('dashboard.attorney.index', ['user' => $user])->render();
} catch (Exception $e) {
    // Display any errors that might occur
    echo "<h1>Error Loading Attorney Dashboard</h1>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
