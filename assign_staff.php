<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Assigning Staff to Applications ===\n";

// Get staff IDs
$caseManager = App\Models\User::role('case_manager')->first();
$attorney = App\Models\User::role('attorney')->first();

if ($caseManager) {
    App\Models\Application::where('id', 2)->update(['case_manager_id' => $caseManager->id]);
    App\Models\Application::where('id', 6)->update(['case_manager_id' => $caseManager->id]);
    echo "Case Manager assigned: " . $caseManager->name . "\n";
}

if ($attorney) {
    App\Models\Application::where('id', 2)->update(['attorney_id' => $attorney->id]);
    App\Models\Application::where('id', 6)->update(['attorney_id' => $attorney->id]);
    echo "Attorney assigned: " . $attorney->name . "\n";
}

echo "\n=== Updated Application 2 ===\n";
$app = App\Models\Application::with(['caseManager', 'attorney'])->find(2);
echo "Case Manager: " . ($app->caseManager->name ?? 'Not assigned') . "\n";
echo "Attorney: " . ($app->attorney->name ?? 'Not assigned') . "\n";

echo "Staff assignment completed!\n";
?>
