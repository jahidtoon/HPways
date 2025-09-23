@extends('layouts.dashboard')

@section('title', 'Attorney Cases')
@section('page-title', 'Attorney Cases')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Cases for {{ $user->name }}</h1>
            <p class="text-muted">All applications assigned to this attorney</p>
        </div>
        <div>
            <a href="{{ route('admin.attorneys.view', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Cases ({{ $cases->total() }})</h6>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="assigned_to_attorney">Assigned to Attorney</option>
                    <option value="under_attorney_review">Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="rfe_issued">RFE Issued</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            @if($cases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th>Case ID</th>
                                <th>Applicant</th>
                                <th>Visa Type</th>
                                <th>Status</th>
                                <th>Case Manager</th>
                                <th>Documents</th>
                                <th>Payments</th>
                                <th>Assigned Date</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cases as $case)
                            <tr>
                                <td><strong>#{{ $case->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-label-primary">
                                                {{ $case->user ? strtoupper(substr($case->user->name, 0, 1)) : 'N' }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $case->user->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $case->user->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ strtoupper($case->visa_type) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'assigned_to_attorney' => 'warning',
                                            'under_attorney_review' => 'info',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'rfe_issued' => 'warning',
                                            'attorney_feedback_provided' => 'primary'
                                        ];
                                        $color = $statusColors[$case->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($case->caseManager)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem; color: white;">
                                                {{ strtoupper(substr($case->caseManager->name, 0, 1)) }}
                                            </div>
                                            <small>{{ $case->caseManager->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($case->documents->count() > 0)
                                        <span class="badge bg-success">{{ $case->documents->count() }} files</span>
                                    @else
                                        <span class="badge bg-warning">No documents</span>
                                    @endif
                                </td>
                                <td>
                                    @if($case->payments->count() > 0)
                                        @php $latestPayment = $case->payments->sortByDesc('created_at')->first(); @endphp
                                        <span class="badge bg-{{ $latestPayment->status === 'succeeded' ? 'success' : ($latestPayment->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($latestPayment->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No payments</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $case->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $case->updated_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.application-detail', $case->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if(in_array($case->status, ['assigned_to_attorney', 'under_attorney_review']))
                                        <a href="/attorney/review-case/{{ $case->id }}" class="btn btn-sm btn-outline-success" target="_blank">
                                            <i class="fas fa-gavel"></i> Review
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small text-muted">
                        Showing {{ $cases->firstItem() }} to {{ $cases->lastItem() }} of {{ $cases->total() }} cases
                    </div>
                    {{ $cases->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No cases assigned to this attorney</h5>
                    <p class="text-muted">Cases will appear here when assigned by case managers</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const url = new URL(window.location);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
});

// Set the current filter value
const urlParams = new URLSearchParams(window.location.search);
const currentStatus = urlParams.get('status');
if (currentStatus) {
    document.getElementById('statusFilter').value = currentStatus;
}
</script>
@endsection