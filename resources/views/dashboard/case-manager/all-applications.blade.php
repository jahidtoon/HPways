@extends('layouts.dashboard')

@section('title', 'All Applications')
@section('page-title', 'All Applications')

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}
.btn-action {
    transition: all 0.2s ease;
}
.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.filter-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2 text-primary"></i>All Applications
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                View and manage all applications in the system
            </p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('dashboard.case-manager.applications') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-check me-1"></i> My Applications
            </a>
            <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
            <button class="btn btn-primary" onclick="location.reload()" title="Refresh Data">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Status Filter</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="under_case_manager_review" {{ request('status') == 'under_case_manager_review' ? 'selected' : '' }}>Under CM Review</option>
                                <option value="under_attorney_review" {{ request('status') == 'under_attorney_review' ? 'selected' : '' }}>Under Attorney Review</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Case Manager</label>
                            <select name="case_manager" class="form-select">
                                <option value="">All Case Managers</option>
                                <option value="unassigned" {{ request('case_manager') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                @foreach($caseManagers as $cm)
                                <option value="{{ $cm->id }}" {{ request('case_manager') == $cm->id ? 'selected' : '' }}>{{ $cm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Attorney</label>
                            <select name="attorney" class="form-select">
                                <option value="">All Attorneys</option>
                                <option value="unassigned" {{ request('attorney') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                                @foreach($attorneys as $attorney)
                                <option value="{{ $attorney->id }}" {{ request('attorney') == $attorney->id ? 'selected' : '' }}>{{ $attorney->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold d-flex align-items-center">
                <i class="fas fa-database me-2"></i>All Applications Database
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-2">{{ $applications->total() }} Total</span>
                <span class="badge bg-warning">{{ $applications->whereNull('case_manager_id')->count() }} Unassigned</span>
            </div>
        </div>
        <div class="card-body">
            @if($applications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th width="8%">ID</th>
                                <th width="20%">Client</th>
                                <th width="12%">Status</th>
                                <th width="15%">Case Manager</th>
                                <th width="15%">Attorney</th>
                                <th width="10%">Documents</th>
                                <th width="10%">Created</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                            <tr class="align-middle">
                                <td>
                                    <span class="badge bg-light text-dark border">#{{ $application->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center me-3" 
                                             style="width: 35px; height: 35px; font-size: 0.8rem; color: white; font-weight: bold;">
                                            {{ strtoupper(substr($application->user->name ?? 'N', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">{{ $application->user->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>{{ $application->user->email ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge status-badge
                                        @if($application->status == 'completed') bg-success
                                        @elseif($application->status == 'pending') bg-warning text-dark
                                        @elseif($application->status == 'under_case_manager_review') bg-info
                                        @elseif($application->status == 'under_attorney_review') bg-primary
                                        @elseif($application->status == 'assigned_to_attorney') bg-primary
                                        @elseif($application->status == 'documents_requested') bg-warning text-dark
                                        @elseif($application->status == 'approved') bg-success
                                        @elseif($application->status == 'rejected') bg-danger
                                        @elseif($application->status == 'draft') bg-secondary
                                        @else bg-secondary
                                        @endif
                                    ">
                                        @if($application->status == 'under_case_manager_review')
                                            <i class="fas fa-user-tie me-1"></i>CM Review
                                        @elseif($application->status == 'under_attorney_review')
                                            <i class="fas fa-gavel me-1"></i>Attorney Review
                                        @elseif($application->status == 'approved')
                                            <i class="fas fa-check-circle me-1"></i>Approved
                                        @elseif($application->status == 'rejected')
                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                        @elseif($application->status == 'draft')
                                            <i class="fas fa-edit me-1"></i>Draft
                                        @else
                                            <i class="fas fa-clock me-1"></i>{{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($application->case_manager_id)
                                        @php $caseManager = $caseManagers->find($application->case_manager_id) @endphp
                                        @if($caseManager)
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 25px; height: 25px; font-size: 0.7rem; color: white; font-weight: bold;">
                                                    {{ strtoupper(substr($caseManager->name, 0, 1)) }}
                                                </div>
                                                <small class="fw-bold text-primary">{{ $caseManager->name }}</small>
                                            </div>
                                        @else
                                            <small class="text-danger">Invalid CM</small>
                                        @endif
                                    @else
                                        <span class="text-warning">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Unassigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($application->attorney)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 25px; height: 25px; font-size: 0.7rem; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($application->attorney->name, 0, 1)) }}
                                            </div>
                                            <small class="fw-bold text-success">{{ $application->attorney->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-user-slash me-1"></i>Not assigned
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($application->documents->count() > 0)
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-file-check me-1"></i>{{ $application->documents->count() }} files
                                        </span>
                                    @else
                                        <span class="badge bg-danger status-badge">
                                            <i class="fas fa-file-times me-1"></i>No documents
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-plus me-1"></i>{{ $application->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.case-manager.case.view', $application->id) }}" 
                                           class="btn btn-sm btn-primary btn-action" 
                                           title="View Application Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$application->case_manager_id)
                                        <button class="btn btn-sm btn-success btn-action" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#assignCMModal{{ $application->id }}"
                                                title="Assign Case Manager">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($applications->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        <small>
                            Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} 
                            of {{ $applications->total() }} applications
                        </small>
                    </div>
                    <div>
                        {{ $applications->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="text-muted mb-3">No Applications Found</h4>
                    <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                        <i class="fas fa-info-circle me-1"></i>
                        No applications match your current filters. Try adjusting your search criteria.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('dashboard.case-manager.all-applications') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i> Clear Filters
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if($applications->count() > 0)
        <!-- Statistics Footer -->
        <div class="card-footer bg-light">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-primary mb-1">{{ $applications->total() }}</h6>
                        <small class="text-muted">Total Applications</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-warning mb-1">{{ $applications->whereNull('case_manager_id')->count() }}</h6>
                        <small class="text-muted">Unassigned</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-success mb-1">{{ $applications->where('status', 'approved')->count() }}</h6>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="text-info mb-1">{{ $applications->whereIn('status', ['under_case_manager_review', 'under_attorney_review'])->count() }}</h6>
                    <small class="text-muted">Under Review</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Assign Case Manager Modals -->
@foreach($applications as $application)
@if(!$application->case_manager_id)
<div class="modal fade" id="assignCMModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Case Manager - Application #{{ $application->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.case-manager.case.assign-case-manager', $application->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Client: <strong>{{ $application->user->name ?? 'N/A' }}</strong></label>
                        <br>
                        <small class="text-muted">{{ $application->user->email ?? 'N/A' }}</small>
                    </div>
                    <div class="form-group">
                        <label for="case_manager_id" class="form-label">Select Case Manager</label>
                        <select name="case_manager_id" class="form-select" required>
                            <option value="">Choose Case Manager...</option>
                            @foreach($caseManagers as $cm)
                            <option value="{{ $cm->id }}">{{ $cm->name }} ({{ $cm->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i> Assign Case Manager
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection