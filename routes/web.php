<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ApplicationIntakeController;
use App\Http\Controllers\PackageSelectionController;
use App\Http\Controllers\PaymentIntentController;
use App\Http\Controllers\UserApplicationController;
use App\Http\Controllers\DocumentUploadController;
use App\Http\Controllers\ApplicationReviewController;
use App\Http\Controllers\FeedbackListController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\LockboxSearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [QuizController::class, 'newQuiz']);

Route::post('/quiz/advance', [QuizController::class,'advance'])->name('quiz.advance');
Route::get('/quiz/state', [QuizController::class,'state'])->name('quiz.state');
Route::post('/quiz/reset', [QuizController::class,'reset'])->name('quiz.reset');
Route::get('/eligibility-quiz', [QuizController::class,'newQuiz'])->name('eligibility.quiz');
// Shared quiz spec endpoint (single source of truth)
Route::get('/api/quiz/spec', [QuizController::class,'specJson'])->name('quiz.spec.json');
Route::post('/quiz/tag-terminal', [QuizController::class,'tagTerminal'])->name('quiz.tagTerminal');

Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// Auth routes
Route::get('/login', [LoginController::class,'show'])->name('login');
Route::post('/login', [LoginController::class,'login'])->name('login.perform');
Route::match(['get', 'post'], '/logout', [LoginController::class,'logout'])->name('logout');
Route::get('/register', [RegisterController::class,'show'])->name('register');
Route::post('/register', [RegisterController::class,'register'])->name('register.perform');

// Test route to verify the application is working
Route::get('/test', function () {
    return response()->json(['status' => 'working', 'timestamp' => now()]);
});

// Protected Dashboard routes - require authentication
Route::middleware(['auth'])->group(function () {
    
    // Main dashboard route - redirects based on role
    Route::get('/dashboard', function() {
        $user = auth()->user();
        $userRole = $user->getRoleNames()->first() ?? 'applicant';
        
        switch($userRole) {
            case 'big_admin':
                return redirect()->route('admin.dashboard');
            case 'case_manager':
                return redirect()->route('dashboard.case-manager.index');
            case 'attorney':
                return redirect()->route('dashboard.attorney.index');
            case 'printing_department':
                return redirect()->route('dashboard.printing.index');
            default:
                return redirect()->route('dashboard.applicant.index');
        }
    })->name('dashboard');

    // Applicant Dashboard Routes
    Route::prefix('dashboard/applicant')->name('dashboard.applicant.')->group(function() {
        Route::get('/', [App\Http\Controllers\ApplicantController::class, 'dashboard'])->name('index');
        Route::get('/applications', [App\Http\Controllers\ApplicantController::class, 'applications'])->name('applications');
    Route::get('/application/{id}', [App\Http\Controllers\ApplicantController::class, 'viewApplication'])->name('application.view');
    Route::get('/documents', [App\Http\Controllers\ApplicantController::class, 'documents'])->name('documents');
    Route::get('/documents/upload/{application?}', [App\Http\Controllers\ApplicantController::class, 'uploadDocuments'])->name('documents.upload');
        Route::get('/payments', [App\Http\Controllers\ApplicantController::class, 'payments'])->name('payments');
    Route::get('/resources', [App\Http\Controllers\ApplicantController::class, 'resources'])->name('resources');
        Route::get('/support', [App\Http\Controllers\ApplicantController::class, 'support'])->name('support');
        Route::get('/settings', [App\Http\Controllers\ApplicantController::class, 'settings'])->name('settings');
        Route::get('/reports', [App\Http\Controllers\ApplicantController::class, 'reports'])->name('reports');

        // Applicant meetings
        Route::get('/meetings', [App\Http\Controllers\MeetingsController::class, 'applicantIndex'])->name('meetings.index');
    });

    // Shared progress endpoints (auth required)
    Route::post('/applications/{application}/progress/mark-printed', [App\Http\Controllers\ApplicationProgressController::class, 'markPrinted']);
    Route::post('/applications/{application}/progress/mark-delivered', [App\Http\Controllers\ApplicationProgressController::class, 'markDelivered']);
    Route::post('/applications/{application}/progress/confirm-received', [App\Http\Controllers\ApplicationProgressController::class, 'confirmReceived']);
    
    // Case Manager Dashboard Routes
    Route::prefix('dashboard/case-manager')->name('dashboard.case-manager.')->middleware(['auth', 'role:case_manager'])->group(function() {
        Route::get('/', [App\Http\Controllers\CaseManagerController::class, 'index'])->name('index');
        Route::get('/applications', [App\Http\Controllers\CaseManagerController::class, 'applications'])->name('applications');
        Route::get('/all-applications', [App\Http\Controllers\CaseManagerController::class, 'allApplications'])->name('all-applications');
        Route::get('/case/{id}', [App\Http\Controllers\CaseManagerController::class, 'viewCase'])->name('case.view');
        Route::post('/case/{id}/assign-self', [App\Http\Controllers\CaseManagerController::class, 'assignSelf'])->name('case.assign-self');
        Route::post('/case/{id}/assign-case-manager', [App\Http\Controllers\CaseManagerController::class, 'assignCaseManager'])->name('case.assign-case-manager');
        Route::post('/case/{id}/assign-attorney', [App\Http\Controllers\CaseManagerController::class, 'assignAttorney'])->name('case.assign-attorney');
        Route::post('/case/{id}/request-documents', [App\Http\Controllers\CaseManagerController::class, 'requestDocuments'])->name('case.request-documents');
        Route::post('/case/{id}/mark-ready', [App\Http\Controllers\CaseManagerController::class, 'markReady'])->name('mark-ready');
        Route::get('/attorneys', [App\Http\Controllers\CaseManagerController::class, 'attorneys'])->name('attorneys');

        // Case manager meetings
        Route::get('/meetings', [App\Http\Controllers\MeetingsController::class, 'caseManagerIndex'])->name('meetings.index');
        Route::get('/meetings/{meeting}/schedule', [App\Http\Controllers\MeetingsController::class, 'editSchedule'])->name('meetings.schedule');
        Route::post('/meetings/{meeting}/schedule', [App\Http\Controllers\MeetingsController::class, 'updateSchedule'])->name('meetings.schedule.update');
        Route::post('/meetings/{meeting}/approve', [App\Http\Controllers\MeetingsController::class, 'approve'])->name('meetings.approve');
        Route::post('/meetings/{meeting}/decline', [App\Http\Controllers\MeetingsController::class, 'decline'])->name('meetings.decline');
        Route::post('/meetings/{meeting}/cancel', [App\Http\Controllers\MeetingsController::class, 'cancel'])->name('meetings.cancel');
        
        // Reports & Analytics
        Route::get('/reports', [App\Http\Controllers\CaseManagerController::class, 'reports'])->name('reports');
        Route::get('/analytics', [App\Http\Controllers\CaseManagerController::class, 'analytics'])->name('analytics');
        
        // Tools
        Route::get('/documents', [App\Http\Controllers\CaseManagerController::class, 'documents'])->name('documents');
        Route::get('/notifications', [App\Http\Controllers\CaseManagerController::class, 'notifications'])->name('notifications');
        Route::get('/settings', [App\Http\Controllers\CaseManagerController::class, 'settings'])->name('settings');
    });
    
    // Attorney Dashboard Routes  
    Route::prefix('dashboard/attorney')->name('dashboard.attorney.')->middleware(['auth', 'role:attorney'])->group(function() {
        Route::get('/', [App\Http\Controllers\AttorneyController::class, 'index'])->name('index');
        Route::get('/cases', [App\Http\Controllers\AttorneyController::class, 'cases'])->name('cases');
        Route::get('/case/{id}', [App\Http\Controllers\AttorneyController::class, 'reviewCase'])->name('case.review');
        Route::post('/case/{id}/accept', [App\Http\Controllers\AttorneyController::class, 'acceptCase'])->name('case.accept');
        Route::post('/case/{id}/feedback', [App\Http\Controllers\AttorneyController::class, 'provideFeedback'])->name('case.feedback');
        Route::post('/case/{id}/approve', [App\Http\Controllers\AttorneyController::class, 'approveApplication'])->name('case.approve');
        Route::post('/case/{id}/reject', [App\Http\Controllers\AttorneyController::class, 'rejectApplication'])->name('case.reject');
        Route::get('/reviews', [App\Http\Controllers\AttorneyController::class, 'reviews'])->name('reviews');

        // Attorney meetings
        Route::get('/meetings', [App\Http\Controllers\MeetingsController::class, 'attorneyIndex'])->name('meetings.index');
        Route::post('/case/{id}/meetings/request', [App\Http\Controllers\MeetingsController::class, 'storeRequest'])->name('meetings.request');
        Route::post('/meetings/{meeting}/cancel', [App\Http\Controllers\MeetingsController::class, 'cancel'])->name('meetings.cancel');
    });
    
    // Printing Department Dashboard Routes
    Route::prefix('dashboard/printing')->name('dashboard.printing.')->middleware(['auth', 'role:printing_department'])->group(function() {
        Route::get('/', [App\Http\Controllers\PrintingDepartmentController::class, 'index'])->name('index');
        Route::get('/queue', [App\Http\Controllers\PrintingDepartmentController::class, 'printQueue'])->name('queue');
        Route::get('/management', [App\Http\Controllers\PrintingDepartmentController::class, 'management'])->name('management');
        Route::get('/shipping', [App\Http\Controllers\PrintingDepartmentController::class, 'shipping'])->name('shipping');
        Route::post('/add-to-queue/{id}', [App\Http\Controllers\PrintingDepartmentController::class, 'addToPrintQueue'])->name('add-to-queue');
        Route::post('/prepare-shipment/{id}', [App\Http\Controllers\PrintingDepartmentController::class, 'prepareShipment'])->name('prepare-shipment');
        Route::post('/ship/{id}', [App\Http\Controllers\PrintingDepartmentController::class, 'ship'])->name('ship');
    });
});

// Big Admin Dashboard Routes - Middleware temporarily disabled for development
// First admin group removed due to duplicate routes

// Formerly protected workflow routes (now public)
Route::post('/intake/applications', [ApplicationIntakeController::class,'store'])->name('intake.application.store');
Route::get('/me/application', [UserApplicationController::class,'current'])->name('me.application');
// Documents
Route::get('/applications/{application}/documents', [DocumentUploadController::class,'index'])->name('applications.documents.index');
Route::post('/applications/{application}/documents', [DocumentUploadController::class,'store'])->name('applications.documents.store');
Route::patch('/applications/{application}/documents/{document}/translation', [DocumentUploadController::class,'updateTranslationStatus'])->name('applications.documents.translation');
Route::get('/applications/{application}/payments-list', [App\Http\Controllers\ApplicationPaymentsController::class,'index'])->name('applications.payments.list');
// Package selection
Route::get('/applications/{application}/packages', [PackageSelectionController::class,'index'])->name('applications.packages.index');
Route::post('/applications/{application}/packages', [PackageSelectionController::class,'store'])->name('applications.packages.select');
// Payments (placeholder intent + confirm)
Route::get('/applications/{application}/payment-intent', [PaymentIntentController::class,'create'])->name('payments.intent');
Route::post('/payments/{payment}/confirm', [PaymentIntentController::class,'confirm'])->name('payments.confirm');
Route::get('/payments/{payment}/success', [PaymentIntentController::class,'success'])->name('payments.success');
// Review workflow
Route::post('/applications/{application}/request-review', [ApplicationReviewController::class,'requestReview'])->name('applications.request_review');
Route::post('/applications/{application}/review/start', [ApplicationReviewController::class,'startReview'])->name('applications.review.start');
Route::post('/applications/{application}/review/rfe', [ApplicationReviewController::class,'issueRfe'])->name('applications.review.rfe');
Route::post('/applications/{application}/review/respond', [ApplicationReviewController::class,'applicantRespond'])->name('applications.review.respond');
Route::post('/applications/{application}/review/ready', [ApplicationReviewController::class,'markReadyToFile'])->name('applications.review.ready');
Route::post('/applications/{application}/review/filed', [ApplicationReviewController::class,'markFiled'])->name('applications.review.filed');
Route::get('/applications/{application}/feedback', [FeedbackListController::class,'index'])->name('applications.feedback.index');

// Payment provider webhooks (public endpoint - add signature validation in production)
Route::post('/webhooks/payments',[App\Http\Controllers\PaymentWebhookController::class,'handle'])->name('webhooks.payments');

// Admin Routes (public per request)
Route::prefix('admin')->name('admin.')->group(function () {
    // Use the real controller for the dashboard so data reflects actual applications/users
    Route::get('/', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminDashboardController::class, 'userManagement'])->name('users');
    Route::get('/users/{id}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('create-user');
    Route::post('/users/store', [AdminDashboardController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('delete-user');
    Route::post('/users/{user}/reset-password', [AdminDashboardController::class, 'resetPassword'])->name('reset-password');
    Route::post('/users/{user}/assign-role', [AdminDashboardController::class, 'assignRole'])->name('assign-role');
    
    // Staff Management
    Route::get('/case-managers', [AdminDashboardController::class, 'caseManagers'])->name('case-managers');
    Route::post('/case-managers/create', [AdminDashboardController::class, 'createCaseManager'])->name('case-managers.create');
    Route::get('/case-managers/{user}', [AdminDashboardController::class, 'viewCaseManager'])->name('case-managers.view');
    Route::get('/case-managers/{user}/edit', [AdminDashboardController::class, 'editCaseManager'])->name('case-managers.edit');
    Route::put('/case-managers/{user}', [AdminDashboardController::class, 'updateCaseManager'])->name('case-managers.update');
    Route::get('/case-managers/{user}/cases', [AdminDashboardController::class, 'caseManagerCases'])->name('case-managers.cases');
    Route::post('/case-managers/{user}/suspend', [AdminDashboardController::class, 'suspendCaseManager'])->name('case-managers.suspend');
    Route::post('/case-managers/{user}/activate', [AdminDashboardController::class, 'activateCaseManager'])->name('case-managers.activate');
    Route::post('/case-managers/{user}/reset-password', [AdminDashboardController::class, 'resetCaseManagerPassword'])->name('case-managers.reset-password');
    
    // Attorney Management Routes
    Route::get('/attorneys', [AdminDashboardController::class, 'attorneys'])->name('attorneys');
    Route::post('/attorneys/create', [AdminDashboardController::class, 'createAttorney'])->name('attorneys.create');
    Route::get('/attorneys/{user}', [AdminDashboardController::class, 'viewAttorney'])->name('attorneys.view');
    Route::get('/attorneys/{user}/edit', [AdminDashboardController::class, 'editAttorney'])->name('attorneys.edit');
    Route::put('/attorneys/{user}', [AdminDashboardController::class, 'updateAttorney'])->name('attorneys.update');
    Route::get('/attorneys/{user}/cases', [AdminDashboardController::class, 'attorneyCases'])->name('attorneys.cases');
    Route::get('/attorneys/{user}/performance', [AdminDashboardController::class, 'attorneyPerformance'])->name('attorneys.performance');
    Route::post('/attorneys/{user}/suspend', [AdminDashboardController::class, 'suspendAttorney'])->name('attorneys.suspend');
    Route::post('/attorneys/{user}/activate', [AdminDashboardController::class, 'activateAttorney'])->name('attorneys.activate');
    Route::post('/attorneys/{user}/reset-password', [AdminDashboardController::class, 'resetAttorneyPassword'])->name('attorneys.reset-password');
    
    // Application Management
    Route::get('/applications', [AdminDashboardController::class, 'applications'])->name('applications');
    Route::get('/applications/{id}', [AdminDashboardController::class, 'showApplication'])->name('application-detail');
    Route::put('/applications/{application}/status', [AdminDashboardController::class, 'updateApplicationStatus'])->name('update-application-status');
    
    // Staff Assignment Routes
    Route::post('/applications/{id}/assign-case-manager', [AdminDashboardController::class, 'assignCaseManager'])->name('assign-case-manager');
    Route::post('/applications/{id}/assign-attorney', [AdminDashboardController::class, 'assignAttorney'])->name('assign-attorney');
    Route::post('/applications/{id}/assign-printer', [AdminDashboardController::class, 'assignPrinter'])->name('assign-printer');
    
    // Reports & Analytics
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
    
    // System Settings
    Route::get('/settings', [AdminDashboardController::class, 'systemSettings'])->name('settings');
    
    // Payment Settings
    Route::get('/payment-settings', [AdminDashboardController::class, 'paymentSettings'])->name('payment-settings');
    Route::post('/payment-settings', [AdminDashboardController::class, 'updatePaymentSettings'])->name('payment-settings.update');
    Route::get('/payments', [AdminDashboardController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [AdminDashboardController::class, 'paymentDetails'])->name('payments.show');
    
    // Shipment Tracking
    Route::get('/shipment-tracking', [AdminDashboardController::class, 'shipmentTracking'])->name('shipment-tracking');

    // Admin Meetings
    Route::get('/meetings', [\App\Http\Controllers\MeetingsController::class, 'adminIndex'])->name('meetings.index');
    
    // Printing Staff Management
    Route::get('/printing-staff', [AdminDashboardController::class, 'printingStaff'])->name('printing-staff');
    Route::post('/printing-staff/create', [AdminDashboardController::class, 'createPrintingStaff'])->name('printing-staff.create');
    Route::get('/printing-staff/{id}/edit', [AdminDashboardController::class, 'editPrintingStaff'])->name('printing-staff.edit');
    Route::put('/printing-staff/{id}', [AdminDashboardController::class, 'updatePrintingStaff'])->name('printing-staff.update');
    Route::post('/printing-staff/{id}/toggle-status', [AdminDashboardController::class, 'togglePrintingStaffStatus'])->name('printing-staff.toggle-status');
    Route::delete('/printing-staff/{id}', [AdminDashboardController::class, 'deletePrintingStaff'])->name('printing-staff.delete');
    Route::post('/printing-staff/{id}/assign-jobs', [AdminDashboardController::class, 'assignPrintingJobs'])->name('printing-staff.assign-jobs');
    
    // Packages Management
    Route::prefix('packages')->name('packages.')->group(function() {
        Route::get('/', [AdminDashboardController::class, 'packagesIndex'])->name('index');
        Route::get('/create', [AdminDashboardController::class, 'packagesCreate'])->name('create');
        Route::post('/', [AdminDashboardController::class, 'packagesStore'])->name('store');
        Route::get('/{package}/edit', [AdminDashboardController::class, 'packagesEdit'])->name('edit');
        Route::put('/{package}', [AdminDashboardController::class, 'packagesUpdate'])->name('update');
        Route::post('/{package}/toggle', [AdminDashboardController::class, 'packagesToggleActive'])->name('toggle');
        Route::delete('/{package}', [AdminDashboardController::class, 'packagesDestroy'])->name('destroy');
        Route::get('/bulk-edit', [AdminDashboardController::class, 'packagesBulkEdit'])->name('bulk-edit');
        Route::post('/bulk-update', [AdminDashboardController::class, 'packagesBulkUpdate'])->name('bulk-update');
        Route::post('/bulk-create', [AdminDashboardController::class, 'packagesBulkCreate'])->name('bulk-create');
    });
    
    // Quiz Management
    Route::prefix('quizzes')->name('quizzes.')->group(function() {
        Route::get('/', [AdminDashboardController::class, 'quizzesIndex'])->name('index');
        Route::get('/create', [AdminDashboardController::class, 'quizzesCreate'])->name('create');
        Route::post('/', [AdminDashboardController::class, 'quizzesStore'])->name('store');
        Route::get('/{quizNode}/edit', [AdminDashboardController::class, 'quizzesEdit'])->name('edit');
        Route::put('/{quizNode}', [AdminDashboardController::class, 'quizzesUpdate'])->name('update');
        Route::delete('/{quizNode}', [AdminDashboardController::class, 'quizzesDestroy'])->name('destroy');
        Route::post('/save-flowchart', [AdminDashboardController::class, 'saveQuizFlowchart'])->name('save-flowchart');
    });
    
    // API Routes
    Route::get('/api/packages', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'getPackagesByVisaType'])->name('api.packages');
});

// Printing Department Dashboard Routes
Route::prefix('printing')->name('printing.')->group(function() {
    Route::get('/', [App\Http\Controllers\PrintingDepartmentController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\PrintingDepartmentController::class, 'index'])->name('dashboard-alt');
    Route::get('/documents', [App\Http\Controllers\PrintingDepartmentController::class, 'documents'])->name('documents');
    Route::get('/management', [App\Http\Controllers\PrintingDepartmentController::class, 'management'])->name('management');
    Route::get('/shipping', [App\Http\Controllers\PrintingDepartmentController::class, 'shipping'])->name('shipping');
    Route::get('/analytics', [App\Http\Controllers\PrintingDepartmentController::class, 'analytics'])->name('analytics');
    
    // Print Operations
    Route::post('/{application}/add-to-queue', [App\Http\Controllers\PrintingDepartmentController::class, 'addToPrintQueue'])->name('add-to-queue');
    Route::post('/{application}/mark-printing', [App\Http\Controllers\PrintingDepartmentController::class, 'markAsPrinting'])->name('mark-printing');
    Route::post('/{application}/mark-printed', [App\Http\Controllers\PrintingDepartmentController::class, 'markAsPrinted'])->name('mark-printed');
    Route::post('/bulk-print', [App\Http\Controllers\PrintingDepartmentController::class, 'bulkPrint'])->name('bulk-print');
    Route::post('/auto-add-approved', [App\Http\Controllers\PrintingDepartmentController::class, 'autoAddApproved'])->name('auto-add');
    Route::post('/sync-assigned', [App\Http\Controllers\PrintingDepartmentController::class, 'syncAssignedToQueue'])->name('sync-assigned');
    
    // Shipping Operations
    Route::post('/prepare-shipment', [App\Http\Controllers\PrintingDepartmentController::class, 'prepareShipment'])->name('prepare-shipment');
    Route::post('/shipment/{shipment}/ship', [App\Http\Controllers\PrintingDepartmentController::class, 'ship'])->name('ship');
    Route::post('/shipment/{shipment}/update-tracking', [App\Http\Controllers\PrintingDepartmentController::class, 'updateTrackingStatus'])->name('update-tracking');
    Route::get('/shipment/{shipment}/tracking', [App\Http\Controllers\PrintingDepartmentController::class, 'tracking'])->name('tracking');
    Route::post('/shipment/{shipment}/refresh-tracking', [App\Http\Controllers\PrintingDepartmentController::class, 'refreshTracking'])->name('refresh-tracking');

    // Printer-specific application detail view (documents only)
    Route::middleware(['auth','role:printing_department'])->group(function() {
        Route::get('/applications/{id}', [App\Http\Controllers\PrintingDepartmentController::class, 'viewApplication'])
            ->name('applications.view');
    });
    
    // Legacy dashboard routes
    Route::prefix('dashboard/printing')->name('dashboard.')->group(function() {
        Route::get('/', function() { return redirect()->route('printing.dashboard'); });
        Route::get('/management', function() { return redirect()->route('printing.management'); });
        Route::get('/shipping', function() { return redirect()->route('printing.shipping'); });
        Route::get('/documents', function() { return redirect()->route('printing.documents'); });
        Route::get('/analytics', function() { return redirect()->route('printing.analytics'); });
        Route::get('/prepare-shipment', function() { return redirect()->route('printing.shipping'); });
    });
    
    // Lockbox Search Routes
    Route::get('/lockbox', [LockboxSearchController::class, 'index'])->name('lockbox.index');
    Route::post('/lockbox/search', [LockboxSearchController::class, 'search'])->name('lockbox.search');
});

// Printing Department Dashboard Routes (proper URL structure)
Route::prefix('dashboard/printing-department')->name('dashboard.printing-department.')->middleware(['auth', 'role:printing_department'])->group(function() {
    Route::get('/', [App\Http\Controllers\PrintingDepartmentController::class, 'index'])->name('index');
    Route::get('/queue', [App\Http\Controllers\PrintingDepartmentController::class, 'printQueue'])->name('queue');
    Route::get('/management', [App\Http\Controllers\PrintingDepartmentController::class, 'management'])->name('management');
    Route::get('/shipping', [App\Http\Controllers\PrintingDepartmentController::class, 'shipping'])->name('shipping');
    Route::get('/analytics', [App\Http\Controllers\PrintingDepartmentController::class, 'analytics'])->name('analytics');
    Route::get('/documents', [App\Http\Controllers\PrintingDepartmentController::class, 'documents'])->name('documents');
});

// Case Manager Routes
Route::prefix('case-manager')->name('case-manager.')->group(function() {
    Route::get('/', [App\Http\Controllers\CaseManagerController::class, 'index'])->name('index');
    Route::get('/dashboard', [App\Http\Controllers\CaseManagerController::class, 'index'])->name('dashboard');
    Route::get('/case/{id}', [App\Http\Controllers\CaseManagerController::class, 'viewCase'])->name('view-case');
    Route::post('/case/{id}/assign-self', [App\Http\Controllers\CaseManagerController::class, 'assignSelf'])->name('assign-self');
    Route::post('/case/{id}/assign-attorney', [App\Http\Controllers\CaseManagerController::class, 'assignAttorney'])->name('assign-attorney');
    Route::post('/case/{id}/request-documents', [App\Http\Controllers\CaseManagerController::class, 'requestDocuments'])->name('request-documents');
    Route::post('/case/{id}/mark-ready', [App\Http\Controllers\CaseManagerController::class, 'markReady'])->name('mark-ready');
});

Route::get('/case-manager', function () {
    return redirect()->route('case-manager.dashboard');
});

// Redirect old printing department URLs (preserve legacy route names used in layouts)
Route::get('/dashboard/printing', function () {
    return redirect()->route('dashboard.printing-department.index');
})->name('dashboard.printing.index');

Route::get('/printing', function () {
    return redirect('/dashboard/printing-department');
});

// Temporary test route to check Print Manager login
Route::get('/test-print-manager', function () {
    $user = \App\Models\User::where('name', 'Print Manager')->first();
    if ($user) {
        \Auth::login($user);
        
        // Debug info
        $debug = [
            'user_logged_in' => \Auth::check(),
            'user_id' => \Auth::id(),
            'user_name' => \Auth::user() ? \Auth::user()->name : 'No user',
            'user_roles' => \Auth::user() ? \Auth::user()->getRoleNames()->toArray() : [],
            'has_printing_department_role' => \Auth::user() ? \Auth::user()->hasRole('printing_department') : false,
            'redirect_url' => '/dashboard/printing-department'
        ];
        
        return response()->json($debug);
    }
    return 'Print Manager not found';
});

// Debug route to check current authentication status
Route::get('/debug-auth', function () {
    return response()->json([
        'authenticated' => \Auth::check(),
        'user_id' => \Auth::id(),
        'user_name' => \Auth::user() ? \Auth::user()->name : null,
        'user_roles' => \Auth::user() ? \Auth::user()->getRoleNames() : [],
        'session_id' => session()->getId(),
    ]);
});

// Attorney Dashboard Routes
Route::prefix('attorney')->name('attorney.')->group(function() {
    Route::get('/', [App\Http\Controllers\AttorneyController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\AttorneyController::class, 'index'])->name('dashboard-alt');
    Route::get('/review-case/{id}', [App\Http\Controllers\AttorneyController::class, 'reviewCase'])->name('review-case');
    Route::post('/accept-case/{id}', [App\Http\Controllers\AttorneyController::class, 'acceptCase'])->name('accept-case');
    Route::post('/provide-feedback/{id}', [App\Http\Controllers\AttorneyController::class, 'provideFeedback'])->name('provide-feedback');
    Route::post('/approve/{id}', [App\Http\Controllers\AttorneyController::class, 'approveApplication'])->name('approve');
    Route::post('/reject/{id}', [App\Http\Controllers\AttorneyController::class, 'rejectApplication'])->name('reject');
    Route::post('/request-info/{id}', [App\Http\Controllers\AttorneyController::class, 'requestMoreInfo'])->name('request-info');
    Route::get('/history', [App\Http\Controllers\AttorneyController::class, 'history'])->name('history');
    Route::get('/responses', [App\Http\Controllers\AttorneyController::class, 'responses'])->name('responses');
    
    // Legacy routes for backward compatibility
    Route::get('/cases', function() {
        return redirect()->route('attorney.dashboard');
    });
    Route::get('/legal-advice', function() {
        return redirect()->route('attorney.dashboard');
    });
    Route::get('/documents', function() {
        return redirect()->route('attorney.dashboard');
    });
    Route::get('/approvals', function() {
        return redirect()->route('attorney.dashboard');
    });
    Route::get('/profile', function() {
        return redirect()->route('attorney.dashboard');
    });
    
    // Test route for debugging
    Route::get('/test', function() {
        return view('dashboard.attorney.test');
    })->name('test');
});
