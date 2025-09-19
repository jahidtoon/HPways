@extends('layouts.dashboard')

@section('title', 'Case Managers Management')
@section('page-title', 'Case Managers Management')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Case Managers</h1>
            <p class="text-muted">Manage your case managers and their assignments</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCaseManagerModal">
            <i class="fas fa-plus"></i> Add New Case Manager
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
                                Total Case Managers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $caseManagers->total() ?? 0 }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Cases</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCases ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Average Cases per Manager</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $caseManagers->total() > 0 ? round($totalCases / $caseManagers->total(), 1) : 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">+12%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Case Managers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Case Managers List</h6>
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
                                <th>Active Cases</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($caseManagers as $manager)
                            <tr>
                                <td>{{ $manager->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-label-primary">
                                                {{ strtoupper(substr($manager->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $manager->name }}</h6>
                                            <small class="text-muted">{{ $manager->first_name }} {{ $manager->last_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $manager->email }}</td>
                                <td>{{ $manager->username ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $manager->applications->count() ?? 0 }}</span>
                                </td>
                                <td>{{ $manager->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($manager->is_suspended ?? false)
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
                                            <li><a class="dropdown-item" href="{{ route('admin.case-managers.view', $manager) }}"><i class="fas fa-eye"></i> View Profile</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.case-managers.edit', $manager) }}"><i class="fas fa-edit"></i> Edit</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.case-managers.cases', $manager) }}"><i class="fas fa-tasks"></i> View Cases</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($manager->is_suspended ?? false)
                                                <li>
                                                    <form action="{{ route('admin.case-managers.activate', $manager) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to activate this case manager?')">
                                                            <i class="fas fa-check"></i> Activate
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.case-managers.suspend', $manager) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to suspend this case manager?')">
                                                            <i class="fas fa-ban"></i> Suspend
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No case managers found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($caseManagers->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $caseManagers->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Create Case Manager Modal -->
<div class="modal fade" id="createCaseManagerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Case Manager</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.case-managers.create') }}" method="POST">
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
                    <button type="submit" class="btn btn-primary">Create Case Manager</button>
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
.bg-label-primary {
    background-color: rgba(78, 115, 223, 0.12) !important;
    color: #4e73df !important;
}
</style>
@endsection
