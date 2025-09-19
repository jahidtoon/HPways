<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Print Management - Horizon Pathways</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #4895ef;
      --secondary: #3f37c9;
      --success: #0cce6b;
      --danger: #e5383b;
      --warning: #ff9e00;
      --info: #4cc9f0;
      --light-bg: #f5f7fa;
      --dark: #212b36;
    }
    
    body {
      background: var(--light-bg);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #333;
      min-height: 100vh;
      padding: 0;
      overflow-x: hidden;
    }
    
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 260px;
      background: var(--dark);
      color: white;
      z-index: 1000;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    
    .sidebar-brand {
      padding: 1.5rem;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-brand h3 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
    }
    
    .sidebar-brand img {
      width: 36px;
      margin-right: 0.75rem;
    }
    
    .sidebar-menu {
      padding: 1rem 0;
    }
    
    .sidebar-menu-header {
      padding: 0.75rem 1.5rem;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.6);
      font-weight: 600;
    }
    
    .sidebar-menu-item {
      padding: 0.75rem 1.5rem;
      display: flex;
      align-items: center;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }
    
    .sidebar-menu-item i {
      margin-right: 0.75rem;
      font-size: 1rem;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-menu-item:hover, .sidebar-menu-item.active {
      color: white;
      background: rgba(255, 255, 255, 0.08);
      border-left-color: var(--primary-light);
    }
    
    .sidebar-menu-item.active {
      color: white;
      background: rgba(255, 255, 255, 0.08);
      border-left-color: var(--primary-light);
    }
    
    .main-content {
      margin-left: 260px;
      padding: 2rem;
      transition: all 0.3s ease;
    }
    
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    
    .search-box {
      position: relative;
      width: 280px;
    }
    
    .search-box input {
      padding: 0.5rem 1rem 0.5rem 2.25rem;
      border-radius: 0.75rem;
      border: 1px solid #e2e8f0;
      width: 100%;
      font-size: 0.875rem;
      transition: all 0.2s ease;
    }
    
    .search-box i {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
    }
    
    .search-box input:focus {
      outline: none;
      border-color: var(--primary-light);
      box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.15);
    }
    
    .user-dropdown {
      display: flex;
      align-items: center;
      cursor: pointer;
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      margin-right: 0.75rem;
    }
    
    .user-info {
      display: flex;
      flex-direction: column;
    }
    
    .user-name {
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    .user-role {
      font-size: 0.75rem;
      color: #718096;
    }
    
    .page-header {
      background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
      color: #fff;
      border-radius: 1rem;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 24px rgba(67, 97, 238, 0.15);
      position: relative;
      overflow: hidden;
    }
    
    .page-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 200%;
      background: rgba(255, 255, 255, 0.1);
      transform: rotate(30deg);
      pointer-events: none;
    }
    
    .page-header .welcome {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      letter-spacing: 0.5px;
    }
    
    .page-header .subtitle {
      font-size: 1rem;
      opacity: 0.9;
      font-weight: 400;
      max-width: 600px;
    }
    
    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 2rem;
    }
    
    .card-header {
      background: white;
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
      padding: 1.25rem 1.5rem;
    }
    
    .card-title {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: #2d3748;
      display: flex;
      align-items: center;
    }
    
    .card-title i {
      margin-right: 0.75rem;
      font-size: 1.25rem;
    }
    
    .badge {
      padding: 0.35rem 0.75rem;
      font-weight: 600;
      font-size: 0.75rem;
      border-radius: 0.5rem;
    }
    
    .btn {
      border-radius: 0.5rem;
      font-weight: 500;
      font-size: 0.875rem;
      padding: 0.5rem 1rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      gap: 0.25rem;
    }
    
    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.75rem;
      border-radius: 0.375rem;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table th {
      padding: 1rem 1.5rem;
      font-weight: 600;
      font-size: 0.875rem;
      color: #4a5568;
      background: #f7fafc;
      border: none;
      white-space: nowrap;
    }
    
    .table td {
      padding: 1rem 1.5rem;
      vertical-align: middle;
      border-top: 1px solid #f0f4f8;
      font-size: 0.875rem;
    }

    /* Responsive styles */
    @media (max-width: 991.98px) {
      .sidebar {
        width: 80px;
      }
      
      .sidebar-brand h3,
      .sidebar-menu-item span {
        display: none;
      }
      
      .sidebar-brand {
        justify-content: center;
      }
      
      .sidebar-brand img {
        margin-right: 0;
      }
      
      .sidebar-menu-item {
        justify-content: center;
        padding: 0.75rem;
      }
      
      .sidebar-menu-item i {
        margin-right: 0;
      }
      
      .main-content {
        margin-left: 80px;
      }
      
      .search-box {
        width: 200px;
      }
    }
    
    @media (max-width: 767.98px) {
      .sidebar {
        transform: translateX(-100%);
      }
      
      .main-content {
        margin-left: 0;
      }
      
      .sidebar.show {
        transform: translateX(0);
      }
      
      .mobile-toggle {
        display: block;
      }
      
      .topbar {
        flex-wrap: wrap;
        gap: 1rem;
      }
      
      .search-box {
        width: 100%;
        order: 3;
      }
    }
    
    /* Print Queue styles */
    .print-job-card {
      border-left: 4px solid var(--primary);
      transition: all 0.2s ease;
    }
    
    .print-job-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .print-job-card.high-priority {
      border-left-color: var(--danger);
    }
    
    .print-job-card.medium-priority {
      border-left-color: var(--warning);
    }
    
    .print-job-card.low-priority {
      border-left-color: var(--success);
    }
    
    .priority-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    
    .priority-high {
      background-color: var(--danger);
    }
    
    .priority-medium {
      background-color: var(--warning);
    }
    
    .priority-low {
      background-color: var(--success);
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <div class="sidebar-brand">
      <img src="https://via.placeholder.com/36x36" alt="Logo">
      <h3>Horizon Pathways</h3>
    </div>
    
    <div class="sidebar-menu">
      <div class="sidebar-menu-header">Main</div>
      <a href="{{ route('dashboard.printing.index') }}" class="sidebar-menu-item">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('dashboard.printing.management') }}" class="sidebar-menu-item active">
        <i class="fas fa-print"></i>
        <span>Print Management</span>
      </a>
      <a href="{{ route('dashboard.printing.shipping') }}" class="sidebar-menu-item">
        <i class="fas fa-shipping-fast"></i>
        <span>Shipping</span>
      </a>
      <a href="{{ route('dashboard.printing.documents') }}" class="sidebar-menu-item">
        <i class="fas fa-file-alt"></i>
        <span>Documents</span>
      </a>
      
      <div class="sidebar-menu-header">Reports</div>
      <a href="{{ route('dashboard.printing.analytics') }}" class="sidebar-menu-item">
        <i class="fas fa-chart-line"></i>
        <span>Analytics</span>
      </a>
      <a href="#" class="sidebar-menu-item">
        <i class="fas fa-history"></i>
        <span>History</span>
      </a>
      
      <div class="sidebar-menu-header">Settings</div>
      <a href="#" class="sidebar-menu-item">
        <i class="fas fa-cog"></i>
        <span>General</span>
      </a>
      <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-menu-item">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="main-content">
    <div class="topbar">
      <button class="btn btn-sm btn-light d-md-none mobile-toggle">
        <i class="fas fa-bars"></i>
      </button>
      
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search print jobs...">
      </div>
      
      <div class="user-dropdown">
        <div class="user-avatar">AD</div>
        <div class="user-info">
          <div class="user-name">Admin Dept</div>
          <div class="user-role">Print Manager</div>
        </div>
      </div>
    </div>
    
    <!-- Page Header -->
    <div class="page-header d-flex align-items-center">
      <div>
        <h1 class="welcome">Print Management</h1>
        <p class="subtitle">Manage print jobs, queues, and printer configurations</p>
      </div>
      <div class="ms-auto">
        <button class="btn btn-light btn-lg">
          <i class="fas fa-plus-circle me-2"></i> New Print Job
        </button>
      </div>
    </div>
    
    <div class="container-fluid p-0">
      <!-- Stats Row -->
      <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 p-3 bg-primary bg-opacity-10 rounded me-3">
                  <i class="fas fa-print fa-2x text-primary"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Print Queue</h6>
                  <h3 class="mb-0">24</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 p-3 bg-danger bg-opacity-10 rounded me-3">
                  <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">High Priority</h6>
                  <h3 class="mb-0">8</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 p-3 bg-success bg-opacity-10 rounded me-3">
                  <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Printed Today</h6>
                  <h3 class="mb-0">42</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 p-3 bg-info bg-opacity-10 rounded me-3">
                  <i class="fas fa-tint fa-2x text-info"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Ink Levels</h6>
                  <h3 class="mb-0">78%</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Print Queue -->
      <div class="row mb-4">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title">
                <i class="fas fa-tasks text-primary"></i> Print Queue
              </h5>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-sort-amount-down me-1"></i> Sort
                </button>
                <button class="btn btn-sm btn-outline-secondary">
                  <i class="fas fa-filter me-1"></i> Filter
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item p-3 print-job-card high-priority">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">
                        <span class="priority-indicator priority-high"></span>
                        #PJ-1001: Passport Documents
                      </h6>
                      <p class="mb-1 text-muted small">John Doe • 5 pages • Color</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-danger me-3">High Priority</span>
                      <div>
                        <button class="btn btn-sm btn-primary me-1">
                          <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-sm btn-light">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 print-job-card medium-priority">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">
                        <span class="priority-indicator priority-medium"></span>
                        #PJ-1002: Visa Application
                      </h6>
                      <p class="mb-1 text-muted small">Jane Smith • 12 pages • B&W</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-warning me-3">Medium Priority</span>
                      <div>
                        <button class="btn btn-sm btn-primary me-1">
                          <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-sm btn-light">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 print-job-card low-priority">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">
                        <span class="priority-indicator priority-low"></span>
                        #PJ-1003: I-485 Form
                      </h6>
                      <p class="mb-1 text-muted small">Robert Johnson • 8 pages • B&W</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-success me-3">Low Priority</span>
                      <div>
                        <button class="btn btn-sm btn-primary me-1">
                          <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-sm btn-light">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 print-job-card high-priority">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">
                        <span class="priority-indicator priority-high"></span>
                        #PJ-1004: Work Permit
                      </h6>
                      <p class="mb-1 text-muted small">Maria Garcia • 3 pages • Color</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-danger me-3">High Priority</span>
                      <div>
                        <button class="btn btn-sm btn-primary me-1">
                          <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-sm btn-light">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 print-job-card medium-priority">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="mb-1">
                        <span class="priority-indicator priority-medium"></span>
                        #PJ-1005: Green Card
                      </h6>
                      <p class="mb-1 text-muted small">William Brown • 6 pages • Color</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-warning me-3">Medium Priority</span>
                      <div>
                        <button class="btn btn-sm btn-primary me-1">
                          <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button class="btn btn-sm btn-light">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white text-center">
              <button class="btn btn-link text-primary">View All Print Jobs</button>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <!-- Printer Status -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-print text-primary"></i> Printer Status
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <div class="me-2 bg-success rounded-circle" style="width: 10px; height: 10px;"></div>
                    <h6 class="mb-0">HP LaserJet Pro MFP</h6>
                  </div>
                  <span class="badge bg-success">Online</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block mb-1">Toner Level</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="mb-0">
                  <small class="text-muted d-block mb-1">Paper Tray</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <div class="me-2 bg-warning rounded-circle" style="width: 10px; height: 10px;"></div>
                    <h6 class="mb-0">Canon PIXMA Color</h6>
                  </div>
                  <span class="badge bg-warning">Low Ink</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block mb-1">Ink Level</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="mb-0">
                  <small class="text-muted d-block mb-1">Paper Tray</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
              
              <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <div class="me-2 bg-danger rounded-circle" style="width: 10px; height: 10px;"></div>
                    <h6 class="mb-0">Xerox WorkCentre</h6>
                  </div>
                  <span class="badge bg-danger">Offline</span>
                </div>
                <div class="mb-2">
                  <small class="text-muted d-block mb-1">Toner Level</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="mb-0">
                  <small class="text-muted d-block mb-1">Paper Tray</small>
                  <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white">
              <button class="btn btn-sm btn-primary w-100">
                <i class="fas fa-sync-alt me-1"></i> Refresh Status
              </button>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-history text-primary"></i> Recent Activity
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item p-3">
                  <p class="mb-1 fw-medium">Printed passport documents</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">John Doe • APP-101</small>
                    <small class="text-muted">10 min ago</small>
                  </div>
                </div>
                <div class="list-group-item p-3">
                  <p class="mb-1 fw-medium">Added 3 jobs to print queue</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Admin • System</small>
                    <small class="text-muted">25 min ago</small>
                  </div>
                </div>
                <div class="list-group-item p-3">
                  <p class="mb-1 fw-medium">Changed printer settings</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Admin • System</small>
                    <small class="text-muted">1 hour ago</small>
                  </div>
                </div>
                <div class="list-group-item p-3">
                  <p class="mb-1 fw-medium">Printed visa documents</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Jane Smith • APP-102</small>
                    <small class="text-muted">2 hours ago</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile sidebar toggle
      const mobileToggle = document.querySelector('.mobile-toggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
    });
  </script>
  
  <!-- Logout Form -->
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</body>
</html>
