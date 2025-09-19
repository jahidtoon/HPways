@extends('layouts.dashboard')

@section('title', 'My Applications')
@section('page-title', 'My Applications')

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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-list me-2 text-primary"></i>My Assigned Applications
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                {{ $applications->total() }} applications assigned to me for case management
            </p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
            <button class="btn btn-primary" onclick="location.reload()" title="Refresh Data">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold d-flex align-items-center">
                <i class="fas fa-tasks me-2"></i>Applications Management
            </h6>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-2">{{ $applications->total() }} Total</span>
                <span class="badge bg-success">{{ $applications->where('status', '!=', 'draft')->count() }} Active</span>
            </div>
        </div>
        <div class="card-body">
            @if($applications->count() > 0)
                <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th width="8%">ID</th>
                                <th width="25%">Client</th>
                                <th width="15%">Status</th>
                                <th width="18%">Attorney</th>
                                <th width="12%">Documents</th>
                                <th width="10%">Created</th>
                                <th width="10%">Updated</th>
                                <th width="12%">Action</th>
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
                                             style="width: 40px; height: 40px; font-size: 0.9rem; color: white; font-weight: bold;">
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
                                    @if($application->attorney)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 28px; height: 28px; font-size: 0.75rem; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($application->attorney->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <small class="fw-bold text-success">{{ $application->attorney->name }}</small>
                                                <br><small class="text-muted">{{ $application->attorney->email }}</small>
                                            </div>
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
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-edit me-1"></i>{{ $application->updated_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.case-manager.case.view', $application->id) }}" 
                                       class="btn btn-sm btn-primary btn-action" 
                                       title="View Application Details">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                </td>
                            </tr>

                            <!-- Assign Attorney Modal -->
                            <div class="modal fade" id="assignAttorneyModal{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Assign Attorney - Case #{{ $application->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('dashboard.case-manager.case.assign-attorney', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Applicant: <strong>{{ $application->user->name ?? 'N/A' }}</strong></label>
                                                    <br>
                                                    <small class="text-muted">{{ $application->user->email ?? 'N/A' }}</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="attorney_id" class="form-label">Select Attorney</label>
                                                    <select name="attorney_id" class="form-select" required>
                                                        <option value="">Choose Attorney...</option>
                                                        @php
                                                            $attorneys = \App\Models\User::role('attorney')->get();
                                                        @endphp
                                                        @foreach($attorneys as $attorney)
                                                            <option value="{{ $attorney->id }}">{{ $attorney->name }} ({{ $attorney->email }})</option>
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

                            <!-- Request Documents Modal -->
                            <div class="modal fade" id="requestDocsModal{{ $application->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Request Documents - Case #{{ $application->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('dashboard.case-manager.case.request-documents', $application->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Applicant: <strong>{{ $application->user->name ?? 'N/A' }}</strong></label>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="document_list" class="form-label">Required Documents</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="passport" id="passport{{ $application->id }}">
                                                                <label class="form-check-label" for="passport{{ $application->id }}">Passport</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="birth_certificate" id="birth{{ $application->id }}">
                                                                <label class="form-check-label" for="birth{{ $application->id }}">Birth Certificate</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="education_certificate" id="education{{ $application->id }}">
                                                                <label class="form-check-label" for="education{{ $application->id }}">Education Certificate</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="employment_letter" id="employment{{ $application->id }}">
                                                                <label class="form-check-label" for="employment{{ $application->id }}">Employment Letter</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="bank_statement" id="bank{{ $application->id }}">
                                                                <label class="form-check-label" for="bank{{ $application->id }}">Bank Statement</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="document_list[]" value="medical_report" id="medical{{ $application->id }}">
                                                                <label class="form-check-label" for="medical{{ $application->id }}">Medical Report</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="message" class="form-label">Additional Message (Optional)</label>
                                                    <textarea name="message" class="form-control" rows="3" placeholder="Any additional instructions for the applicant..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">Request Documents</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
                        {{ $applications->links() }}
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="text-muted mb-3">No Applications Assigned Yet</h4>
                    <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                        <i class="fas fa-info-circle me-1"></i>
                        You don't have any applications assigned to you at the moment. 
                        When applications are assigned to you for case management, they will appear here.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
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
                        <h6 class="text-info mb-1">{{ $applications->whereIn('status', ['under_case_manager_review', 'under_attorney_review'])->count() }}</h6>
                        <small class="text-muted">Under Review</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border-end">
                        <h6 class="text-success mb-1">{{ $applications->where('status', 'approved')->count() }}</h6>
                        <small class="text-muted">Approved</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="text-warning mb-1">{{ $applications->whereNull('attorney_id')->count() }}</h6>
                    <small class="text-muted">Need Attorney</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection