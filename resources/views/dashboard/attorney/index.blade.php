@extends('layouts.dashboard')

@section('title', 'Attorney Dashboard')
@section('page-title', 'Attorney Dashboard')
@section('sidebar')
    @parent
@endsection
@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
    
    :root {
        --primary: #5046e5;
        --primary-light: #6366f1;
        --primary-dark: #4338ca;
        --primary-gradient: linear-gradient(135deg, #5046e5 0%, #3730a3 100%);
        --primary-gradient-soft: linear-gradient(135deg, rgba(80, 70, 229, 0.9) 0%, rgba(55, 48, 163, 0.95) 100%);
        --primary-bg-subtle: rgba(80, 70, 229, 0.08);
        --secondary: #0f172a;
        --secondary-gradient: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        --success: #10b981;
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --success-bg-subtle: rgba(16, 185, 129, 0.08);
        --danger: #ef4444;
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --danger-bg-subtle: rgba(239, 68, 68, 0.08);
        --warning: #f59e0b;
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --warning-bg-subtle: rgba(245, 158, 11, 0.08);
        --info: #3b82f6;
        --info-gradient: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        --info-bg-subtle: rgba(59, 130, 246, 0.08);
        --light-bg: #f8fafc;
        --light-border: #e2e8f0;
        --dark: #0f172a;
        --card-border-radius: 16px;
        --btn-border-radius: 10px;
        --transition-speed: 0.2s;
        --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
    }
    
    body {
        background: #f8fafc;
        font-family: var(--font-sans);
        min-height: 100vh;
        color: #334155;
        line-height: 1.6;
        letter-spacing: -0.01em;
        overflow-x: hidden;
    }
    
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
        max-width: 1920px;
        margin: 0 auto;
    }
    
    .attorney-hero {
        background-color: #fff;
        border-radius: var(--card-border-radius);
        padding: 1.75rem 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 2rem;
        z-index: 1;
        border: 1px solid var(--light-border);
    }
    
    @keyframes gradientBackground {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .attorney-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: var(--primary-gradient-soft);
        width: 8px;
        border-top-left-radius: var(--card-border-radius);
        border-bottom-left-radius: var(--card-border-radius);
    }
    
    .attorney-hero .icon {
        font-size: 1.5rem;
        color: white;
        position: relative;
        z-index: 2;
        background: var(--primary-gradient);
        border-radius: 12px;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(80, 70, 229, 0.2);
        transition: transform 0.3s ease;
    }
    
    .attorney-hero:hover .icon {
        transform: translateY(-5px);
    }
    
    .attorney-hero .welcome {
        font-size: clamp(1.5rem, 5vw, 2rem);
        font-weight: 800;
        margin-bottom: 0.5rem;
        letter-spacing: -0.025em;
        position: relative;
        z-index: 2;
        color: var(--secondary);
    }
    
    .attorney-hero .subtitle {
        font-size: clamp(0.875rem, 2vw, 1rem);
        font-weight: 400;
        position: relative;
        z-index: 2;
        max-width: 650px;
        line-height: 1.6;
        color: #64748b;
    }
    
    @media (max-width: 576px) {
        .attorney-hero {
            flex-direction: column;
            align-items: flex-start;
            text-align: center;
            gap: 1.25rem;
            padding: 1.5rem;
        }
        
        .attorney-hero .icon {
            margin: 0 auto;
        }
        
        .attorney-hero div {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    }
    
    /* Card Styles */
    .card-glass {
        background: white;
        border-radius: var(--card-border-radius);
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        height: 100%;
        border: 1px solid var(--light-border);
        width: 100%;
    }
    
    .card-glass:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid var(--light-border);
        padding: 1rem 1.25rem;
    }
    
    .card-header h5 {
        font-size: 1.05rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        color: var(--dark);
        margin-bottom: 0;
        letter-spacing: -0.025em;
    }
    
    .card-header h5 i {
        margin-right: 0.75rem;
        color: var(--primary);
        font-size: 1.1em;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: var(--primary-bg-subtle);
        border-radius: 8px;
    }
    
    .table {
        margin-bottom: 0;
        width: 100%;
    }
    
    .table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.75rem;
        border: none;
        padding: 0.75rem 1rem;
        white-space: nowrap;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    
    .table tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #334155;
    }
    
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(248, 250, 252, 0.7);
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    @media (max-width: 992px) {
        .table thead th, .table tbody td {
            padding: 0.75rem 0.85rem;
        }
    }
    
    .badge {
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        font-size: 0.7rem;
        border-radius: 6px;
        letter-spacing: 0.2px;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }
    
    .badge:hover {
        transform: translateY(-1px);
    }
    
    .bg-primary {
        background: var(--primary) !important;
        color: white !important;
        border: none;
    }
    
    .bg-info {
        background: var(--info) !important;
        color: white !important;
        border: none;
    }
    
    .bg-success {
        background: var(--success) !important;
        color: white !important;
        border: none;
    }
    
    .bg-warning {
        background: var(--warning) !important;
        color: white !important;
        border: none;
    }
    
    .bg-danger {
        background: var(--danger) !important;
        color: white !important;
        border: none;
    }
    
    .btn-primary {
        background: var(--primary);
        border: none;
        color: white;
        font-weight: 600;
        border-radius: var(--btn-border-radius);
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(80, 70, 229, 0.15);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(80, 70, 229, 0.2);
        background: var(--primary-dark);
    }
    
    .btn-outline-primary {
        border: 1.5px solid var(--primary);
        color: var(--primary);
        background: var(--primary-bg-subtle);
        padding: 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.25s ease;
        border-radius: var(--btn-border-radius);
    }
    
    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 8px rgba(80, 70, 229, 0.15);
        transform: translateY(-2px);
    }
    
    .btn-outline-success {
        border: 1.5px solid var(--success);
        color: var(--success);
        background: var(--success-bg-subtle);
        padding: 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.25s ease;
        border-radius: var(--btn-border-radius);
    }
    
    .btn-outline-success:hover {
        background: var(--success);
        color: white;
        border-color: var(--success);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.15);
        transform: translateY(-2px);
    }
    
    .btn-outline-info {
        border: 1.5px solid var(--info);
        color: var(--info);
        background: var(--info-bg-subtle);
        padding: 0.45rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.25s ease;
        border-radius: var(--btn-border-radius);
    }
    
    .btn-outline-info:hover {
        background: var(--info);
        color: white;
        border-color: var(--info);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    
    .alert-info {
        background: var(--info-bg-subtle);
        border-left: 4px solid var(--info);
        color: #1e40af;
        border-radius: 12px;
        padding: 1.125rem 1.5rem;
        box-shadow: var(--shadow-sm);
    }
    
    .alert-link {
        font-weight: 600;
        text-decoration: none;
        color: var(--info);
        transition: all 0.2s ease;
    }
    
    .alert-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }
    
    /* Quick Actions */
    .quick-actions {
        margin: -1px;
    }
    
    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #4b5563;
        padding: 1rem 0.5rem;
        border-radius: 10px;
        background: white;
        transition: all 0.25s ease;
        height: 100%;
        border: 1px solid var(--light-border);
    }
    
    .quick-action-btn:hover {
        background: var(--primary-bg-subtle);
        color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        border-color: rgba(80, 70, 229, 0.2);
    }
    
    .quick-action-btn i {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--primary);
        background: var(--primary-bg-subtle);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.25s ease;
    }
    
    .quick-action-btn:hover i {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 8px rgba(80, 70, 229, 0.2);
    }
    
    .btn {
        white-space: nowrap;
    }
    
    @media (max-width: 576px) {
        .quick-actions .row {
            gap: 0.75rem;
        }
        
        .quick-action-btn {
            padding: 0.75rem 0.35rem;
        }
    }
    
    .quick-action-btn span {
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    
    /* Timeline styles */
    .timeline {
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 18px;
        width: 1px;
        background: #e2e8f0;
        z-index: 1;
    }
    
    .timeline-item {
        position: relative;
        padding-left: 8px;
        z-index: 2;
    }
    
    .timeline-item .icon-container {
        position: relative;
        z-index: 3;
    }
    
    .bg-light-subtle {
        background-color: #f8fafc;
    }
    
    @media (max-width: 991px) {
        .row.mb-4 {
            margin-bottom: 1rem !important;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header h5 {
            margin-bottom: 0.75rem;
        }
        
        .card-header .d-flex.gap-2 {
            width: 100%;
        }
        
        .card-header .position-relative {
            flex-grow: 1;
        }
    }
    
    /* Stats Card Styles */
    .stats-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 1.25rem;
        box-shadow: var(--shadow);
        transition: all 0.25s ease;
        border: 1px solid var(--light-border);
        height: 100%;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    .stats-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.85rem;
        font-size: 1.2rem;
        color: white;
        box-shadow: 0 4px 8px rgba(80, 70, 229, 0.15);
    }
    
    .stats-value {
        font-size: clamp(1.5rem, 4vw, 1.65rem);
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
        letter-spacing: -0.025em;
    }
    
    .stats-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-bottom: 0;
    }
    
    @media (max-width: 1199px) {
        .stats-card {
            margin-bottom: 1rem;
        }
    }
    
    @media (max-width: 767px) {
        .row [class*="col-"] {
            margin-bottom: 1rem;
        }
        
        .row [class*="col-"]:last-child {
            margin-bottom: 0;
        }
    }
    
    .progress-thin {
        height: 5px;
        border-radius: 100px;
        overflow: hidden;
        font-size: 0;
        background: #f1f5f9;
    }
    
    .progress-thin .progress-bar {
        background: var(--primary);
        height: 100%;
        border-radius: 100px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid px-lg-3 px-xl-4">
    <!-- Attorney Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="attorney-hero">
                <div class="icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <div>
                    <div class="welcome">Welcome, <span class="fw-bold">Attorney</span></div>
                    <div class="subtitle">Review assigned cases, provide legal advice, and approve applications efficiently through your personalized dashboard.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: var(--primary);">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stats-value">12</div>
                <div class="stats-label">Active Cases</div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Case Completion</span>
                        <span class="small fw-semibold">68%</span>
                    </div>
                    <div class="progress-thin">
                        <div class="progress-bar" role="progressbar" style="width: 68%" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-value">24</div>
                <div class="stats-label">Approved This Month</div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Approval Rate</span>
                        <span class="small fw-semibold">91%</span>
                    </div>
                    <div class="progress-thin">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 91%" aria-valuenow="91" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: var(--warning);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stats-value">7</div>
                <div class="stats-label">Pending Reviews</div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Response Time</span>
                        <span class="small fw-semibold">1.2 days</span>
                    </div>
                    <div class="progress-thin">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-icon" style="background: var(--info);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stats-value">38</div>
                <div class="stats-label">Feedbacks Provided</div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Resolution Rate</span>
                        <span class="small fw-semibold">86%</span>
                    </div>
                    <div class="progress-thin">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 86%" aria-valuenow="86" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <div class="me-3">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <strong>New Responses Available!</strong> 
                    You have <strong>3 new responses</strong> from applicants based on your feedback.
                    <a href="{{ route('attorney.responses') }}" class="alert-link ms-2">Review Now</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card-glass">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0" style="font-size:0.8rem;"><i class="fas fa-clipboard-check"></i> Cases for Review</h6>
                    <a href="{{ route('attorney.cases') }}" class="btn btn-sm btn-primary ms-auto">
                        <i class="fas fa-list-ul me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="font-size:0.92rem;">
                            <thead>
                                <tr>
                                    <th>Case</th>
                                    <th>Applicant</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                // Mock data for pending reviews if variable not set
                                if (!isset($pendingReviews) || empty($pendingReviews)) {
                                    $pendingReviews = [
                                        (object)[
                                            'id' => 'C-1001',
                                            'applicant_name' => 'John Doe',
                                            'visa_type' => 'H-1B',
                                            'submitted_at' => '2025-08-15',
                                            'status' => 'Pending Review'
                                        ],
                                        (object)[
                                            'id' => 'C-1002',
                                            'applicant_name' => 'Jane Smith',
                                            'visa_type' => 'F-1 Student',
                                            'submitted_at' => '2025-08-20',
                                            'status' => 'Advice Needed'
                                        ],
                                        (object)[
                                            'id' => 'C-1003',
                                            'applicant_name' => 'Robert Johnson',
                                            'visa_type' => 'L-1 Transfer',
                                            'submitted_at' => '2025-08-22',
                                            'status' => 'Ready for Approval'
                                        ]
                                    ];
                                }
                            @endphp
                            @foreach(array_slice($pendingReviews, 0, 2) as $case)
                                <tr>
                                    <td><span class="fw-semibold">{{ $case->id }}</span></td>
                                    <td>{{ $case->applicant_name }}</td>
                                    <td>
                                        @php
                                            $status = strtolower(str_replace(' ', '_', $case->status));
                                        @endphp
                                        @if($status == 'pending_review')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($status == 'advice_needed')
                                            <span class="badge bg-info">Advice</span>
                                        @elseif($status == 'ready_for_approval')
                                            <span class="badge bg-success">Ready</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $case->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('attorney.review-case', $case->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activities & Quick Actions -->
        <div class="col-lg-4">
            <div class="card-glass h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i>Quick Actions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="quick-actions p-3 bg-light-subtle border-bottom">
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('attorney.cases') }}" class="quick-action-btn">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>All Cases</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('attorney.documents') }}" class="quick-action-btn">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Documents</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('attorney.legal-advice') }}" class="quick-action-btn">
                                    <i class="fas fa-gavel"></i>
                                    <span>Legal Advice</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('attorney.approvals') }}" class="quick-action-btn">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Approvals</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 border-bottom">
                        <h6 class="fw-bold mb-3">Recent Activities</h6>
                        <div class="timeline">
                            <div class="timeline-item pb-3 mb-3 border-bottom">
                                <div class="d-flex">
                                    <div class="icon-container" style="width: 38px; height: 38px; background: var(--success-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <i class="fas fa-check" style="font-size: 14px; color: var(--success);"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1 fw-medium">Approved H-1B Application</p>
                                        <p class="mb-0 small text-muted">2 hours ago</p>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item pb-3 mb-3 border-bottom">
                                <div class="d-flex">
                                    <div class="icon-container" style="width: 38px; height: 38px; background: var(--info-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <i class="fas fa-comment" style="font-size: 14px; color: var(--info);"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1 fw-medium">Provided legal advice</p>
                                        <p class="mb-0 small text-muted">Yesterday at 4:23 PM</p>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="icon-container" style="width: 38px; height: 38px; background: var(--warning-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                        <i class="fas fa-file-import" style="font-size: 14px; color: var(--warning);"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1 fw-medium">Received new case assignment</p>
                                        <p class="mb-0 small text-muted">2 days ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 text-center">
                        <a href="{{ route('attorney.history') }}" class="btn btn-primary">
                            <i class="fas fa-history me-1"></i> View All Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Deadlines -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card-glass">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i>Upcoming Deadlines</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Case</th>
                                    <th>Deadline</th>
                                    <th>Priority</th>
                                    <th>Time Remaining</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 32px; height: 32px; background: var(--primary-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                <i class="fas fa-file-contract" style="font-size: 14px; color: var(--primary);"></i>
                                            </div>
                                            <div>
                                                <p class="fw-medium mb-0">H-1B Documentation</p>
                                                <p class="text-muted small mb-0">C-1002</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>September 10, 2025</td>
                                    <td><span class="badge bg-danger">Urgent</span></td>
                                    <td>4 days</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">Review</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 32px; height: 32px; background: var(--primary-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                <i class="fas fa-file-contract" style="font-size: 14px; color: var(--primary);"></i>
                                            </div>
                                            <div>
                                                <p class="fw-medium mb-0">L-1 Transfer Approval</p>
                                                <p class="text-muted small mb-0">C-1003</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>September 15, 2025</td>
                                    <td><span class="badge bg-warning">High</span></td>
                                    <td>9 days</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">Review</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 32px; height: 32px; background: var(--primary-bg-subtle); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                <i class="fas fa-file-contract" style="font-size: 14px; color: var(--primary);"></i>
                                            </div>
                                            <div>
                                                <p class="fw-medium mb-0">F-1 Student Feedback</p>
                                                <p class="text-muted small mb-0">C-1005</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>September 18, 2025</td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>12 days</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">Review</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
