@extends('layouts.dashboard')

@section('title', 'Attorney Profile')
@section('page-title', 'Attorney Profile')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Attorney Profile</h1>
            <p class="text-muted">Detailed information and case management for {{ $user->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.attorneys') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Attorneys
            </a>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.attorneys.cases', $user) }}" class="btn btn-primary">
                    <i class="fas fa-gavel"></i> View All Cases
                </a>
                <a href="{{ route('admin.attorneys.performance', $user) }}" class="btn btn-info">
                    <i class="fas fa-chart-line"></i> Performance
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Attorney Info Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Attorney Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <div class="avatar-initial rounded-circle bg-primary" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-3">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <span class="badge bg-{{ $user->is_suspended ? 'danger' : 'success' }} fs-6">
                            {{ $user->is_suspended ? 'Suspended' : 'Active' }}
                        </span>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-2">{{ $user->email }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Username</label>
                            <p class="mb-2">{{ $user->username ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Joined Date</label>
                            <p class="mb-2">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Specialization</label>
                            <p class="mb-2">Immigration Law</p>
                        </div>
                    </div>

                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                            <i class="fas fa-key"></i> Reset Password
                        </button>
                        @if($user->is_suspended)
                            <form method="POST" action="{{ route('admin.attorneys.activate', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> Activate Attorney
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.attorneys.suspend', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to suspend this attorney?')">
                                    <i class="fas fa-ban"></i> Suspend Attorney
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-lg-8">
            <div class="row mb-4">
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Cases</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_cases'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Cases</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_cases'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Review</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_review'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_cases'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Cases -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Cases</h6>
                    <a href="{{ route('admin.attorneys.cases', $user) }}" class="btn btn-sm btn-primary">View All</a>
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
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedCases->take(5) as $case)
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
                                                    <div class="fw-medium">{{ $case->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $case->visa_type }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'assigned_to_attorney' => 'warning',
                                                    'under_attorney_review' => 'info',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'rfe_issued' => 'warning'
                                                ];
                                                $color = $statusColors[$case->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst(str_replace('_', ' ', $case->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $case->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.application-detail', $case->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No cases assigned yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Attorney Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset the password for <strong>{{ $user->name }}</strong>?</p>
                <p class="text-muted">A new temporary password will be generated and displayed.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.attorneys.reset-password', $user) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection