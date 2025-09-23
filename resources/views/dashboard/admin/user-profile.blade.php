@extends('layouts.dashboard')

@section('title', 'User Profile - ' . $user->name)
@section('page-title', 'User Profile')

@section('content')
<div class="container-fluid">
    <!-- User Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-4">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(79, 70, 229, 0.1) 0%, rgba(79, 70, 229, 0.2) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="font-size: 32px; color: var(--primary);"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-2">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No Role Assigned</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <p class="mb-0">{{ $user->name ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <p class="mb-0">{{ $user->email ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone</label>
                                <p class="mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p class="mb-0">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Address</label>
                                <p class="mb-0">{{ $user->address ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Member Since</label>
                                <p class="mb-0">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications -->
            @if($user->applications && $user->applications->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Applications ({{ $user->applications->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($user->applications as $application)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Application #{{ $application->id }}</h6>
                                    <small class="text-muted">Status: {{ ucfirst($application->status ?? 'pending') }}</small>
                                </div>
                                <a href="{{ route('admin.application-detail', $application->id) }}" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.edit-user', $user->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit User
                        </a>
                        <button class="btn btn-outline-warning" onclick="resetPassword({{ $user->id }})">
                            <i class="fas fa-key me-2"></i>Reset Password
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </div>
                </div>
            </div>

            <!-- User Stats -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-number">{{ $user->applications ? $user->applications->count() : 0 }}</div>
                            <div class="stat-label">Applications</div>
                        </div>
                        <div class="col-6">
                            <div class="stat-number">{{ $user->roles ? $user->roles->count() : 0 }}</div>
                            <div class="stat-label">Roles</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
}
.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
</style>

<!-- Password Reset Modal -->
<div class="modal fade" id="passwordResetModal" tabindex="-1" aria-labelledby="passwordResetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordResetModalLabel">
                    <i class="fas fa-key text-warning me-2"></i>Password Reset Successful
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-success mb-3">Password has been reset successfully!</h6>
                    <p class="text-muted mb-3">A temporary password has been generated for the user.</p>
                </div>
                <div class="alert alert-info">
                    <strong>New Temporary Password:</strong>
                    <div class="mt-2">
                        <code id="newPasswordDisplay" class="fs-5 text-primary" style="font-family: monospace; background: #f8f9fa; padding: 8px 12px; border-radius: 4px; display: block; text-align: center;"></code>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> Please inform the user to change this password immediately after their first login for security reasons.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyPassword()">
                    <i class="fas fa-copy me-1"></i>Copy Password
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password? A temporary password will be generated.')) {
        fetch(`{{ url('/admin/users') }}/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show the modal with the new password
                document.getElementById('newPasswordDisplay').textContent = data.new_password;
                const modal = new bootstrap.Modal(document.getElementById('passwordResetModal'));
                modal.show();
            } else {
                alert('Failed to reset password. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting the password.');
        });
    }
}

function copyPassword() {
    const passwordText = document.getElementById('newPasswordDisplay').textContent;
    navigator.clipboard.writeText(passwordText).then(function() {
        // Show a brief success message
        const copyBtn = document.querySelector('[onclick="copyPassword()"]');
        const originalText = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
        copyBtn.classList.remove('btn-primary');
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalText;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-primary');
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy password: ', err);
        alert('Failed to copy password to clipboard');
    });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`{{ url('/admin/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                alert('User deleted successfully!');
                window.location.href = '{{ route("admin.users") }}';
            } else {
                return response.json().then(data => {
                    alert(data.message || 'Failed to delete user. Please try again.');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        });
    }
}
</script>
@endsection