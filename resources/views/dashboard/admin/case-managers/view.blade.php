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
        <div class="col-xl-3 col-lg-4">
            <div class="card shadow h-100">
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
                    <span class="badge {{ $user->is_suspended ?? false ? 'bg-danger' : 'bg-success' }} mb-3">
                        {{ $user->is_suspended ?? false ? 'Suspended' : 'Active' }}
                    </span>
                    <hr>
                    <div class="text-start">
                        <small class="text-muted d-block">Department</small>
                        <strong>Legal Processing</strong>
                        <br><br>
                        <small class="text-muted d-block">Employee ID</small>
                        <strong>CM-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-5 col-lg-4">
            <div class="card shadow h-100">
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

        <div class="col-xl-4 col-lg-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Cases</span>
                            <span class="badge bg-primary">{{ $totalCases }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Active Cases</span>
                            <span class="badge bg-info">{{ $activeCases }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Completed</span>
                            <span class="badge bg-success">{{ $completedCases }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="stat-item mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Success Rate</span>
                            <span class="font-weight-bold text-success">
                                {{ $totalCases > 0 ? round(($completedCases / $totalCases) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Avg. Processing</span>
                            <span class="font-weight-bold text-info">{{ $avgProcessingDays }} days</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-xl-7 col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                                <h4 class="font-weight-bold text-primary">{{ $totalCases }}</h4>
                                <small class="text-muted">Total Cases</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <i class="fas fa-tasks fa-2x text-info mb-2"></i>
                                <h4 class="font-weight-bold text-info">{{ $activeCases }}</h4>
                                <small class="text-muted">Active Cases</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h4 class="font-weight-bold text-success">{{ $completedCases }}</h4>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="metric-card">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h4 class="font-weight-bold text-warning">{{ $pendingCases }}</h4>
                                <small class="text-muted">Pending Cases</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Monthly Activity</h6>
                </div>
                <div class="card-body">
                    <div class="activity-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Cases Assigned (This Month)</span>
                            <span class="font-weight-bold text-primary">{{ $thisMonthCases }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalCases > 0 ? min(100, round(($thisMonthCases / max($totalCases,1)) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="activity-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Cases Processed (This Month)</span>
                            <span class="font-weight-bold text-info">{{ $processedThisMonth }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalCases > 0 ? min(100, round(($processedThisMonth / max($totalCases,1)) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="activity-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Completed (This Month)</span>
                            <span class="font-weight-bold text-success">{{ $completedThisMonth }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 5px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalCases > 0 ? min(100, round(($completedThisMonth / max($totalCases,1)) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">Performance this month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cases and Activity -->
    <div class="row mb-4">
        <div class="col-xl-7 col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Cases</h6>
                    <a href="{{ route('admin.case-managers.cases', $user) }}" class="btn btn-sm btn-primary">View All Cases</a>
                </div>
                <div class="card-body">
                    @if($recentCases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Applicant</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCases as $case)
                                    <tr>
                                        <td><strong>#{{ $case->id }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-initial rounded-circle bg-light text-dark" style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($case->user->name ?? 'N', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <span>{{ $case->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
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
                                                <i class="fas fa-eye"></i>
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
        
        <div class="col-xl-5 col-lg-6">
            <div class="row">
                <!-- Work Schedule -->
                <div class="col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Work Schedule</h6>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Monday - Friday</span>
                                    <span class="font-weight-bold">9:00 AM - 5:00 PM</span>
                                </div>
                            </div>
                            <div class="schedule-item mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Current Status</span>
                                    <span class="badge bg-success">Available</span>
                                </div>
                            </div>
                            <div class="schedule-item mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Next Break</span>
                                    <span class="font-weight-bold text-info">2:30 PM</span>
                                </div>
                            </div>
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Overtime This Week</span>
                                    <span class="font-weight-bold text-warning">2.5 hrs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Recent Activity</h6>
                        </div>
                        <div class="card-body">
                            <div class="activity-timeline">
                                @forelse($recentActivity as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-{{ $activity['color'] }}"></div>
                                    <div class="timeline-content">
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                        <p class="mb-0">{{ $activity['action'] }} #{{ $activity['id'] }}</p>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-3">
                                    <i class="fas fa-clock fa-2x text-gray-300 mb-2"></i>
                                    <p class="text-muted mb-0">No recent activity</p>
                                </div>
                                @endforelse
                            </div>
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
.form-control-static {
    margin-bottom: 0;
    min-height: 20px;
    padding-top: 7px;
}
.metric-card {
    padding: 15px;
    border-radius: 8px;
    background: rgba(0,0,0,0.02);
    transition: all 0.3s ease;
}
.metric-card:hover {
    background: rgba(0,0,0,0.05);
    transform: translateY(-2px);
}
.stat-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.stat-item:last-child {
    border-bottom: none;
}
.activity-item {
    padding: 8px 0;
}
.h-100 {
    height: 100% !important;
}
.schedule-item {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.schedule-item:last-child {
    border-bottom: none;
}
.activity-timeline {
    position: relative;
    padding-left: 20px;
}
.timeline-item {
    position: relative;
    padding-left: 25px;
}
.timeline-marker {
    position: absolute;
    left: -10px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}
.timeline-item:before {
    content: '';
    position: absolute;
    left: -4px;
    top: 17px;
    height: calc(100% - 12px);
    width: 2px;
    background: #e9ecef;
}
.timeline-item:last-child:before {
    display: none;
}
.timeline-content p {
    font-size: 0.875rem;
    font-weight: 500;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.025);
}
.avatar-sm .avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
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