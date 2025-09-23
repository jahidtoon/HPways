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
                                    <span class="badge bg-{{ ($staff->is_active ?? true) ? 'success' : 'danger' }}" id="status-{{ $staff->id }}">
                                        {{ ($staff->is_active ?? true) ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editStaff({{ $staff->id }})">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="toggleStaffStatus({{ $staff->id }})">
                                                    <i class="fas fa-toggle-{{ ($staff->is_active ?? true) ? 'on' : 'off' }}"></i> 
                                                    {{ ($staff->is_active ?? true) ? 'Deactivate' : 'Activate' }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="assignJobs({{ $staff->id }})">
                                                    <i class="fas fa-tasks"></i> Assign Jobs
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="/printing" target="_blank">
                                                    <i class="fas fa-external-link-alt"></i> View Dashboard
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteStaff({{ $staff->id }}, '{{ $staff->name }}')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                            </li>
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

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStaffModalLabel">Edit Printing Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStaffForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Password Change:</strong> Leave password fields empty to keep current password unchanged.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_password" class="form-label">New Password (optional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Jobs Modal -->
<div class="modal fade" id="assignJobsModal" tabindex="-1" aria-labelledby="assignJobsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignJobsModalLabel">Assign Print Jobs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="assignJobsContent">
                    Loading available jobs...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitJobAssignment()">Assign Selected Jobs</button>
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
.bg-label-warning {
    background-color: rgba(246, 194, 62, 0.12) !important;
    color: #f6c23e !important;
}
</style>

<script>
let currentStaffId = null;

// Edit Staff Function
function editStaff(staffId) {
    fetch(`/admin/printing-staff/${staffId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }
        
        if (!data.success) {
            alert('Failed to load staff information');
            return;
        }
        
        // Clear password fields
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_password_confirmation').value = '';
        
        // Populate form fields
        document.getElementById('edit_name').value = data.name || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_first_name').value = data.first_name || '';
        document.getElementById('edit_last_name').value = data.last_name || '';
        document.getElementById('edit_username').value = data.username || '';
        document.getElementById('edit_status').value = data.status ? 'active' : 'inactive';
        
        // Set form action
        document.getElementById('editStaffForm').action = `/admin/printing-staff/${staffId}`;
        
        // Store current staff ID for reference
        document.getElementById('editStaffForm').dataset.staffId = staffId;
        
        // Show modal
        new bootstrap.Modal(document.getElementById('editStaffModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load staff information: ' + error.message);
    });
}

// Toggle Staff Status
function toggleStaffStatus(staffId) {
    if (!confirm('Are you sure you want to change this staff member\'s status?')) {
        return;
    }
    
    fetch(`/admin/printing-staff/${staffId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge
            const statusBadge = document.getElementById(`status-${staffId}`);
            if (statusBadge) {
                statusBadge.className = `badge bg-${data.status === 'active' ? 'success' : 'danger'}`;
                statusBadge.textContent = data.status === 'active' ? 'Active' : 'Inactive';
            }
            
            // Show success message
            showAlert('success', data.message);
            
            // Reload page after 1 second to update action buttons
            setTimeout(() => location.reload(), 1000);
        } else {
            alert(data.error || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update staff status');
    });
}

// Delete Staff
function deleteStaff(staffId, staffName) {
    if (!confirm(`Are you sure you want to delete ${staffName}? This action cannot be undone.`)) {
        return;
    }
    
    fetch(`/admin/printing-staff/${staffId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Remove row from table
            document.querySelector(`tr:has([onclick*="${staffId}"])`).remove();
        } else {
            alert(data.error || 'Failed to delete staff member');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete staff member');
    });
}

// Assign Jobs Function
function assignJobs(staffId) {
    currentStaffId = staffId;
    
    // Load available jobs
    document.getElementById('assignJobsContent').innerHTML = 'Loading available jobs...';
    
    // For now, show placeholder content
    document.getElementById('assignJobsContent').innerHTML = `
        <div class="alert alert-info">
            <h6>Available Print Jobs</h6>
            <p>This feature will show pending print jobs that can be assigned to this staff member.</p>
            <p><strong>Coming Soon:</strong> Job assignment interface</p>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('assignJobsModal')).show();
}

// Submit Job Assignment
function submitJobAssignment() {
    if (!currentStaffId) return;
    
    // Placeholder for job assignment logic
    alert('Job assignment feature will be implemented here');
    bootstrap.Modal.getInstance(document.getElementById('assignJobsModal')).hide();
}

// Show Alert Function
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Edit Staff Form
    document.getElementById('editStaffForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const actionUrl = this.action;
        
        // Check if passwords match if password is provided
        const password = document.getElementById('edit_password').value;
        const passwordConfirm = document.getElementById('edit_password_confirmation').value;
        
        if (password && password !== passwordConfirm) {
            alert('Passwords do not match');
            return;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;
        
        // Add method spoofing for PUT request
        formData.append('_method', 'PUT');
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
                showAlert('success', data.message || 'Staff member updated successfully');
                setTimeout(() => location.reload(), 1000);
            } else if (data.errors) {
                // Handle validation errors
                let errorMsg = 'Validation errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMsg += `${field}: ${data.errors[field].join(', ')}\n`;
                });
                alert(errorMsg);
            } else {
                alert(data.error || 'Failed to update staff member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update staff member: ' + error.message);
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endsection
