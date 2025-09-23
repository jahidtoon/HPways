<?php
// Attorney Actions Handler - Process feedback, approve, reject
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$app->boot();

use App\Models\Application;
use App\Models\User;
use App\Models\Feedback;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /attorney-dashboard.php');
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    $caseId = $_POST['case_id'] ?? '';
    
    if (!$caseId) {
        throw new Exception('Case ID is required');
    }
    
    $case = Application::findOrFail($caseId);
    $attorney = User::role('attorney')->first();
    
    if (!$attorney) {
        throw new Exception('Attorney not found');
    }
    
    switch ($action) {
        case 'approve':
            $case->update(['status' => 'approved']);
            
            // Add approval feedback
            Feedback::create([
                'application_id' => $case->id,
                'user_id' => $attorney->id,
                'message' => $_POST['approval_notes'] ?? 'Application approved by attorney.',
                'type' => 'approval',
                'created_at' => now()
            ]);
            
            $message = 'Application approved successfully!';
            break;
            
        case 'reject':
            $rejectionReason = $_POST['rejection_reason'] ?? '';
            if (empty($rejectionReason)) {
                throw new Exception('Rejection reason is required');
            }
            
            $case->update(['status' => 'rejected']);
            
            // Add rejection feedback
            Feedback::create([
                'application_id' => $case->id,
                'user_id' => $attorney->id,
                'message' => $rejectionReason,
                'type' => 'rejection',
                'created_at' => now()
            ]);
            
            $message = 'Application rejected with reason provided.';
            break;
            
        case 'feedback':
            $feedbackMessage = $_POST['feedback_message'] ?? '';
            $feedbackType = $_POST['feedback_type'] ?? 'general';
            
            if (empty($feedbackMessage)) {
                throw new Exception('Feedback message is required');
            }
            
            // Update application status based on feedback type
            $newStatus = match($feedbackType) {
                'rfe' => 'rfe_issued',
                'document_issue' => 'documents_required',
                default => 'attorney_feedback_provided'
            };
            
            $case->update(['status' => $newStatus]);
            
            // Create feedback entry
            Feedback::create([
                'application_id' => $case->id,
                'user_id' => $attorney->id,
                'message' => $feedbackMessage,
                'type' => $feedbackType,
                'created_at' => now()
            ]);
            
            $message = 'Feedback provided successfully!';
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
    // Redirect back with success message
    header('Location: /attorney-review-case.php?id=' . $caseId . '&success=' . urlencode($message));
    exit;
    
} catch (Exception $e) {
    // Redirect back with error message
    header('Location: /attorney-review-case.php?id=' . ($caseId ?? '') . '&error=' . urlencode($e->getMessage()));
    exit;
}
?>