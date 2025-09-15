<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Printing Department Dashboard</title>
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
    
    .printing-hero {
      background: linear-gradient(135deg, #36b37e 0%, #00875a 100%);
      color: #fff;
      border-radius: 1rem;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 24px rgba(0, 135, 90, 0.15);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    
    .printing-hero::before {
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
    
    .printing-hero .icon {
      font-size: 3rem;
      color: #fff;
      opacity: 0.9;
      background: rgba(255, 255, 255, 0.2);
      height: 80px;
      width: 80px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .printing-hero .welcome {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      letter-spacing: 0.5px;
    }
    
    .printing-hero .subtitle {
      font-size: 1rem;
      opacity: 0.9;
      font-weight: 400;
      max-width: 600px;
    }
    
    .stat-card {
      background: white;
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      padding: 1.25rem;
      height: 100%;
      transition: all 0.3s ease;
      border: 1px solid rgba(226, 232, 240, 0.8);
      position: relative;
      overflow: hidden;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
    }
    
    .stat-card.primary::before {
      background: var(--primary);
    }
    
    .stat-card.success::before {
      background: var(--success);
    }
    
    .stat-card.info::before {
      background: var(--info);
    }
    
    .stat-card.warning::before {
      background: var(--warning);
    }
    
    .stat-card-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .stat-card-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
    }
    
    .stat-card.primary .stat-card-icon {
      background: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }
    
    .stat-card.success .stat-card-icon {
      background: rgba(12, 206, 107, 0.1);
      color: var(--success);
    }
    
    .stat-card.info .stat-card-icon {
      background: rgba(76, 201, 240, 0.1);
      color: var(--info);
    }
    
    .stat-card.warning .stat-card-icon {
      background: rgba(255, 158, 0, 0.1);
      color: var(--warning);
    }
    
    .stat-card-label {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #718096;
      margin-bottom: 0.25rem;
    }
    
    .stat-card-value {
      font-size: 1.75rem;
      font-weight: 700;
      color: #2d3748;
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
    
    .table-hover tbody tr:hover {
      background: #f7fafc;
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
    
    .btn-primary {
      background: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      border-color: var(--secondary);
    }
    
    .btn-success {
      background: var(--success);
      border-color: var(--success);
    }
    
    .btn-outline-primary {
      border-color: var(--primary);
      color: var(--primary);
    }
    
    .btn-outline-primary:hover {
      background: var(--primary);
      color: white;
    }
    
    .btn-outline-success {
      border-color: var(--success);
      color: var(--success);
    }
    
    .btn-outline-success:hover {
      background: var(--success);
      color: white;
    }
    
    .btn-outline-info {
      border-color: var(--info);
      color: var(--info);
    }
    
    .btn-outline-info:hover {
      background: var(--info);
      color: white;
    }
    
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
      <a href="{{ route('dashboard.printing.index') }}" class="sidebar-menu-item active">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
      </a>
      <a href="{{ route('dashboard.printing.management') }}" class="sidebar-menu-item">
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
      <a href="#" class="sidebar-menu-item">
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
        <input type="text" placeholder="Search...">
      </div>
      
      <div class="user-dropdown">
        <div class="user-avatar">AD</div>
        <div class="user-info">
          <div class="user-name">Admin Dept</div>
          <div class="user-role">Print Manager</div>
        </div>
      </div>
    </div>
    
    <!-- Hero Section -->
    <div class="printing-hero mb-4">
      <div class="icon">
        <i class="fas fa-print"></i>
      </div>
      <div>
        <h2 class="welcome">Printing Department Dashboard</h2>
        <p class="subtitle">Manage document printing and shipping operations efficiently</p>
      </div>
    </div>
    
    <div class="container-fluid p-0">
      <!-- Stats Row -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stat-card primary h-100">
            <div class="stat-card-content">
              <div>
                <div class="stat-card-label">In Print Queue</div>
                <div class="stat-card-value">24</div>
              </div>
              <div class="stat-card-icon">
                <i class="fas fa-print"></i>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stat-card success h-100">
            <div class="stat-card-content">
              <div>
                <div class="stat-card-label">Ready to Ship</div>
                <div class="stat-card-value">16</div>
              </div>
              <div class="stat-card-icon">
                <i class="fas fa-box"></i>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stat-card info h-100">
            <div class="stat-card-content">
              <div>
                <div class="stat-card-label">Shipped Today</div>
                <div class="stat-card-value">12</div>
              </div>
              <div class="stat-card-icon">
                <i class="fas fa-shipping-fast"></i>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stat-card warning h-100">
            <div class="stat-card-content">
              <div>
                <div class="stat-card-label">Delivery Issues</div>
                <div class="stat-card-value">2</div>
              </div>
              <div class="stat-card-icon">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

        <!-- Main Table Card -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title"><i class="fas fa-shipping-fast text-success"></i> Shipping & Print Tracking</h5>
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#printBatchModal">
              <i class="fas fa-print"></i> Print Batch
            </button>
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#bulkShipModal">
              <i class="fas fa-truck"></i> Bulk Ship
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Application ID</th>
                  <th>Applicant</th>
                  <th>Document</th>
                  <th>Print Status</th>
                  <th>Shipping Status</th>
                  <th>Tracking #</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>APP-101</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">JD</div>
                      John Doe
                    </div>
                  </td>
                  <td>Passport</td>
                  <td><span class="badge bg-info">In Queue</span></td>
                  <td><span class="badge bg-warning">Awaiting Shipment</span></td>
                  <td>-</td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-print"></i> Mark Printed
                    </button>
                  </td>
                </tr>
                <tr>
                  <td><strong>APP-102</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">JS</div>
                      Jane Smith
                    </div>
                  </td>
                  <td>Visa Card</td>
                  <td><span class="badge bg-success">Printed</span></td>
                  <td><span class="badge bg-success">Shipped</span></td>
                  <td><a href="#" class="tracking-link" data-tracking="TRK12345" data-carrier="FedEx">TRK12345</a></td>
                  <td>
                    <button class="btn btn-sm btn-outline-info tracking-btn" data-tracking="TRK12345" data-carrier="FedEx">
                      <i class="fas fa-search"></i> Track
                    </button>
                  </td>
                </tr>
                <tr>
                  <td><strong>APP-103</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">RJ</div>
                      Robert Johnson
                    </div>
                  </td>
                  <td>I-485 Form</td>
                  <td><span class="badge bg-info">In Queue</span></td>
                  <td><span class="badge bg-secondary">Not Ready</span></td>
                  <td>-</td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-print"></i> Mark Printed
                    </button>
                  </td>
                </tr>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">No more applications available.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    
      <!-- Analytics Section -->
      <div class="row mb-4">
        <!-- Carrier Status -->
        <div class="col-lg-6 mb-4 mb-lg-0">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0"><i class="fas fa-truck-loading text-primary me-2"></i>Carrier Status</h5>
            </div>
            <div class="card-body">
              <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3 position-relative">
                      <span class="carrier-status-indicator online"></span>
                    </div>
                    <div>
                      <h6 class="mb-0">FedEx</h6>
                      <small class="text-muted">API connected, accepting shipments</small>
                    </div>
                  </div>
                  <span class="badge bg-success rounded-pill">Online</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3 position-relative">
                      <span class="carrier-status-indicator online"></span>
                    </div>
                    <div>
                      <h6 class="mb-0">UPS</h6>
                      <small class="text-muted">API connected, accepting shipments</small>
                    </div>
                  </div>
                  <span class="badge bg-success rounded-pill">Online</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3 position-relative">
                      <span class="carrier-status-indicator delayed"></span>
                    </div>
                    <div>
                      <h6 class="mb-0">USPS</h6>
                      <small class="text-muted">API experiencing delays</small>
                    </div>
                  </div>
                  <span class="badge bg-warning rounded-pill">Delayed</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                  <div class="d-flex align-items-center">
                    <div class="me-3 position-relative">
                      <span class="carrier-status-indicator offline"></span>
                    </div>
                    <div>
                      <h6 class="mb-0">DHL</h6>
                      <small class="text-muted">API disconnected</small>
                    </div>
                  </div>
                  <span class="badge bg-danger rounded-pill">Offline</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Shipping Analytics -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
              <h5 class="card-title mb-0"><i class="fas fa-chart-line text-info me-2"></i>Shipping Analytics</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6 mb-4">
                  <div class="text-center p-3 rounded bg-light">
                    <h6 class="text-muted mb-1">Average Processing Time</h6>
                    <h2 class="mb-0 text-primary">1.2 <small class="text-muted fs-6">days</small></h2>
                  </div>
                </div>
                <div class="col-6 mb-4">
                  <div class="text-center p-3 rounded bg-light">
                    <h6 class="text-muted mb-1">Delivery Success Rate</h6>
                    <h2 class="mb-0 text-success">98.7<small class="text-muted fs-6">%</small></h2>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-3 rounded bg-light">
                    <h6 class="text-muted mb-1">Carrier Distribution</h6>
                    <div class="d-flex justify-content-around mt-2">
                      <div class="text-primary"><i class="fas fa-circle me-1"></i>45%</div>
                      <div class="text-success"><i class="fas fa-circle me-1"></i>30%</div>
                      <div class="text-warning"><i class="fas fa-circle me-1"></i>25%</div>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-3 rounded bg-light">
                    <h6 class="text-muted mb-1">Print Queue Health</h6>
                    <h2 class="mb-0 text-info">Good <i class="fas fa-check-circle fs-5"></i></h2>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- End of container-fluid -->

  <!-- Modals -->
  <!-- Print Batch Modal -->
  <div class="modal fade" id="printBatchModal" tabindex="-1" aria-labelledby="printBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="printBatchModalLabel">Print Batch</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Progress Steps -->
          <div class="d-flex justify-content-between mb-4">
            <div class="text-center">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">1</div>
              <div class="small">Select Documents</div>
            </div>
            <div class="progress align-self-center" style="width: 20%; height: 2px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="text-center">
              <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">2</div>
              <div class="small">Configure</div>
            </div>
            <div class="progress align-self-center" style="width: 20%; height: 2px;">
              <div class="progress-bar bg-light" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="text-center">
              <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">3</div>
              <div class="small">Print</div>
            </div>
          </div>
          
          <!-- Document Selection -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">Select Documents to Print</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllDocs">
                <label class="form-check-label" for="selectAllDocs">Select All</label>
              </div>
            </div>
            <div class="list-group">
              <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <input class="form-check-input me-3" type="checkbox">
                  <div>
                    <strong>APP-101: Passport</strong>
                    <div class="small text-muted">John Doe</div>
                  </div>
                </div>
                <span class="badge bg-info rounded-pill">Pending</span>
              </label>
              <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <input class="form-check-input me-3" type="checkbox">
                  <div>
                    <strong>APP-103: I-485 Form</strong>
                    <div class="small text-muted">Robert Johnson</div>
                  </div>
                </div>
                <span class="badge bg-info rounded-pill">Pending</span>
              </label>
              <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <input class="form-check-input me-3" type="checkbox">
                  <div>
                    <strong>APP-104: Work Permit</strong>
                    <div class="small text-muted">Maria Garcia</div>
                  </div>
                </div>
                <span class="badge bg-info rounded-pill">Pending</span>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-1"></i></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bulk Ship Modal -->
  <div class="modal fade" id="bulkShipModal" tabindex="-1" aria-labelledby="bulkShipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bulkShipModalLabel">Bulk Ship Documents</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Progress Steps -->
          <div class="d-flex justify-content-between mb-4">
            <div class="text-center">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">1</div>
              <div class="small">Select Documents</div>
            </div>
            <div class="progress align-self-center" style="width: 15%; height: 2px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="text-center">
              <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">2</div>
              <div class="small">Carrier</div>
            </div>
            <div class="progress align-self-center" style="width: 15%; height: 2px;">
              <div class="progress-bar bg-light" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="text-center">
              <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">3</div>
              <div class="small">Address</div>
            </div>
            <div class="progress align-self-center" style="width: 15%; height: 2px;">
              <div class="progress-bar bg-light" role="progressbar" style="width: 100%"></div>
            </div>
            <div class="text-center">
              <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 40px; height: 40px;">4</div>
              <div class="small">Ship</div>
            </div>
          </div>
          
          <!-- Document Selection -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0">Select Printed Documents to Ship</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllShipDocs">
                <label class="form-check-label" for="selectAllShipDocs">Select All</label>
              </div>
            </div>
            <div class="list-group">
              <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <input class="form-check-input me-3" type="checkbox">
                  <div>
                    <strong>APP-102: Visa Card</strong>
                    <div class="small text-muted">Jane Smith</div>
                  </div>
                </div>
                <span class="badge bg-success rounded-pill">Printed</span>
              </label>
              <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                  <input class="form-check-input me-3" type="checkbox">
                  <div>
                    <strong>APP-105: Green Card</strong>
                    <div class="small text-muted">William Brown</div>
                  </div>
                </div>
                <span class="badge bg-success rounded-pill">Printed</span>
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Next <i class="fas fa-arrow-right ms-1"></i></button>
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
        </div>
      </div>
    </div>
  </div>

  <style>
    .hover-lift {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
    }
    
    .carrier-status-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }
    
    .carrier-status-indicator.online {
      background-color: var(--success);
      box-shadow: 0 0 0 rgba(12, 206, 107, 0.4);
      animation: pulse 2s infinite;
    }
    
    .carrier-status-indicator.delayed {
      background-color: var(--warning);
      box-shadow: 0 0 0 rgba(255, 158, 0, 0.4);
      animation: pulse 2s infinite;
    }
    
    .carrier-status-indicator.offline {
      background-color: var(--danger);
    }
    
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(12, 206, 107, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(12, 206, 107, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(12, 206, 107, 0);
      }
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Initialize all tooltips
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile sidebar toggle
      const mobileToggle = document.querySelector('.mobile-toggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
      
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      });
      
      // Tracking button click handler
      const trackingBtns = document.querySelectorAll('.tracking-btn');
      trackingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          const trackingNumber = this.dataset.tracking;
          const carrier = this.dataset.carrier;
          
          document.getElementById('tracking-number').textContent = trackingNumber;
          document.getElementById('tracking-carrier').textContent = carrier;
          
          var trackingModal = new bootstrap.Modal(document.getElementById('trackingModal'));
          trackingModal.show();
        });
      });
      
      // Select all checkbox handlers
      const selectAllDocs = document.getElementById('selectAllDocs');
      if (selectAllDocs) {
        selectAllDocs.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('#printBatchModal .list-group-item input[type="checkbox"]');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
        });
      }
      
      const selectAllShipDocs = document.getElementById('selectAllShipDocs');
      if (selectAllShipDocs) {
        selectAllShipDocs.addEventListener('change', function() {
          const checkboxes = document.querySelectorAll('#bulkShipModal .list-group-item input[type="checkbox"]');
          checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
          });
        });
      }
    });
  </script>
</body>
</html>
