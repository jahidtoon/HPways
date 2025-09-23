<?php
// Debug Feedback Issue Page
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$app->boot();

use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;

try {
    echo "<h1>Feedback Debug Page</h1>";
    
    $caseId = $_GET['id'] ?? 22;
    echo "<h2>Case ID: $caseId</h2>";
    
    // Get case with feedback
    $case = Application::with(['user', 'feedback.user'])->find($caseId);
    
    if (!$case) {
        echo "<p>Case not found!</p>";
        exit;
    }
    
    echo "<h3>Case Information:</h3>";
    echo "<p>Status: " . $case->status . "</p>";
    echo "<p>Attorney ID: " . ($case->attorney_id ?? 'Not assigned') . "</p>";
    
    echo "<h3>Feedback Information:</h3>";
    echo "<p>Feedback count: " . $case->feedback->count() . "</p>";
    
    if ($case->feedback->count() > 0) {
        echo "<h4>Feedback List:</h4>";
        foreach ($case->feedback as $feedback) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            echo "<p><strong>ID:</strong> " . $feedback->id . "</p>";
            echo "<p><strong>Type:</strong> " . $feedback->type . "</p>";
            echo "<p><strong>Content:</strong> " . $feedback->content . "</p>";
            echo "<p><strong>Attorney:</strong> " . ($feedback->user ? $feedback->user->name : 'N/A') . "</p>";
            echo "<p><strong>Created:</strong> " . $feedback->created_at . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No feedback found</p>";
    }
    
    // Test feedback creation
    if (isset($_POST['test_feedback'])) {
        echo "<h3>Creating Test Feedback:</h3>";
        $attorney = User::find(40); // Jahidul
        if ($attorney) {
            $newFeedback = $case->feedback()->create([
                'attorney_id' => $attorney->id,
                'content' => 'Debug test feedback: ' . date('Y-m-d H:i:s'),
                'type' => 'general'
            ]);
            echo "<p style='color: green;'>Feedback created successfully! ID: " . $newFeedback->id . "</p>";
            echo "<script>window.location.reload();</script>";
        } else {
            echo "<p style='color: red;'>Attorney not found!</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<form method="POST">
    <button type="submit" name="test_feedback" value="1">Create Test Feedback</button>
</form>

<p><a href="/dashboard/attorney/case/<?= $caseId ?>">Back to Case Review</a></p>