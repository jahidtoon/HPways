<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Application Detail Data ===\n";

// Test application 2
$application = App\Models\Application::with(['user', 'documents', 'payments', 'caseManager', 'attorney'])
    ->find(2);

if ($application) {
    echo "Application ID: " . $application->id . "\n";
    echo "User: " . ($application->user->name ?? 'No user') . "\n";
    echo "Visa Type: " . ($application->visa_type ?? 'No visa type') . "\n";
    echo "Status: " . $application->status . "\n";
    echo "Documents Count: " . $application->documents->count() . "\n";
    echo "Payments Count: " . $application->payments->count() . "\n";
    
    if ($application->payments->count() > 0) {
        foreach ($application->payments as $payment) {
            echo "Payment: $" . number_format($payment->amount_cents / 100, 2) . " - " . $payment->status . "\n";
        }
    }
    
    echo "Case Manager: " . ($application->caseManager->name ?? 'Not assigned') . "\n";
    echo "Attorney: " . ($application->attorney->name ?? 'Not assigned') . "\n";
    
} else {
    echo "Application not found!\n";
}

echo "\n=== Required Documents for H1B ===\n";
$requiredDocs = App\Models\RequiredDocument::where('visa_type', 'H1B')
    ->orWhere('visa_type', 'all')
    ->get();

foreach ($requiredDocs as $doc) {
    echo "- " . $doc->label . " (" . $doc->code . ")\n";
}

echo "\n=== Available Staff ===\n";
$caseManagers = App\Models\User::role('case_manager')->get();
$attorneys = App\Models\User::role('attorney')->get();

echo "Case Managers: " . $caseManagers->count() . "\n";
foreach ($caseManagers as $cm) {
    echo "  - " . $cm->name . " (" . $cm->email . ")\n";
}

echo "Attorneys: " . $attorneys->count() . "\n";
foreach ($attorneys as $attorney) {
    echo "  - " . $attorney->name . " (" . $attorney->email . ")\n";
}
?>
