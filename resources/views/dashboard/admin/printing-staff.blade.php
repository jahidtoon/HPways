@extends('layouts.dashboard')

@section('title', 'Printing Staff Management')
@section('page-title', 'Printing Staff Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Printing Staff</h1>
            <p class="text-muted">Manage your printing department staff and operations</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPrintingStaffModal">
            <i class="fas fa-plus"></i> Add New Staff
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
                                Total Staff</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $printingStaff->total() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-print fa-2x text-gray-300"></i>
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
                                Active Shipments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalShipments ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shipping-fast fa-2x text-gray-300"></i>
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
                                Print Queue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">15</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-ol fa-2x text-gray-300"></i>
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
                                Documents Printed Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Printing Staff Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Printing Staff List</h6>
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
                                <th>Department</th>
                                <th>Shift</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($printingStaff as $staff)
                            <tr>
                                <td>{{ $staff->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-label-warning">
                                                {{ strtoupper(substr($staff->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $staff->name }}</h6>
                                            <small class="text-muted">{{ $staff->first_name }} {{ $staff->last_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->username ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-primary">Printing & Shipping</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">Day Shift</span>
                                </td>
                                <td>{{ $staff->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye"></i> View Profile</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit"></i> Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-print"></i> View Tasks</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-clock"></i> Work Schedule</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-ban"></i> Suspend</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No printing staff found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($printingStaff->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $printingStaff->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Print Queue Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span>Documents in Queue</span>
                        <span class="badge bg-warning">15</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span>Currently Printing</span>
                        <span class="badge bg-info">3</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Ready for Shipping</span>
                        <span class="badge bg-success">8</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Performance</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span>Documents Processed Today</span>
                        <span class="text-success font-weight-bold">24</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span>Average Processing Time</span>
                        <span class="text-info font-weight-bold">2.5 hrs</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Efficiency Rate</span>
                        <span class="text-success font-weight-bold">94%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Printing Staff Modal -->
<div class="modal fade" id="createPrintingStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Printing Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.printing-staff.create') }}" method="POST">
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
                    <button type="submit" class="btn btn-primary">Create Staff Member</button>
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
.bg-label-warning {
    background-color: rgba(246, 194, 62, 0.12) !important;
    color: #f6c23e !important;
}
</style>
@endsection
