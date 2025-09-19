@extends('layouts.dashboard')

@section('title', 'Case Manager Cases')
@section('page-title', 'Case Manager Cases')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Cases for {{ $user->name }}</h1>
            <p class="text-muted">All applications assigned to this case manager</p>
        </div>
        <div>
            <a href="{{ route('admin.case-managers.view', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Cases ({{ $cases->total() }})</h6>
        </div>
        <div class="card-body">
            @if($cases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Attorney</th>
                                <th>Created</th>
                                <th>Updated</th>
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
                                            <h6 class="mb-0">{{ $case->user->name ?? 'Unknown' }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $case->user->email ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-warning',
                                            'in_progress' => 'bg-info',
                                            'document_review' => 'bg-primary',
                                            'completed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'approved' => 'bg-success'
                                        ];
                                        $statusColor = $statusColors[$case->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($case->attorney)
                                        <span class="text-success">
                                            <i class="fas fa-user-tie me-1"></i>
                                            {{ $case->attorney->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus-circle me-1"></i>
                                            Not assigned
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $case->created_at->format('M d, Y') }}</td>
                                <td>{{ $case->updated_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.application-detail', $case->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cases->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $cases->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-gray-300 mb-4"></i>
                    <h5 class="text-gray-600">No Cases Assigned</h5>
                    <p class="text-muted">This case manager hasn't been assigned any cases yet.</p>
                    <a href="{{ route('admin.applications') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Assign Cases
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Case Statistics -->
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cases->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cases->where('status', 'pending')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">In Progress</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cases->whereIn('status', ['in_progress', 'document_review'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cases->whereIn('status', ['completed', 'approved'])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
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
.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    font-weight: 600;
}
.bg-label-primary {
    background-color: rgba(78, 115, 223, 0.1) !important;
    color: #4e73df !important;
}
</style>
@endsection