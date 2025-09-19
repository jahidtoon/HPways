<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipping - Horizon Pathways</title>
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
      background: linear-gradient(135deg, #ff9e00 0%, #ff7300 100%);
      color: #fff;
      border-radius: 1rem;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 24px rgba(255, 158, 0, 0.15);
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
    
    /* Shipping status indicators */
    .shipment-status {
      display: flex;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .status-step {
      flex: 1;
      text-align: center;
      position: relative;
    }

    .status-step::before {
      content: "";
      height: 2px;
      width: 100%;
      background-color: #e2e8f0;
      position: absolute;
      top: 15px;
      left: 50%;
      z-index: 1;
    }

    .status-step:last-child::before {
      display: none;
    }

    .status-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background-color: #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.5rem;
      position: relative;
      z-index: 2;
    }

    .status-icon i {
      font-size: 0.875rem;
      color: #718096;
    }

    .status-label {
      font-size: 0.75rem;
      color: #718096;
      margin: 0;
    }

    .status-step.active .status-icon {
      background-color: var(--success);
    }

    .status-step.active .status-icon i {
      color: white;
    }

    .status-step.active .status-label {
      color: var(--success);
      font-weight: 600;
    }

    .status-step.active::before {
      background-color: var(--success);
    }

    .carrier-logo {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      margin-right: 1rem;
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
      <a href="{{ route('dashboard.printing.management') }}" class="sidebar-menu-item">
        <i class="fas fa-print"></i>
        <span>Print Management</span>
      </a>
      <a href="{{ route('dashboard.printing.shipping') }}" class="sidebar-menu-item active">
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
        <input type="text" placeholder="Search shipments...">
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
        <h1 class="welcome">Shipping Management</h1>
        <p class="subtitle">Track shipments, manage carriers, and process deliveries</p>
      </div>
      <div class="ms-auto">
        <button class="btn btn-light btn-lg">
          <i class="fas fa-plus-circle me-2"></i> Create Shipment
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
                  <i class="fas fa-box fa-2x text-primary"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Pending Shipments</h6>
                  <h3 class="mb-0">14</h3>
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
                  <i class="fas fa-shipping-fast fa-2x text-success"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">In Transit</h6>
                  <h3 class="mb-0">22</h3>
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
                  <i class="fas fa-check-circle fa-2x text-info"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Delivered Today</h6>
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
                <div class="flex-shrink-0 p-3 bg-danger bg-opacity-10 rounded me-3">
                  <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Delivery Issues</h6>
                  <h3 class="mb-0">2</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shipment Tracking -->
      <div class="row mb-4">
        <div class="col-xl-8 mb-4 mb-xl-0">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title">
                <i class="fas fa-truck text-warning"></i> Recent Shipments
              </h5>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-filter me-1"></i> Filter
                </button>
                <button class="btn btn-sm btn-outline-success">
                  <i class="fas fa-download me-1"></i> Export
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Tracking #</th>
                      <th>Recipient</th>
                      <th>Carrier</th>
                      <th>Status</th>
                      <th>Date Shipped</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <strong>TRK12345</strong>
                        <small class="d-block text-muted">APP-102</small>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">JS</div>
                          <div>
                            Jane Smith
                            <small class="d-block text-muted">Visa Card</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="carrier-logo bg-primary bg-opacity-10">
                            <i class="fas fa-truck text-primary"></i>
                          </div>
                          FedEx
                        </div>
                      </td>
                      <td><span class="badge bg-success">Delivered</span></td>
                      <td>Sep 5, 2025</td>
                      <td>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#trackingModal">
                          <i class="fas fa-search me-1"></i> Track
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <strong>TRK12346</strong>
                        <small class="d-block text-muted">APP-105</small>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">WB</div>
                          <div>
                            William Brown
                            <small class="d-block text-muted">Green Card</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="carrier-logo bg-info bg-opacity-10">
                            <i class="fas fa-truck text-info"></i>
                          </div>
                          USPS
                        </div>
                      </td>
                      <td><span class="badge bg-primary">In Transit</span></td>
                      <td>Sep 6, 2025</td>
                      <td>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#trackingModal">
                          <i class="fas fa-search me-1"></i> Track
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <strong>TRK12347</strong>
                        <small class="d-block text-muted">APP-106</small>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">MG</div>
                          <div>
                            Maria Garcia
                            <small class="d-block text-muted">Work Permit</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="carrier-logo bg-warning bg-opacity-10">
                            <i class="fas fa-truck text-warning"></i>
                          </div>
                          UPS
                        </div>
                      </td>
                      <td><span class="badge bg-info">Out for Delivery</span></td>
                      <td>Sep 6, 2025</td>
                      <td>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#trackingModal">
                          <i class="fas fa-search me-1"></i> Track
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <strong>TRK12348</strong>
                        <small class="d-block text-muted">APP-107</small>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">DJ</div>
                          <div>
                            David Johnson
                            <small class="d-block text-muted">I-765 Form</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="carrier-logo bg-danger bg-opacity-10">
                            <i class="fas fa-truck text-danger"></i>
                          </div>
                          DHL
                        </div>
                      </td>
                      <td><span class="badge bg-danger">Delayed</span></td>
                      <td>Sep 3, 2025</td>
                      <td>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#trackingModal">
                          <i class="fas fa-search me-1"></i> Track
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white text-center">
              <button class="btn btn-link text-primary">View All Shipments</button>
            </div>
          </div>
        </div>
        
        <!-- Ready to Ship -->
        <div class="col-xl-4">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-box-open text-primary"></i> Ready to Ship
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <h6 class="mb-0">John Doe</h6>
                      <small class="text-muted">APP-101 • Passport</small>
                    </div>
                    <span class="badge bg-primary">Ready</span>
                  </div>
                  <div class="d-grid">
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-shipping-fast me-1"></i> Create Shipment
                    </button>
                  </div>
                </div>
                <div class="list-group-item p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <h6 class="mb-0">Sarah Lee</h6>
                      <small class="text-muted">APP-108 • I-130 Form</small>
                    </div>
                    <span class="badge bg-primary">Ready</span>
                  </div>
                  <div class="d-grid">
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-shipping-fast me-1"></i> Create Shipment
                    </button>
                  </div>
                </div>
                <div class="list-group-item p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <h6 class="mb-0">Carlos Rodriguez</h6>
                      <small class="text-muted">APP-109 • Green Card</small>
                    </div>
                    <span class="badge bg-primary">Ready</span>
                  </div>
                  <div class="d-grid">
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-shipping-fast me-1"></i> Create Shipment
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white">
              <button class="btn btn-primary w-100">
                <i class="fas fa-shipping-fast me-1"></i> Bulk Ship
              </button>
            </div>
          </div>

          <!-- Carrier Selection -->
          <div class="card mt-4">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-truck-loading text-primary"></i> Carrier Status
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i class="fas fa-circle text-success small"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">FedEx</h6>
                      <small class="text-muted">API Connected</small>
                    </div>
                  </div>
                  <span class="badge bg-success">Online</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i class="fas fa-circle text-success small"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">UPS</h6>
                      <small class="text-muted">API Connected</small>
                    </div>
                  </div>
                  <span class="badge bg-success">Online</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i class="fas fa-circle text-warning small"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">USPS</h6>
                      <small class="text-muted">Experiencing Delays</small>
                    </div>
                  </div>
                  <span class="badge bg-warning">Delayed</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <i class="fas fa-circle text-danger small"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">DHL</h6>
                      <small class="text-muted">API Disconnected</small>
                    </div>
                  </div>
                  <span class="badge bg-danger">Offline</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Shipment Details -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title">
            <i class="fas fa-route text-info"></i> Detailed Tracking
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="mb-1">TRK12345 - Jane Smith</h5>
                <p class="mb-0 text-muted">FedEx • Shipped on Sep 5, 2025</p>
              </div>
              <span class="badge bg-success">Delivered</span>
            </div>
            <div class="shipment-status">
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-box"></i>
                </div>
                <p class="status-label">Processed</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-warehouse"></i>
                </div>
                <p class="status-label">Picked Up</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-truck"></i>
                </div>
                <p class="status-label">In Transit</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-truck-loading"></i>
                </div>
                <p class="status-label">Out for Delivery</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-check"></i>
                </div>
                <p class="status-label">Delivered</p>
              </div>
            </div>
          </div>
          
          <div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="mb-1">TRK12346 - William Brown</h5>
                <p class="mb-0 text-muted">USPS • Shipped on Sep 6, 2025</p>
              </div>
              <span class="badge bg-primary">In Transit</span>
            </div>
            <div class="shipment-status">
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-box"></i>
                </div>
                <p class="status-label">Processed</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-warehouse"></i>
                </div>
                <p class="status-label">Picked Up</p>
              </div>
              <div class="status-step active">
                <div class="status-icon">
                  <i class="fas fa-truck"></i>
                </div>
                <p class="status-label">In Transit</p>
              </div>
              <div class="status-step">
                <div class="status-icon">
                  <i class="fas fa-truck-loading"></i>
                </div>
                <p class="status-label">Out for Delivery</p>
              </div>
              <div class="status-step">
                <div class="status-icon">
                  <i class="fas fa-check"></i>
                </div>
                <p class="status-label">Delivered</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Tracking Modal -->
  <div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="trackingModalLabel">Shipment Tracking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center mb-3">
            <div class="me-3 p-2 rounded bg-light">
              <i class="fas fa-shipping-fast text-primary fa-2x"></i>
            </div>
            <div>
              <h6 class="mb-0" id="tracking-carrier">FedEx</h6>
              <div class="d-flex align-items-center">
                <span class="text-muted me-2">Tracking Number:</span>
                <strong id="tracking-number">TRK12345</strong>
              </div>
            </div>
          </div>
          
          <!-- Tracking Timeline -->
          <div class="position-relative px-3 py-2">
            <div class="position-absolute h-100" style="width: 2px; background-color: #e9ecef; left: 24px; top: 0;"></div>
            
            <div class="d-flex mb-4 position-relative">
              <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3 position-relative" style="width: 32px; height: 32px; z-index: 1;">
                <i class="fas fa-check"></i>
              </div>
              <div class="border rounded p-3 shadow-sm w-100">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-1">Delivered</h6>
                  <span class="text-muted small">Today, 10:34 AM</span>
                </div>
                <p class="mb-0 text-muted small">Package delivered to recipient</p>
              </div>
            </div>
            
            <div class="d-flex mb-4 position-relative">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 position-relative" style="width: 32px; height: 32px; z-index: 1;">
                <i class="fas fa-truck"></i>
              </div>
              <div class="border rounded p-3 shadow-sm w-100">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-1">Out for Delivery</h6>
                  <span class="text-muted small">Today, 8:15 AM</span>
                </div>
                <p class="mb-0 text-muted small">Package is out for delivery</p>
              </div>
            </div>
            
            <div class="d-flex mb-4 position-relative">
              <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3 position-relative" style="width: 32px; height: 32px; z-index: 1;">
                <i class="fas fa-warehouse"></i>
              </div>
              <div class="border rounded p-3 shadow-sm w-100">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-1">Arrived at Facility</h6>
                  <span class="text-muted small">Yesterday, 7:23 PM</span>
                </div>
                <p class="mb-0 text-muted small">Package arrived at local facility</p>
              </div>
            </div>
            
            <div class="d-flex position-relative">
              <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3 position-relative" style="width: 32px; height: 32px; z-index: 1;">
                <i class="fas fa-box"></i>
              </div>
              <div class="border rounded p-3 shadow-sm w-100">
                <div class="d-flex justify-content-between">
                  <h6 class="mb-1">Shipped</h6>
                  <span class="text-muted small">2 days ago</span>
                </div>
                <p class="mb-0 text-muted small">Package shipped from origin facility</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">
            <i class="fas fa-print me-1"></i> Print Label
          </button>
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

      // Tracking modal data
      const trackingBtns = document.querySelectorAll('[data-bs-target="#trackingModal"]');
      trackingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          // In a real application, this would fetch data from the server
          // For now, we're just showing a static modal
        });
      });
    });
  </script>
  
  <!-- Logout Form -->
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
  </form>
</body>
</html>
