@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog me-2 text-primary"></i>Settings
        </h1>
        <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <!-- Profile Settings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Role</label>
                                    <input type="text" class="form-control" value="Case Manager" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Member Since</label>
                                    <input type="text" class="form-control" value="{{ $user->created_at->format('M d, Y') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Profile information is managed by the administrator. Contact admin for changes.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('dashboard.case-manager.applications') }}" class="btn btn-primary">
                            <i class="fas fa-clipboard-list me-2"></i> My Applications
                        </a>
                        <a href="{{ route('dashboard.case-manager.all-applications') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list-alt me-2"></i> All Applications
                        </a>
                        <a href="{{ route('dashboard.case-manager.reports') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-chart-bar me-2"></i> View Reports
                        </a>
                        <a href="{{ route('dashboard.case-manager.documents') }}" class="btn btn-outline-info">
                            <i class="fas fa-folder-open me-2"></i> Document Manager
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Last Login:</span>
                            <span>{{ now()->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Assigned Cases:</span>
                            <span class="badge bg-primary">{{ $user->managedCases()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Status:</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection