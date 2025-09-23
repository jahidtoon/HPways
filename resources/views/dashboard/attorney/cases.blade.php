@extends('layouts.dashboard')

@section('title', 'My Cases')
@section('page-title', 'My Cases')

@section('styles')
<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .case-card {
        background: rgba(255,255,255,0.95);
        border-radius: 1.25rem;
        box-shadow: 0 4px 24px rgba(78,115,223,0.10);
        backdrop-filter: blur(4px);
        border: 1px solid #e0e7ff;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    .case-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(78,115,223,0.15);
    }
    .case-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1.25rem 1.25rem 0 0;
        padding: 1.5rem;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-review {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-review:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-lg-3 px-xl-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">My Cases</h2>
                    <p class="text-muted">All applications assigned to you for review</p>
                </div>
                <a href="{{ url('/dashboard/attorney') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-glass">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Case ID</th>
                                    <th>Applicant</th>
                                    <th>Visa Type</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Submitted</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($assignedCases->count() > 0)
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
                                                    <h6 class="mb-0" style="font-size: 0.875rem;">{{ $case->user->name ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $case->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $case->visa_type ?? 'Not specified' }}</span>
                                        </td>
                                        <td>
                                            @if($case->status == 'assigned_to_attorney')
                                                <span class="badge bg-warning">Pending Review</span>
                                            @elseif($case->status == 'under_attorney_review')
                                                <span class="badge bg-info">Under Review</span>
                                            @elseif($case->status == 'attorney_feedback_provided')
                                                <span class="badge bg-primary">Feedback Sent</span>
                                            @elseif($case->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($case->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $case->documents->count() ?? 0 }} files</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $case->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $case->updated_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('dashboard.attorney.case.review', $case->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Review
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h4>No Cases Assigned</h4>
                                            <p class="text-muted">You don't have any cases assigned to you yet.</p>
                                            <a href="{{ url('/dashboard/attorney') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection