<?php
// Test printing dashboard access

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate login for printing department user
$user = App\Models\User::where('email', 'printer@hpways.com')->first();
if (!$user) {
    echo "Printing user not found\n";
    exit(1);
}

echo "Found printing user: {$user->name} ({$user->email})\n";
echo "User roles: " . $user->roles->pluck('name')->implode(', ') . "\n";

// Check approved applications
$approvedApps = App\Models\Application::where('status', 'approved')->count();
echo "Approved applications: {$approvedApps}\n";

// Check applications in print queue
$queueApps = App\Models\Application::where('status', 'in_print_queue')->count();
echo "Applications in print queue: {$queueApps}\n";

// Check applications currently printing
$printingApps = App\Models\Application::where('status', 'printing')->count();
echo "Applications currently printing: {$printingApps}\n";

// Check printed applications
$printedApps = App\Models\Application::where('status', 'printed')->count();
echo "Printed applications: {$printedApps}\n";

echo "\nPrinting dashboard test completed successfully!\n";
?>