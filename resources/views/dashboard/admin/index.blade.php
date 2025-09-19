@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('sidebar')
    @parent
@endsection
@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    :root {
        --primary: #4f46e5;
        --primary-light: #6366f1;
        --primary-dark: #4338ca;
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --secondary: #2a2aa0;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #3b82f6;
        --light-bg: #f9fafb;
        --dark: #111827;
        --card-border-radius: 16px;
        --transition-speed: 0.3s;
    }
    
    body {
        background: #f3f4f6;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
        color: #374151;
    }
    

        opacity: 0.9;
    }
    
    /* Dashboard Cards */
    .card {
        border: none;
        border-radius: var(--card-border-radius);
        background: white;
        position: relative;
        overflow: hidden;
        height: 100%;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
    }
    
    .card .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    
    .card:hover .icon-wrapper {
        transform: scale(1.05);
    }
    
    .card .icon {
        font-size: 1.5rem;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .card:hover .icon {
        transform: scale(1.1);
    }
    
    .card .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 3rem;
        opacity: 0.03;
        z-index: 0;
        transform: rotate(-10deg);
        transition: all 0.5s ease;
        pointer-events: none;
        user-select: none;
    }
    
    .card:hover .icon-bg {
        transform: rotate(-15deg) scale(1.05);
        opacity: 0.04;
    }

    @media (max-width: 992px) {
        .card .icon-bg { display: none; }
    }
    
    .card.users-card .icon-wrapper {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%);
    }
    
    .card.users-card .icon {
        color: var(--primary);
    }
    
    .card.applicants-card .icon-wrapper {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.2) 100%);
    }
    
    .card.applicants-card .icon {
        color: var(--success);
    }
    
    .card.managers-card .icon-wrapper {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.2) 100%);
    }
    
    .card.managers-card .icon {
        color: var(--info);
    }
    
    .card.attorneys-card .icon-wrapper {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.2) 100%);
    }
    
    .card.attorneys-card .icon {
        color: var(--warning);
    }
    
    .card .stat {
        font-size: 2.25rem;
        font-weight: 700;
        line-height: 1;
        color: var(--dark);
        position: relative;
        letter-spacing: -1px;
    }
    
    .card .label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #6b7280;
        margin: 0;
    }
    
    .card .footer-link {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }
    
    .card .footer-link:hover {
        color: var(--primary-dark);
    }
    
    .card .footer-link i {
        margin-left: 0.5rem;
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .card .footer-link:hover i {
        transform: translateX(5px);
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
        border-radius: 12px;
        background: white;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .quick-action-btn:hover {
        background: rgba(79, 70, 229, 0.05);
        color: var(--primary);
        transform: translateY(-3px);
    }
    
    .quick-action-btn i {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        color: var(--primary);
    }
    
    .quick-action-btn span {
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    /* Tables and Content Cards */
    .card-glass {
        background: white;
        border-radius: var(--card-border-radius);
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        height: 100%;
    }
    
    .card-glass:hover {
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        padding: 1.25rem 1.5rem;
    }
    
    .card-header h5 {
        font-size: 1.15rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        color: var(--dark);
        margin-bottom: 0;
    }
    
    .card-header h5 i {
        margin-right: 0.75rem;
        color: var(--primary);
        font-size: 1.1em;
    }
    
    .card-header .btn-primary {
        background: var(--primary-gradient);
        border: none;
        font-weight: 500;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    
    .card-header .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(79, 70, 229, 0.3);
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        background: #f9fafb;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.75rem;
        border: none;
        padding: 1rem 1.25rem;
        white-space: nowrap;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    
    .table tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-top: 1px solid #f3f4f6;
        font-size: 0.875rem;
        color: #1f2937;
    }
    
    .table-hover tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(249, 250, 251, 0.7);
    }
    
    .badge {
        padding: 0.4rem 0.75rem;
        font-weight: 600;
        font-size: 0.7rem;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    
    .bg-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.2) 100%) !important;
        color: var(--info) !important;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    .bg-primary {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%) !important;
        color: var(--primary) !important;
        border: 1px solid rgba(79, 70, 229, 0.3);
    }
    
    .bg-secondary {
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.1) 0%, rgba(107, 114, 128, 0.2) 100%) !important;
        color: #6b7280 !important;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }
    
    .bg-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.2) 100%) !important;
        color: var(--success) !important;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    
    .bg-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.2) 100%) !important;
        color: var(--warning) !important;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    
    .bg-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.2) 100%) !important;
        color: var(--danger) !important;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    
    .bg-primary-subtle {
        background-color: rgba(79, 70, 229, 0.1) !important;
    }
    
    .bg-success-subtle {
        background-color: rgba(16, 185, 129, 0.1) !important;
    }
    
    .bg-info-subtle {
        background-color: rgba(59, 130, 246, 0.1) !important;
    }
    
    .bg-warning-subtle {
        background-color: rgba(245, 158, 11, 0.1) !important;
    }
    
    .bg-danger-subtle {
        background-color: rgba(239, 68, 68, 0.1) !important;
    }
    
    .text-primary {
        color: var(--primary) !important;
    }
    
    .text-success {
        color: var(--success) !important;
    }
    
    .text-info {
        color: var(--info) !important;
    }
    
    .text-warning {
        color: var(--warning) !important;
    }
    
    .text-danger {
        color: var(--danger) !important;
    }
    
    .btn-outline-primary {
        border: 1.5px solid var(--primary);
        color: var(--primary);
        background: rgba(79, 70, 229, 0.02);
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background: var(--primary-gradient);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        transform: translateY(-2px);
    }
    
    .btn-outline-secondary {
        border: 1.5px solid #9ca3af;
        color: #6b7280;
        background: rgba(156, 163, 175, 0.02);
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
    }
    
    /* User List Styling */
    .list-group-item {
        border: none;
        border-bottom: 1px solid #f3f4f6;
        padding: 1.25rem;
        transition: all 0.25s ease;
        background: transparent;
    }
    
    .list-group-item:hover {
        background-color: #f9fafb;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    /* Chart Styling */
    .chart-container {
        margin: 0 auto;
    }
    
    .chart-filter .form-select {
        border-color: #e5e7eb;
        color: #4b5563;
        font-size: 0.85rem;
        box-shadow: none;
    }
    
    .chart-filter .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.1);
    }
    
    .stat-summary {
        border: 1px solid #e5e7eb;
    }
    
    .stat-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
    
    /* Task Styling */
    .task-item {
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }
    
    .task-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: #d1d5db;
    }
    
    .task-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (max-width: 767.98px) {
        .card {
            margin-bottom: 1rem;
        }
        
        .card-header {
            flex-direction: column;
            gap: 1rem;
        }
        
        .quick-action-btn {
            padding: 0.75rem 0.5rem;
        }
        
        .stat-summary {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection
@section('content')

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card users-card h-100 position-relative">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-auto">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-users icon"></i>
                    </div>
                    <div>
                        <h2 class="stat mb-0">{{ $totalUsers ?? '0' }}</h2>
                        <p class="label">Total Users</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Growth</small>
                        <small class="fw-medium text-success">+12%</small>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px; background: rgba(79, 70, 229, 0.1);">
                        <div class="progress-bar" role="progressbar" style="width: 85%; background: var(--primary-gradient);" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.users') }}" class="footer-link">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            <span class="icon-bg"><i class="fas fa-users"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card applicants-card h-100 position-relative">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-auto">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-user-check icon"></i>
                    </div>
                    <div>
                        <h2 class="stat mb-0">{{ $totalApplicants ?? '0' }}</h2>
                        <p class="label">Applicants</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Growth</small>
                        <small class="fw-medium text-success">+8%</small>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px; background: rgba(16, 185, 129, 0.1);">
                        <div class="progress-bar" role="progressbar" style="width: 68%; background: linear-gradient(135deg, var(--success) 0%, #059669 100%);" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.applications') }}" class="footer-link">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            <span class="icon-bg"><i class="fas fa-user-check"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card managers-card h-100 position-relative">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-auto">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-user-tie icon"></i>
                    </div>
                    <div>
                        <h2 class="stat mb-0">{{ $totalCaseManagers ?? '0' }}</h2>
                        <p class="label">Case Managers</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Growth</small>
                        <small class="fw-medium text-success">+3%</small>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px; background: rgba(59, 130, 246, 0.1);">
                        <div class="progress-bar" role="progressbar" style="width: 45%; background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.case-managers') }}" class="footer-link">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            <span class="icon-bg"><i class="fas fa-user-tie"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card attorneys-card h-100 position-relative">
            <div class="card-body d-flex flex-column p-4">
                <div class="d-flex align-items-center mb-auto">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-gavel icon"></i>
                    </div>
                    <div>
                        <h2 class="stat mb-0">{{ $totalAttorneys ?? '0' }}</h2>
                        <p class="label">Attorneys</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Growth</small>
                        <small class="fw-medium text-success">+5%</small>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 999px; background: rgba(245, 158, 11, 0.1);">
                        <div class="progress-bar" role="progressbar" style="width: 72%; background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <a href="{{ route('admin.attorneys') }}" class="footer-link">View Details <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            <span class="icon-bg"><i class="fas fa-gavel"></i></span>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="row g-4 mb-4">
    <!-- Recent Applications Table -->
    <div class="col-lg-8">
        <div class="card-glass h-100">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i>Recent Applications</h5>
                <div class="d-flex gap-2">
                    <div class="position-relative">
                        <input type="text" class="form-control form-control-sm" placeholder="Search applications..." style="padding-left: 32px; border-radius: 20px; min-width: 200px;">
                        <i class="fas fa-search position-absolute" style="left: 12px; top: 9px; color: #6b7280; font-size: 0.8rem;"></i>
                    </div>
                    <a href="{{ route('admin.applications') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list-ul me-1"></i> View All
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Applicant</th>
                                <th>Visa Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications ?? [] as $app)
                            <tr>
                                <td><span class="fw-semibold">APP-{{ $app->id ?? '0' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                            <i class="fas fa-user" style="font-size: 14px; color: var(--primary);"></i>
                                        </div>
                                        <div class="fw-medium">{{ isset($app->user) ? ($app->user->name ?? 'Unknown') : 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary">{{ $app->visa_type ?? 'Unknown' }}</span></td>
                                <td>
                                    @php $date = $app->submitted_at ?? $app->created_at ?? null; @endphp
                                    {{ $date ? \Carbon\Carbon::parse($date)->format('M d, Y') : '-' }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'bg-info';
                                        $status = strtolower($app->status ?? 'pending');
                                        
                                        if ($status == 'approved') {
                                            $statusClass = 'bg-success';
                                            $status = 'Approved';
                                        } elseif ($status == 'rejected') {
                                            $statusClass = 'bg-danger';
                                            $status = 'Rejected';
                                        } elseif (in_array($status, ['under_review','reviewing','pending_review','pending_attorney_review'])) {
                                            $statusClass = 'bg-warning';
                                            $status = 'Under Review';
                                        } elseif ($status == 'draft') {
                                            $statusClass = 'bg-secondary';
                                            $status = 'Draft';
                                        } else {
                                            $status = 'Pending';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td>
                                    @if(isset($app->id))
                                    <a href="{{ route('admin.application-detail', $app->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                    @else
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" disabled>
                                        <i class="fas fa-eye me-1"></i> View
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div style="width: 60px; height: 60px; background: rgba(79, 70, 229, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                            <i class="fas fa-clipboard-list" style="font-size: 24px; color: var(--primary);"></i>
                                        </div>
                                        <p class="text-muted mb-0">No applications found</p>
                                        <p class="text-muted small">New applications will appear here</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Applicants & Quick Actions -->
    <div class="col-lg-4">
        <div class="card-glass h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users"></i>Recent Applicants</h5>
                <a href="{{ route('admin.users') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list-ul me-1"></i> View All
                </a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentUsers ?? [] as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-start p-3">
                        <div class="d-flex w-100 align-items-center">
                            <div style="width: 42px; height: 42px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0;">
                                <i class="fas fa-user" style="font-size: 16px; color: var(--primary);"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-semibold text-truncate">{{ $user->name ?? 'Unknown' }}</h6>
                                <small class="text-muted d-block text-truncate">{{ $user->email ?? 'No email provided' }}</small>
                                @if(isset($user->birth_date) && $user->birth_date)
                                    <small class="text-info d-block">Age: {{ \Carbon\Carbon::parse($user->birth_date)->age }} years</small>
                                @endif
                                <div class="mt-1">
                                    <span class="badge bg-primary">Applicant</span>
                                </div>
                            </div>
                        </div>
                        <div class="ms-2">
                            @if(isset($user->id))
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0;">
                                <i class="fas fa-eye"></i>
                            </a>
                            @else
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; padding: 0;" disabled>
                                <i class="fas fa-eye"></i>
                            </button>
                            @endif
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div style="width: 60px; height: 60px; background: rgba(79, 70, 229, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                <i class="fas fa-user-plus" style="font-size: 24px; color: var(--primary);"></i>
                            </div>
                            <p class="text-muted mb-0">No recent applicants found</p>
                            <p class="text-muted small">New applicants will appear here</p>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Applications Activity Chart -->
    <div class="col-lg-8">
        <div class="card-glass">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i>Application Activity</h5>
                <div class="chart-filter">
                    <select class="form-select form-select-sm rounded-pill">
                        <option selected>Last 6 Months</option>
                        <option>Last Year</option>
                        <option>All Time</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4 g-3">
                    <div class="col-md-4">
                        <div class="stat-summary d-flex align-items-start p-3 rounded-3 bg-light-subtle">
                            <div class="stat-icon me-3 bg-primary-subtle p-2 rounded-circle">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div>
                                <h3 class="fs-4 fw-bold mb-0">{{ $totalApplicants ?? '0' }}</h3>
                                <span class="text-muted small">Total Applications</span>
                                <div class="mt-1">
                                    <span class="badge text-success bg-success-subtle">+8.2% <i class="fas fa-arrow-up ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-summary d-flex align-items-start p-3 rounded-3 bg-light-subtle">
                            <div class="stat-icon me-3 bg-success-subtle p-2 rounded-circle">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div>
                                <h3 class="fs-4 fw-bold mb-0">{{ round(($totalApplicants ?? 0) * 0.65) }}</h3>
                                <span class="text-muted small">Approved</span>
                                <div class="mt-1">
                                    <span class="badge text-success bg-success-subtle">+12.5% <i class="fas fa-arrow-up ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-summary d-flex align-items-start p-3 rounded-3 bg-light-subtle">
                            <div class="stat-icon me-3 bg-warning-subtle p-2 rounded-circle">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                            <div>
                                <h3 class="fs-4 fw-bold mb-0">{{ round(($totalApplicants ?? 0) * 0.35) }}</h3>
                                <span class="text-muted small">Pending</span>
                                <div class="mt-1">
                                    <span class="badge text-danger bg-danger-subtle">-4.8% <i class="fas fa-arrow-down ms-1"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 280px;">
                    <canvas id="applicationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Visa Types & Tasks -->
    <div class="col-lg-4">
        <div class="row g-4 h-100">
            <div class="col-12">
                <div class="card-glass">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i>Visa Type Distribution</h5>
                    </div>
                    <div class="card-body pb-3">
                        <div class="chart-container mb-3" style="position: relative; height: 180px;">
                            <canvas id="visaTypesChart"></canvas>
                        </div>
                        <div class="visa-legend">
                            <div class="row g-0">
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2 ps-2">
                                        <span class="legend-dot" style="background-color: #4f46e5;"></span>
                                        <span class="ms-2 small">Tourist (35%)</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2 ps-2">
                                        <span class="legend-dot" style="background-color: #10b981;"></span>
                                        <span class="ms-2 small">Business (25%)</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2 ps-2">
                                        <span class="legend-dot" style="background-color: #3b82f6;"></span>
                                        <span class="ms-2 small">Student (20%)</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2 ps-2">
                                        <span class="legend-dot" style="background-color: #f59e0b;"></span>
                                        <span class="ms-2 small">Work (15%)</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2 ps-2">
                                        <span class="legend-dot" style="background-color: #ef4444;"></span>
                                        <span class="ms-2 small">Other (5%)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card-glass d-flex flex-column h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tasks"></i>Quick Tasks</h5>
                    </div>
                    <div class="card-body d-flex flex-column flex-grow-1">
                        <div class="task-item d-flex align-items-center p-2 rounded-3 mb-2 bg-light-subtle">
                            <span class="task-icon me-3 bg-info-subtle p-2 rounded-circle">
                                <i class="fas fa-user-plus text-info"></i>
                            </span>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-medium">Review new applications</h6>
                                <span class="text-muted small">8 pending applications</span>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Review
                            </a>
                        </div>
                        <div class="task-item d-flex align-items-center p-2 rounded-3 mb-2 bg-light-subtle">
                            <span class="task-icon me-3 bg-warning-subtle p-2 rounded-circle">
                                <i class="fas fa-file-signature text-warning"></i>
                            </span>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-medium">Approve documents</h6>
                                <span class="text-muted small">12 documents awaiting review</span>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Review
                            </a>
                        </div>
                        <div class="mt-auto text-center">
                            <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-primary rounded-pill w-75">
                                <i class="fas fa-chart-bar me-1"></i> View Detailed Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fix stat display issue if any
        const statElements = document.querySelectorAll('.stat');
        statElements.forEach(function(el) {
            if (el.textContent === '' || el.textContent === 'undefined') {
                el.textContent = '0';
            }
        });
    
        // Check if chart containers exist before initializing charts
        const applicationsChartElement = document.getElementById('applicationsChart');
        const visaTypesChartElement = document.getElementById('visaTypesChart');
        
        // Applications Chart
        if (applicationsChartElement) {
            try {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const currentMonth = new Date().getMonth();
                const lastSixMonths = months.slice(currentMonth - 5, currentMonth + 1);
                if (lastSixMonths.length < 6) {
                    const monthsNeeded = 6 - lastSixMonths.length;
                    lastSixMonths.unshift(...months.slice(12 - monthsNeeded));
                }
                
                // Generate random but realistic data with an upward trend
                const baseData = [45, 52, 58, 64, 73, 85];
                const randomizedData = baseData.map(val => val + Math.floor(Math.random() * 20) - 5);
                
                const applicationsCtx = applicationsChartElement.getContext('2d');
                const gradientFill = applicationsCtx.createLinearGradient(0, 0, 0, 400);
                gradientFill.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                gradientFill.addColorStop(1, 'rgba(79, 70, 229, 0.02)');
                
                const applicationsChart = new Chart(applicationsCtx, {
                    type: 'line',
                    data: {
                        labels: lastSixMonths,
                        datasets: [{
                            label: 'Applications',
                            data: randomizedData,
                            fill: true,
                            backgroundColor: gradientFill,
                            borderColor: '#4f46e5',
                            tension: 0.4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#4f46e5',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBorderWidth: 3,
                            pointHoverBorderWidth: 3,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#4f46e5'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#1f2937',
                                bodyColor: '#4f46e5',
                                bodyFont: {
                                    weight: 'bold',
                                    size: 14
                                },
                                padding: 12,
                                borderColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 1,
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)',
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        return `Applications: ${context.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.03)',
                                    lineWidth: 1
                                },
                                ticks: {
                                    padding: 10,
                                    color: '#6b7280',
                                    font: {
                                        size: 11
                                    }
                                },
                                border: {
                                    dash: [4, 4]
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    padding: 10,
                                    color: '#6b7280',
                                    font: {
                                        size: 11
                                    }
                                }
                            }
                        },
                        elements: {
                            line: {
                                borderWidth: 3
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing applications chart:', error);
                // Add a fallback message if chart fails to load
                applicationsChartElement.parentNode.innerHTML = '<div class="text-center py-4 text-muted">Chart data could not be displayed</div>';
            }
        }
        
        // Visa Types Chart
        if (visaTypesChartElement) {
            try {
                const visaTypesCtx = visaTypesChartElement.getContext('2d');
                const visaTypesChart = new Chart(visaTypesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Tourist', 'Business', 'Student', 'Work', 'Other'],
                        datasets: [{
                            data: [35, 25, 20, 15, 5],
                            backgroundColor: [
                                '#4f46e5', // Primary
                                '#10b981', // Success
                                '#3b82f6', // Info
                                '#f59e0b', // Warning
                                '#ef4444'  // Danger
                            ],
                            borderWidth: 5,
                            borderColor: '#ffffff',
                            hoverOffset: 15,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    color: '#4b5563',
                                    font: { 
                                        size: 12,
                                        family: "'Inter', sans-serif",
                                        weight: 500
                                    },
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#1f2937',
                                bodyColor: '#4b5563',
                                bodyFont: {
                                    weight: 'bold',
                                    size: 14
                                },
                                padding: 12,
                                borderColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 1,
                                boxShadow: '0 4px 20px rgba(0, 0, 0, 0.1)',
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.raw}%`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                            duration: 2000,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing visa types chart:', error);
                // Add a fallback message if chart fails to load
                visaTypesChartElement.parentNode.innerHTML = '<div class="text-center py-4 text-muted">Chart data could not be displayed</div>';
            }
        }
        
        // Add subtle animations for stat cards
        const cards = document.querySelectorAll('.dashboard-cards .card');
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `all 0.5s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    });
</script>
@endsection
