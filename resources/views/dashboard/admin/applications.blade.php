@extends('layouts.dashboard')
@section('title', 'Applications Management')
@section('page-title', 'Applications Management')

@section('styles')
<style>
    /* Defensive fixes to avoid oversized background icons leaking in */
    .icon-bg { display: none !important; }
    i.fas, i.far, i.fab { line-height: 1; }
    .card-glass {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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
        color: #111827;
        margin-bottom: 0;
    }
    
    .card-header h5 i {
        margin-right: 0.75rem;
        color: #4f46e5;
        font-size: 1.1em;
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
        color: #3b82f6 !important;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    .bg-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.2) 100%) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    
    .bg-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.2) 100%) !important;
        color: #f59e0b !important;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    
    .bg-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.2) 100%) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    
    .btn-outline-primary {
        border: 1.5px solid #4f46e5;
        color: #4f46e5;
        background: rgba(79, 70, 229, 0.02);
        padding: 0.375rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border-radius: 20px;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        transform: translateY(-2px);
    }
    
    .pagination {
        --bs-pagination-active-bg: #4f46e5;
        --bs-pagination-active-border-color: #4f46e5;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box input {
        padding-left: 32px;
        border-radius: 20px;
        border-color: #e5e7eb;
    }
    
    .search-box i {
        position: absolute;
        left: 12px;
        top: 10px;
        color: #6b7280;
        font-size: 0.8rem;
    }
    
    .search-box input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }
    
    .filter-dropdown .dropdown-menu {
        border: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 1rem;
    }
    
    .filter-dropdown .dropdown-toggle {
        border-radius: 20px;
    }

    /* Keep Laravel pagination; hide any plugin-generated duplicates (e.g., DataTables) */
    .dataTables_info,
    .dataTables_paginate { display: none !important; }
    /* Also hide stray pagination blocks not using Bootstrap .pagination */
    .table + div:not(:has(.pagination)) { display: none; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 text-center mb-3">
            <img src="/images/logo.png" alt="Horizon Pathways Logo" style="height:56px;max-width:220px;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(79,70,229,0.10));">
        </div>
        <div class="col-12">
            <div class="card-glass">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5><i class="fas fa-clipboard-list"></i>All Applications</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="search-box">
                            <input type="text" class="form-control form-control-sm" placeholder="Search applications...">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="filter-dropdown dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Filter by Status</h6>
                                <div class="px-3 py-2">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="filter-pending" checked>
                                        <label class="form-check-label" for="filter-pending">Pending</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="filter-reviewing" checked>
                                        <label class="form-check-label" for="filter-reviewing">Under Review</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="filter-approved" checked>
                                        <label class="form-check-label" for="filter-approved">Approved</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filter-rejected" checked>
                                        <label class="form-check-label" for="filter-rejected">Rejected</label>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Sort By</h6>
                                <div class="px-3 py-2">
                                    <select class="form-select form-select-sm">
                                        <option selected>Newest First</option>
                                        <option>Oldest First</option>
                                        <option>A-Z</option>
                                        <option>Z-A</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between p-3">
                                    <button class="btn btn-sm btn-outline-secondary">Reset</button>
                                    <button class="btn btn-sm btn-primary">Apply</button>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> New Application
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
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                <tr>
                                    <td><span class="fw-semibold">APP-{{ $app->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div style="width: 36px; height: 36px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                <i class="fas fa-user" style="font-size: 14px; color: #4f46e5;"></i>
                                            </div>
                                            <div class="fw-medium">{{ $app->user->name ?? 'Unknown' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $app->visa_type ?? '-' }}</span>
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
                                            } elseif ($status == 'reviewing') {
                                                $statusClass = 'bg-warning';
                                                $status = 'Under Review';
                                            } else {
                                                $status = 'Pending';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                                    </td>
                                    <td>{{ $app->created_at ? $app->created_at->format('M d, Y') : '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.application-detail', $app->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $applications->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
