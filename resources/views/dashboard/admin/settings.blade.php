@extends('layouts.dashboard')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('styles')
<style>
    .settings-header {
        background: linear-gradient(135deg, #3a36d8 0%, #2a2aa0 100%);
        color: white;
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(58, 54, 216, 0.18);
    }
    
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        padding: 1.25rem 1.5rem;
    }
    
    .card-header h5 {
        font-size: 1.15rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .card-header h5 i {
        margin-right: 0.75rem;
        color: #3a36d8;
    }
    
    .btn-primary {
        background: #3a36d8;
        border-color: #3a36d8;
        border-radius: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background: #2a2aa0;
        border-color: #2a2aa0;
    }
    
    .nav-pills .nav-link {
        color: #718096;
        font-weight: 500;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .nav-pills .nav-link:hover {
        background-color: #f7fafc;
    }
    
    .nav-pills .nav-link.active {
        background-color: #3a36d8;
        color: white;
    }
    
    .nav-pills .nav-link i {
        margin-right: 0.75rem;
    }
    
    .settings-item {
        padding: 1.5rem 0;
        border-bottom: 1px solid #f0f4f8;
    }
    
    .settings-item:last-child {
        border-bottom: none;
    }
    
    .settings-item h6 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .settings-item p {
        color: #718096;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    
    .form-switch {
        padding-left: 2.5rem;
    }
    
    .form-switch .form-check-input {
        width: 3rem;
        height: 1.5rem;
        margin-left: -2.5rem;
    }
    
    .form-switch .form-check-input:checked {
        background-color: #3a36d8;
        border-color: #3a36d8;
    }
    
    .settings-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f4f8;
    }
</style>
@endsection

@section('content')
<!-- Settings Header -->
<div class="settings-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-2">System Settings</h1>
            <p class="mb-0">Configure and customize your application settings</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-light"><i class="fas fa-sync-alt me-2"></i>Reset to Default</button>
        </div>
    </div>
</div>

<div class="row">
    <!-- Settings Navigation -->
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                    <a class="nav-link active" id="general-tab" data-bs-toggle="pill" href="#general" role="tab">
                        <i class="fas fa-cog"></i>General Settings
                    </a>
                    <a class="nav-link" id="email-tab" data-bs-toggle="pill" href="#email" role="tab">
                        <i class="fas fa-envelope"></i>Email Configuration
                    </a>
                    <a class="nav-link" id="notifications-tab" data-bs-toggle="pill" href="#notifications" role="tab">
                        <i class="fas fa-bell"></i>Notifications
                    </a>
                    <a class="nav-link" id="security-tab" data-bs-toggle="pill" href="#security" role="tab">
                        <i class="fas fa-shield-alt"></i>Security
                    </a>
                    <a class="nav-link" id="api-tab" data-bs-toggle="pill" href="#api" role="tab">
                        <i class="fas fa-code"></i>API Settings
                    </a>
                    <a class="nav-link" id="backup-tab" data-bs-toggle="pill" href="#backup" role="tab">
                        <i class="fas fa-database"></i>Backup & Restore
                    </a>
                    <a class="nav-link" id="logs-tab" data-bs-toggle="pill" href="#logs" role="tab">
                        <i class="fas fa-file-alt"></i>System Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Content -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <h5 class="settings-section-title">General Settings</h5>
                        
                        <div class="settings-item">
                            <h6>System Name</h6>
                            <p>This is the name that appears throughout the application.</p>
                            <div class="mb-3">
                                <input type="text" class="form-control" value="Horizon Pathways">
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <h6>Timezone</h6>
                            <p>Set the default timezone for the application.</p>
                            <div class="mb-3">
                                <select class="form-select">
                                    <option selected>UTC</option>
                                    <option>America/New_York</option>
                                    <option>America/Los_Angeles</option>
                                    <option>Europe/London</option>
                                    <option>Asia/Tokyo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <h6>Date Format</h6>
                            <p>Choose how dates should be displayed throughout the application.</p>
                            <div class="mb-3">
                                <select class="form-select">
                                    <option selected>MM/DD/YYYY</option>
                                    <option>DD/MM/YYYY</option>
                                    <option>YYYY-MM-DD</option>
                                    <option>DD.MM.YYYY</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <h6>Maintenance Mode</h6>
                            <p>When enabled, users will not be able to access the application.</p>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenanceMode">
                                <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                    
                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <h5 class="settings-section-title">Email Configuration</h5>
                        
                        <div class="settings-item">
                            <h6>Mail Driver</h6>
                            <p>Select which mail service to use for sending emails.</p>
                            <div class="mb-3">
                                <select class="form-select">
                                    <option selected>SMTP</option>
                                    <option>Mailgun</option>
                                    <option>Amazon SES</option>
                                    <option>Sendgrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <h6>SMTP Configuration</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Host</label>
                                    <input type="text" class="form-control" placeholder="smtp.example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SMTP Port</label>
                                    <input type="text" class="form-control" placeholder="587">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" placeholder="user@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Encryption</label>
                                    <select class="form-select">
                                        <option selected>TLS</option>
                                        <option>SSL</option>
                                        <option>None</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <h6>Sender Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">From Name</label>
                                    <input type="text" class="form-control" placeholder="Horizon Pathways">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">From Email</label>
                                    <input type="email" class="form-control" placeholder="no-reply@horizonpathways.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-item">
                            <button type="button" class="btn btn-outline-primary">Test Email Configuration</button>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                    
                    <!-- Other Settings Tabs -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <h5 class="settings-section-title">Notification Settings</h5>
                        <p class="text-muted">Configure what notifications are sent and how they're delivered.</p>
                        
                        <!-- Content placeholder -->
                        <div class="alert alert-info">
                            This section is under development.
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <h5 class="settings-section-title">Security Settings</h5>
                        <p class="text-muted">Configure security settings for your application.</p>
                        
                        <!-- Content placeholder -->
                        <div class="alert alert-info">
                            This section is under development.
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="api" role="tabpanel">
                        <h5 class="settings-section-title">API Settings</h5>
                        <p class="text-muted">Manage API keys and configure API endpoints.</p>
                        
                        <!-- Content placeholder -->
                        <div class="alert alert-info">
                            This section is under development.
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="backup" role="tabpanel">
                        <h5 class="settings-section-title">Backup & Restore</h5>
                        <p class="text-muted">Configure backup settings and restore data from backups.</p>
                        
                        <!-- Content placeholder -->
                        <div class="alert alert-info">
                            This section is under development.
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="logs" role="tabpanel">
                        <h5 class="settings-section-title">System Logs</h5>
                        <p class="text-muted">View and download system logs.</p>
                        
                        <!-- Content placeholder -->
                        <div class="alert alert-info">
                            This section is under development.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Bootstrap 5 tab functionality
        const triggerTabList = [].slice.call(document.querySelectorAll('#settings-tab a'));
        triggerTabList.forEach(function(triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
</script>
@endsection
