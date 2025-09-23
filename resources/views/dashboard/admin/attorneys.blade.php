@extends('layouts.dashboard')

@section('title', 'Attorneys Management')
@section('page-title', 'Attorneys Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Attorneys</h1>
            <p class="text-muted">Manage your attorneys and their case assignments</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAttorneyModal">
            <i class="fas fa-plus"></i> Add New Attorney
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Attorneys</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attorneys->total() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalActiveCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gavel fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Cases per Attorney</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $attorneys->total() > 0 ? round(($totalAssignedCases ?? 0) / $attorneys->total(), 1) : 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Approvals This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">+8%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attorneys Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Attorneys List</h6>
        </div>
        <div class="card-body">
            @if(isset($error))
                <div class="alert alert-danger">{{ $error }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Cases Summary</th>
                                <th>Workload</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attorneys as $attorney)
                            <tr>
                                <td>{{ $attorney->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-label-success">
                                                {{ strtoupper(substr($attorney->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $attorney->name }}</h6>
                                            <small class="text-muted">{{ $attorney->first_name }} {{ $attorney->last_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $attorney->email }}</td>
                                <td>{{ $attorney->username ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div>
                                            <span class="badge bg-primary me-1">{{ $attorney->total_assigned_cases ?? 0 }}</span>
                                            <small class="text-muted">Total Assigned</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-success me-1">{{ $attorney->active_cases_count ?? 0 }}</span>
                                            <small class="text-muted">Active</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning me-1">{{ $attorney->pending_review_count ?? 0 }}</span>
                                            <small class="text-muted">Pending Review</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-info me-1">{{ $attorney->completed_cases_count ?? 0 }}</span>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $totalCases = $attorney->total_assigned_cases ?? 0;
                                        $activeCases = $attorney->active_cases_count ?? 0;
                                        $workloadPercentage = $totalCases > 0 ? round(($activeCases / $totalCases) * 100) : 0;
                                        $workloadClass = $workloadPercentage > 80 ? 'bg-danger' : ($workloadPercentage > 60 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <div class="d-flex flex-column">
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar {{ $workloadClass }}" style="width: {{ $workloadPercentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $workloadPercentage }}% Active</small>
                                        @if($workloadPercentage > 80)
                                            <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> High Load</small>
                                        @elseif($workloadPercentage > 60)
                                            <small class="text-warning"><i class="fas fa-clock"></i> Moderate Load</small>
                                        @else
                                            <small class="text-success"><i class="fas fa-check-circle"></i> Available</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $attorney->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($attorney->is_suspended ?? false)
                                        <span class="badge bg-danger">Suspended</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.attorneys.view', $attorney) }}"><i class="fas fa-eye"></i> View Profile</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.attorneys.edit', $attorney) }}"><i class="fas fa-edit"></i> Edit</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.attorneys.cases', $attorney) }}"><i class="fas fa-gavel"></i> View Cases ({{ $attorney->total_assigned_cases ?? 0 }})</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.attorneys.performance', $attorney) }}"><i class="fas fa-chart-line"></i> Performance</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($attorney->is_suspended ?? false)
                                                <li><a class="dropdown-item text-success" href="{{ route('admin.attorneys.activate', $attorney) }}"><i class="fas fa-check"></i> Activate</a></li>
                                            @else
                                                <li><a class="dropdown-item text-warning" href="{{ route('admin.attorneys.suspend', $attorney) }}"><i class="fas fa-ban"></i> Suspend</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No attorneys found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($attorneys->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $attorneys->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Create Attorney Modal -->
<div class="modal fade" id="createAttorneyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Attorney</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.attorneys.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Attorney</button>
                </div>
            </form>
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
.bg-label-success {
    background-color: rgba(28, 200, 138, 0.12) !important;
    color: #1cc88a !important;
}
</style>
@endsection
