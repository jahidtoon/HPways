@extends('layouts.dashboard')

@section('title', 'Admin Reports')
@section('page-title', 'Reports & Analytics')

@section('styles')
<style>
    .reports-header {
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
    
    .btn-outline-primary {
        border: 1px solid #3a36d8;
        color: #3a36d8;
        background: transparent;
        border-radius: 0.5rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-outline-primary:hover {
        background: #3a36d8;
        color: white;
    }
    
    .filter-section {
        background-color: #f8fafc;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .report-tab {
        border-radius: 0.5rem;
        padding: 1rem;
        background-color: white;
        border: 1px solid rgba(226, 232, 240, 0.8);
        transition: all 0.2s ease;
        cursor: pointer;
        margin-bottom: 1rem;
    }
    
    .report-tab:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }
    
    .report-tab.active {
        border-color: #3a36d8;
        background-color: rgba(58, 54, 216, 0.05);
    }
    
    .report-tab i {
        color: #3a36d8;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .report-tab h6 {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .report-tab p {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0;
    }
</style>
@endsection

@section('content')
<!-- Reports Header -->
<div class="reports-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3 mb-2">Reports & Analytics</h1>
            <p class="mb-0">Generate and view comprehensive reports about your platform</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-light me-2"><i class="fas fa-calendar me-2"></i>Date Range</button>
            <button class="btn btn-light"><i class="fas fa-download me-2"></i>Export</button>
        </div>
    </div>
</div>

<!-- Report Types -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="report-tab active text-center">
            <i class="fas fa-users d-block"></i>
            <h6>User Reports</h6>
            <p>User registration and activity statistics</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="report-tab text-center">
            <i class="fas fa-clipboard-list d-block"></i>
            <h6>Application Reports</h6>
            <p>Visa application trends and statuses</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="report-tab text-center">
            <i class="fas fa-chart-line d-block"></i>
            <h6>Performance Reports</h6>
            <p>System and processing performance</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="report-tab text-center">
            <i class="fas fa-file-invoice-dollar d-block"></i>
            <h6>Financial Reports</h6>
            <p>Revenue and payment statistics</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Date Range</label>
            <select class="form-select">
                <option selected>Last 30 Days</option>
                <option>This Month</option>
                <option>Last Quarter</option>
                <option>This Year</option>
                <option>Custom Range</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Group By</label>
            <select class="form-select">
                <option selected>Day</option>
                <option>Week</option>
                <option>Month</option>
                <option>Quarter</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Format</label>
            <div class="btn-group w-100">
                <button class="btn btn-outline-secondary active">Chart</button>
                <button class="btn btn-outline-secondary">Table</button>
                <button class="btn btn-outline-secondary">Both</button>
            </div>
        </div>
    </div>
</div>

<!-- User Registration Chart -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-user-plus"></i>User Registrations</h5>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary active">Day</button>
            <button class="btn btn-sm btn-outline-secondary">Week</button>
            <button class="btn btn-sm btn-outline-secondary">Month</button>
        </div>
    </div>
    <div class="card-body">
        <canvas id="userRegistrationsChart" height="300"></canvas>
    </div>
</div>

<!-- Role Distribution Chart -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users-cog"></i>User Role Distribution</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <canvas id="userRolesChart" height="300"></canvas>
            </div>
            <div class="col-md-4">
                <div class="mt-4">
                    <h6 class="mb-3">Role Breakdown</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Applicants</span>
                        <span class="fw-bold">65%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Case Managers</span>
                        <span class="fw-bold">20%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Attorneys</span>
                        <span class="fw-bold">10%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Admins</span>
                        <span class="fw-bold">5%</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 5%" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Application Status Chart -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-tasks"></i>Application Status Distribution</h5>
    </div>
    <div class="card-body">
        <canvas id="applicationStatusChart" height="300"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if chart containers exist before initializing charts
        const registrationsChartElement = document.getElementById('userRegistrationsChart');
        const rolesChartElement = document.getElementById('userRolesChart');
        const statusChartElement = document.getElementById('applicationStatusChart');
        
        // User Registrations Chart
        if (registrationsChartElement) {
            try {
                const registrationsCtx = registrationsChartElement.getContext('2d');
                const registrationsChart = new Chart(registrationsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jun 1', 'Jun 5', 'Jun 10', 'Jun 15', 'Jun 20', 'Jun 25', 'Jun 30'],
                        datasets: [{
                            label: 'New Users',
                            data: [15, 8, 12, 20, 10, 17, 22],
                            backgroundColor: '#3a36d8',
                            borderColor: '#3a36d8',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing registrations chart:', error);
                registrationsChartElement.parentNode.innerHTML = '<div class="text-center py-4 text-muted">Chart data could not be displayed</div>';
            }
        }
        
        // User Roles Chart
        if (rolesChartElement) {
            try {
                const rolesCtx = rolesChartElement.getContext('2d');
                const rolesChart = new Chart(rolesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Applicants', 'Case Managers', 'Attorneys', 'Admins'],
                        datasets: [{
                            data: [65, 20, 10, 5],
                            backgroundColor: [
                                '#3a36d8', // Primary
                                '#4cc9f0', // Info
                                '#ff9e00', // Warning
                                '#e5383b'  // Danger
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    color: '#1a2436',
                                    font: { 
                                        size: 12,
                                        family: "'Inter', sans-serif",
                                        weight: 500
                                    },
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing roles chart:', error);
                rolesChartElement.parentNode.innerHTML = '<div class="text-center py-4 text-muted">Chart data could not be displayed</div>';
            }
        }
        
        // Application Status Chart
        if (statusChartElement) {
            try {
                const statusCtx = statusChartElement.getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Under Review', 'Needs More Information', 'Approved', 'Rejected'],
                        datasets: [{
                            label: 'Applications',
                            data: [45, 30, 15, 25, 10],
                            backgroundColor: [
                                '#ff9e00', // Warning
                                '#4cc9f0', // Info
                                '#a78bfa', // Purple
                                '#0cce6b', // Success
                                '#e5383b'  // Danger
                            ],
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { 
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error initializing status chart:', error);
                statusChartElement.parentNode.innerHTML = '<div class="text-center py-4 text-muted">Chart data could not be displayed</div>';
            }
        }
        
        // Report tab selection
        const reportTabs = document.querySelectorAll('.report-tab');
        reportTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                reportTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
@endsection
