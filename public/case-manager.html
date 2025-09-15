<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Case Manager Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      --light-bg: #f8fafc;
      --dark: #212b36;
      --card-border-radius: 1rem;
      --transition-speed: 0.3s;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--dark);
      min-height: 100vh;
      padding: 0;
      overflow-x: hidden;
    }
    
    /* Utility classes */
    .d-flex {
      display: flex;
      align-items: center;
    }
    
    .justify-between {
      justify-content: space-between;
    }
    
    .flex-column {
      flex-direction: column;
    }
    
    .me-2 {
      margin-right: 0.5rem;
    }
    
    .me-3 {
      margin-right: 1rem;
    }
    
    .mb-0 {
      margin-bottom: 0;
    }
    
    .mb-2 {
      margin-bottom: 0.5rem;
    }
    
    .mb-3 {
      margin-bottom: 1rem;
    }
    
    .mb-4 {
      margin-bottom: 1.5rem;
    }
    
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: var(--card-border-radius);
      background: white;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .container {
      width: 100%;
      max-width: 1320px;
      margin: 0 auto;
      padding: 2rem;
    }
    
    /* Layout components */
    .app-wrapper {
      display: flex;
      width: 100%;
      min-height: 100vh;
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
      transition: all var(--transition-speed) ease;
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
      color: white;
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
    
    .main-content {
      flex: 1;
      margin-left: 260px;
      padding: 2rem;
      transition: all var(--transition-speed) ease;
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
    /* Hero Section */
    .cm-hero {
      background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
      color: #fff;
      border-radius: var(--card-border-radius);
      padding: 2.5rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 32px rgba(67, 97, 238, 0.15);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    
    .cm-hero::before {
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
    
    .cm-hero .icon {
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
    
    .cm-hero .welcome {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      letter-spacing: 0.5px;
    }
    
    .cm-hero .subtitle {
      font-size: 1rem;
      opacity: 0.9;
      font-weight: 400;
      max-width: 600px;
    }
    
    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background: white;
      border-radius: var(--card-border-radius);
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
    /* Cards and Tables */
    .card {
      border: none;
      border-radius: var(--card-border-radius);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      margin-bottom: 2rem;
      background: white;
    }
    
    .card-header {
      background: white;
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
      padding: 1.25rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
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
      color: var(--primary);
    }
    
    .card-body {
      padding: 0;
    }
    
    .table {
      width: 100%;
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
    
    /* Badges and Buttons */
    .badge {
      padding: 0.35rem 0.75rem;
      font-weight: 600;
      font-size: 0.75rem;
      border-radius: 0.5rem;
    }
    
    .bg-success { 
      background-color: rgba(12, 206, 107, 0.1);
      color: var(--success);
    }
    
    .bg-warning { 
      background-color: rgba(255, 158, 0, 0.1);
      color: var(--warning);
    }
    
    .bg-danger { 
      background-color: rgba(229, 56, 59, 0.1);
      color: var(--danger);
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
      border: none;
      cursor: pointer;
    }
    
    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.75rem;
      border-radius: 0.375rem;
    }
    
    .btn-group {
      display: flex;
      gap: 0.5rem;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
    }
    
    .btn-primary:hover {
      background: var(--secondary);
    }
    
    .btn-outline-primary {
      border: 1px solid var(--primary);
      color: var(--primary);
      background: transparent;
    }
    
    .btn-outline-primary:hover {
      background: var(--primary);
      color: white;
    }
    
    .btn-outline-success {
      border: 1px solid var(--success);
      color: var(--success);
      background: transparent;
    }
    
    .btn-outline-success:hover {
      background: var(--success);
      color: white;
    }
    
    .btn-outline-warning {
      border: 1px solid var(--warning);
      color: var(--warning);
      background: transparent;
    }
    
    .btn-outline-warning:hover {
      background: var(--warning);
      color: white;
    }
    
    .btn-outline-danger {
      border: 1px solid var(--danger);
      color: var(--danger);
      background: transparent;
    }
    
    .btn-outline-danger:hover {
      background: var(--danger);
      color: white;
    }
    /* Responsive Styles */
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
      
      .dashboard-cards {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
      
      .cm-hero {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
      }
      
      .cm-hero .icon {
        margin-bottom: 1rem;
      }
      
      .dashboard-cards {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="app-wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="sidebar-brand">
        <img src="https://via.placeholder.com/36x36" alt="Logo">
        <h3>Horizon Pathways</h3>
      </div>
      
        <div class="sidebar-menu">
        <div class="sidebar-menu-header">Main</div>
        <a href="/case-manager.html" class="sidebar-menu-item active">
          <i class="fas fa-th-large"></i>
          <span>Dashboard</span>
        </a>
        <a href="/case-manager/case-details.html" class="sidebar-menu-item">
          <i class="fas fa-briefcase"></i>
          <span>Case Management</span>
        </a>
        <a href="/case-manager/documents.html" class="sidebar-menu-item">
          <i class="fas fa-file-alt"></i>
          <span>Documents</span>
        </a>
        <a href="/case-manager/applicants.html" class="sidebar-menu-item">
          <i class="fas fa-users"></i>
          <span>Applicants</span>
        </a>
        
        <div class="sidebar-menu-header">Reports</div>
        <a href="/case-manager/analytics.html" class="sidebar-menu-item">
          <i class="fas fa-chart-bar"></i>
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
          <input type="text" placeholder="Search cases...">
        </div>
        
        <div class="user-dropdown">
          <div class="user-avatar">CM</div>
          <div class="user-info">
            <div class="user-name">Case Manager</div>
            <div class="user-role">Admin</div>
          </div>
        </div>
      </div>

      <!-- Hero Section -->
      <div class="cm-hero mb-4">
        <div class="icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <div>
          <h2 class="welcome">Welcome, <span class="fw-bold">Case Manager</span>!</h2>
          <p class="subtitle">Monitor your assigned cases, track document status, and communicate with applicants.</p>
        </div>
      </div>
      
      <!-- Stats Cards -->
      <div class="dashboard-cards">
        <div class="stat-card primary">
          <div class="stat-card-content">
            <div>
              <div class="stat-card-label">Total Cases</div>
              <div class="stat-card-value">5</div>
            </div>
            <div class="stat-card-icon">
              <i class="fas fa-folder-open"></i>
            </div>
          </div>
        </div>
        
        <div class="stat-card success">
          <div class="stat-card-content">
            <div>
              <div class="stat-card-label">Document Ready</div>
              <div class="stat-card-value">2</div>
            </div>
            <div class="stat-card-icon">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>
        
        <div class="stat-card info">
          <div class="stat-card-content">
            <div>
              <div class="stat-card-label">Pending Docs</div>
              <div class="stat-card-value">2</div>
            </div>
            <div class="stat-card-icon">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>
        
        <div class="stat-card warning">
          <div class="stat-card-content">
            <div>
              <div class="stat-card-label">Missing Docs</div>
              <div class="stat-card-value">1</div>
            </div>
            <div class="stat-card-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Assigned Cases Table -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="fas fa-clipboard-list"></i> Assigned Cases</h5>
          <div class="d-flex">
            <button class="btn btn-sm btn-outline-primary me-2">
              <i class="fas fa-filter me-2"></i> Filter
            </button>
            <button class="btn btn-sm btn-primary">
              <i class="fas fa-plus me-2"></i> New Case
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Case ID</th>
                  <th>Applicant</th>
                  <th>Visa Type</th>
                  <th>Assigned Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>CASE-001</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">JS</div>
                      John Smith
                    </div>
                  </td>
                  <td>Tourist Visa</td>
                  <td>Aug 20, 2025</td>
                  <td><span class="badge bg-success">Document Ready</span></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-success">
                        <i class="fas fa-check"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><strong>CASE-002</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">SJ</div>
                      Sarah Johnson
                    </div>
                  </td>
                  <td>Work Visa</td>
                  <td>Aug 21, 2025</td>
                  <td><span class="badge bg-success">Document Ready</span></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-success">
                        <i class="fas fa-check"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><strong>CASE-003</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">MB</div>
                      Michael Brown
                    </div>
                  </td>
                  <td>Student Visa</td>
                  <td>Aug 22, 2025</td>
                  <td><span class="badge bg-warning">Pending Documents</span></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-bell"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><strong>CASE-004</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">EW</div>
                      Emily Wilson
                    </div>
                  </td>
                  <td>Business Visa</td>
                  <td>Aug 23, 2025</td>
                  <td><span class="badge bg-warning">Pending Documents</span></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-bell"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td><strong>CASE-005</strong></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-2" style="width:32px; height:32px; background-color:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:600;">DL</div>
                      David Lee
                    </div>
                  </td>
                  <td>Tourist Visa</td>
                  <td>Aug 24, 2025</td>
                  <td><span class="badge bg-danger">Missing Documents</span></td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-exclamation-circle"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Recent Activity -->
      <div class="card">
        <div class="card-header">
          <h5 class="card-title"><i class="fas fa-history"></i> Recent Activity</h5>
          <button class="btn btn-sm btn-outline-primary">View All</button>
        </div>
        <div class="card-body" style="padding: 1rem;">
          <div style="padding: 0.75rem; border-left: 3px solid var(--primary); background: rgba(67, 97, 238, 0.05); margin-bottom: 0.75rem;">
            <div class="d-flex justify-between mb-2">
              <strong>Document Uploaded</strong>
              <small>Today, 10:23 AM</small>
            </div>
            <p class="mb-0" style="font-size: 0.875rem;">Sarah Johnson uploaded passport scan for work visa application.</p>
          </div>
          
          <div style="padding: 0.75rem; border-left: 3px solid var(--success); background: rgba(12, 206, 107, 0.05); margin-bottom: 0.75rem;">
            <div class="d-flex justify-between mb-2">
              <strong>Case Approved</strong>
              <small>Yesterday, 3:45 PM</small>
            </div>
            <p class="mb-0" style="font-size: 0.875rem;">Tourist visa for John Smith has been approved and forwarded to printing.</p>
          </div>
          
          <div style="padding: 0.75rem; border-left: 3px solid var(--warning); background: rgba(255, 158, 0, 0.05);">
            <div class="d-flex justify-between mb-2">
              <strong>Reminder Sent</strong>
              <small>Yesterday, 11:30 AM</small>
            </div>
            <p class="mb-0" style="font-size: 0.875rem;">Reminder sent to David Lee for missing passport photo documents.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu toggle
      const mobileToggle = document.querySelector('.mobile-toggle');
      const sidebar = document.querySelector('.sidebar');
      
      if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
          sidebar.classList.toggle('show');
        });
      }
      
      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(event) {
        if (window.innerWidth < 768 && 
            !event.target.closest('.sidebar') && 
            !event.target.closest('.mobile-toggle') && 
            sidebar.classList.contains('show')) {
          sidebar.classList.remove('show');
        }
      });
    });
  </script>
</body>
</html>
