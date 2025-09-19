@extends('layouts.dashboard')

@section('title', 'Case Manager Dashboard')
@section('page-title', 'Case Manager Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="text-muted">Manage your assigned applications and track their progress</p>
        </div>
        <div class="text-end">
            <small class="text-muted">{{ now()->format('l, F d, Y') }}</small>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Assigned Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Documents Ready</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $documentsReady }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Awaiting Attorney</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $awaitingAttorney }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Review</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingReview }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Cases Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">My Assigned Cases</h6>
            <small class="text-muted">{{ $assignedCases->count() }} cases assigned to you</small>
        </div>
        <div class="card-body">
            @if($assignedCases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Case ID</th>
                                <th>Applicant</th>
                                <th>Status</th>
                                <th>Attorney</th>
                                <th>Documents</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedCases as $case)
                            <tr>
                                <td><strong>#{{ $case->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.75rem; color: white;">
                                                {{ strtoupper(substr($case->user->name ?? 'N', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $case->user->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $case->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($case->status == 'completed') bg-success
                                        @elseif($case->status == 'pending') bg-warning
                                        @elseif($case->status == 'under_case_manager_review') bg-info
                                        @elseif($case->status == 'assigned_to_attorney') bg-primary
                                        @elseif($case->status == 'documents_requested') bg-warning
                                        @else bg-secondary
                                        @endif
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($case->attorney)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem; color: white;">
                                                {{ strtoupper(substr($case->attorney->name, 0, 1)) }}
                                            </div>
                                            <small>{{ $case->attorney->name }}</small>
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#assignAttorneyModal{{ $case->id }}">
                                            <i class="fas fa-user-plus"></i> Assign
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if($case->documents->count() > 0)
                                        <span class="badge bg-success">{{ $case->documents->count() }} files</span>
                                    @else
                                        <span class="badge bg-warning">No documents</span>
                                    @endif
                                </td>
                                <td>{{ $case->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('dashboard.case-manager.case.view', $case->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($case->documents->count() > 0 && !$case->attorney_id)
                                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignAttorneyModal{{ $case->id }}">
                                                <i class="fas fa-user-tie"></i> Assign Attorney
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Assign Attorney Modal -->
                            <div class="modal fade" id="assignAttorneyModal{{ $case->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Assign Attorney - Case #{{ $case->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('dashboard.case-manager.case.assign-attorney', $case->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Applicant: <strong>{{ $case->user->name ?? 'N/A' }}</strong></label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="attorney_id" class="form-label">Select Attorney</label>
                                                    <select name="attorney_id" class="form-select" required>
                                                        <option value="">Choose Attorney...</option>
                                                        @foreach($availableAttorneys as $attorney)
                                                            <option value="{{ $attorney->id }}">{{ $attorney->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Assign Attorney</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Cases Assigned</h5>
                    <p class="text-muted">You don't have any cases assigned to you yet. Please contact your administrator.</p>
                    <a href="{{ route('dashboard.case-manager.applications') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-refresh"></i> Refresh Applications
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('dashboard.case-manager.applications') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-folder-open text-primary me-2"></i>
                            View All My Applications
                        </a>
                        <a href="{{ route('dashboard.case-manager.attorneys') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-users text-info me-2"></i>
                            View Available Attorneys
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card card-shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($assignedCases->take(3)->count() > 0)
                        @foreach($assignedCases->take(3) as $case)
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-file-alt text-muted"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Case #{{ $case->id }}</h6>
                                <small class="text-muted">{{ $case->user->name ?? 'N/A' }} - {{ $case->status }}</small>
                            </div>
                            <small class="text-muted">{{ $case->updated_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.card-shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}
</style>
@endsection