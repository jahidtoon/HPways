<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    echo view('dashboard.attorney.index')->render();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
