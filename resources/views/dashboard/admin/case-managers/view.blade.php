@extends('layouts.dashboard')

@section('title', 'Case Manager Profile')
@section('page-title', 'Case Manager Profile')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Case Manager Profile</h1>
            <p class="text-muted">{{ $user->name }} - Profile Overview</p>
        </div>
        <div>
            <a href="{{ route('admin.case-managers') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Case Managers
            </a>
            <div class="btn-group">
                <a href="{{ route('admin.case-managers.edit', $user) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <div class="avatar-initial rounded-circle bg-primary" style="width: 80px; height: 80px; font-size: 2rem; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">Case Manager</p>
                    <span class="badge {{ $user->is_suspended ?? false ? 'bg-danger' : 'bg-success' }}">
                        {{ $user->is_suspended ?? false ? 'Suspended' : 'Active' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">Full Name</label>
                                <p class="form-control-static">{{ $user->name }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">First Name</label>
                                <p class="form-control-static">{{ $user->first_name ?? 'Not provided' }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">Last Name</label>
                                <p class="form-control-static">{{ $user->last_name ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">Email Address</label>
                                <p class="form-control-static">{{ $user->email }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">Username</label>
                                <p class="form-control-static">{{ $user->username ?? 'Not provided' }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label font-weight-bold">Member Since</label>
                                <p class="form-control-static">{{ $user->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCases }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedCases }}</div>
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
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Cases</h6>
            <a href="{{ route('admin.case-managers.cases', $user) }}" class="btn btn-sm btn-primary">View All Cases</a>
        </div>
        <div class="card-body">
            @if($recentCases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Applicant</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCases as $case)
                            <tr>
                                <td>#{{ $case->id }}</td>
                                <td>{{ $case->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge 
                                        @if($case->status == 'completed') bg-success
                                        @elseif($case->status == 'pending') bg-warning
                                        @elseif($case->status == 'in_progress') bg-info
                                        @else bg-secondary
                                        @endif
                                    ">
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
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No cases assigned yet.</p>
                </div>
            @endif
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
.form-control-static {
    margin-bottom: 0;
    min-height: 20px;
    padding-top: 7px;
}
</style>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password for {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.case-managers.reset-password', $user) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Warning:</strong> This will reset the case manager's password. They will need to use the new password to log in.
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="password" required minlength="8" 
                               placeholder="Enter new password">
                        <small class="form-text text-muted">Minimum 8 characters required</small>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required 
                               placeholder="Confirm new password">
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_user" name="notify_user" value="1">
                        <label class="form-check-label" for="notify_user">
                            Send email notification to case manager about password change
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection