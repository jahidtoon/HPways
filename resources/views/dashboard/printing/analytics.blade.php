<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - Horizon Pathways</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
      color: #fff;
      border-radius: 1rem;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 24px rgba(76, 201, 240, 0.15);
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
    
    /* Analytics specific styles */
    .stats-highlight {
      position: relative;
      padding: 1.5rem;
      border-radius: 1rem;
      color: white;
      overflow: hidden;
    }
    
    .stats-highlight::before {
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
    
    .stats-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.7;
    }
    
    .stats-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .stats-value {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    
    .stats-trend {
      display: flex;
      align-items: center;
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .stats-trend i {
      margin-right: 0.25rem;
    }
    
    .trend-up {
      color: rgba(255, 255, 255, 0.9);
    }
    
    .trend-down {
      color: rgba(255, 255, 255, 0.9);
    }
    
    .stats-primary {
      background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
    }
    
    .stats-success {
      background: linear-gradient(135deg, #0cce6b 0%, #06b35d 100%);
    }
    
    .stats-info {
      background: linear-gradient(135deg, #4cc9f0 0%, #3db8df 100%);
    }
    
    .stats-warning {
      background: linear-gradient(135deg, #ff9e00 0%, #ff7300 100%);
    }
    
    .date-range-filter {
      display: flex;
      align-items: center;
    }
    
    .date-range-filter .form-select {
      max-width: 200px;
      margin-left: 0.5rem;
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
      <a href="{{ route('dashboard.printing.shipping') }}" class="sidebar-menu-item">
        <i class="fas fa-shipping-fast"></i>
        <span>Shipping</span>
      </a>
      <a href="{{ route('dashboard.printing.documents') }}" class="sidebar-menu-item">
        <i class="fas fa-file-alt"></i>
        <span>Documents</span>
      </a>
      
      <div class="sidebar-menu-header">Reports</div>
      <a href="{{ route('dashboard.printing.analytics') }}" class="sidebar-menu-item active">
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
        <input type="text" placeholder="Search analytics...">
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
        <h1 class="welcome">Analytics Dashboard</h1>
        <p class="subtitle">Track performance metrics and generate insights from printing and shipping operations</p>
      </div>
      <div class="ms-auto date-range-filter">
        <label class="text-white mb-0">Time Period:</label>
        <select class="form-select form-select-sm">
          <option>Last 7 Days</option>
          <option>Last 30 Days</option>
          <option>This Month</option>
          <option>Last Quarter</option>
          <option>Year to Date</option>
          <option>Custom Range</option>
        </select>
      </div>
    </div>
    
    <div class="container-fluid p-0">
      <!-- Stats Highlights -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-highlight stats-primary h-100">
            <i class="fas fa-print stats-icon"></i>
            <div class="stats-title">Documents Printed</div>
            <div class="stats-value">428</div>
            <div class="stats-trend trend-up">
              <i class="fas fa-arrow-up"></i>
              <span>12.8% vs previous period</span>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-highlight stats-success h-100">
            <i class="fas fa-shipping-fast stats-icon"></i>
            <div class="stats-title">Shipments Completed</div>
            <div class="stats-value">284</div>
            <div class="stats-trend trend-up">
              <i class="fas fa-arrow-up"></i>
              <span>8.3% vs previous period</span>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-highlight stats-info h-100">
            <i class="fas fa-clock stats-icon"></i>
            <div class="stats-title">Avg. Processing Time</div>
            <div class="stats-value">1.2<small class="fs-5"> days</small></div>
            <div class="stats-trend trend-down">
              <i class="fas fa-arrow-down"></i>
              <span>6.2% vs previous period</span>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="stats-highlight stats-warning h-100">
            <i class="fas fa-exclamation-triangle stats-icon"></i>
            <div class="stats-title">Delivery Issues</div>
            <div class="stats-value">12</div>
            <div class="stats-trend trend-down">
              <i class="fas fa-arrow-down"></i>
              <span>4.1% vs previous period</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
          <!-- Printing & Shipping Trends -->
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title">
                <i class="fas fa-chart-line text-primary"></i> Printing & Shipping Trends
              </h5>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary active" data-period="day">Daily</button>
                <button class="btn btn-sm btn-outline-primary" data-period="week">Weekly</button>
                <button class="btn btn-sm btn-outline-primary" data-period="month">Monthly</button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="trendsChart" height="300"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <!-- Document Type Distribution -->
          <div class="card h-100">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-chart-pie text-primary"></i> Document Types
              </h5>
            </div>
            <div class="card-body">
              <canvas id="documentTypeChart" height="220"></canvas>
              <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">&nbsp;</span>
                    <span>Passports</span>
                  </div>
                  <span class="fw-bold">32%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge bg-info me-2">&nbsp;</span>
                    <span>Visa Forms</span>
                  </div>
                  <span class="fw-bold">28%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">&nbsp;</span>
                    <span>I-Forms</span>
                  </div>
                  <span class="fw-bold">21%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <span class="badge bg-warning me-2">&nbsp;</span>
                    <span>Other Documents</span>
                  </div>
                  <span class="fw-bold">19%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Second Charts Row -->
      <div class="row mb-4">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <!-- Carrier Distribution -->
          <div class="card h-100">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-truck text-primary"></i> Carrier Distribution
              </h5>
            </div>
            <div class="card-body">
              <canvas id="carrierChart" height="220"></canvas>
              <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge me-2" style="background-color: #4361ee;">&nbsp;</span>
                    <span>FedEx</span>
                  </div>
                  <span class="fw-bold">45%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge me-2" style="background-color: #0cce6b;">&nbsp;</span>
                    <span>UPS</span>
                  </div>
                  <span class="fw-bold">30%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="d-flex align-items-center">
                    <span class="badge me-2" style="background-color: #ff9e00;">&nbsp;</span>
                    <span>USPS</span>
                  </div>
                  <span class="fw-bold">15%</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <span class="badge me-2" style="background-color: #e5383b;">&nbsp;</span>
                    <span>DHL</span>
                  </div>
                  <span class="fw-bold">10%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <!-- Performance Metrics -->
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title">
                <i class="fas fa-tachometer-alt text-primary"></i> Performance Metrics
              </h5>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary active" data-metric="time">Processing Time</button>
                <button class="btn btn-sm btn-outline-primary" data-metric="success">Success Rate</button>
                <button class="btn btn-sm btn-outline-primary" data-metric="cost">Cost Efficiency</button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="performanceChart" height="300"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Key Metrics Table -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">
            <i class="fas fa-table text-primary"></i> Key Performance Indicators
          </h5>
          <button class="btn btn-sm btn-outline-primary">
            <i class="fas fa-download me-1"></i> Export Report
          </button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Current</th>
                  <th>Previous</th>
                  <th>Change</th>
                  <th>Target</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Average Processing Time</td>
                  <td>1.2 days</td>
                  <td>1.3 days</td>
                  <td><span class="text-success"><i class="fas fa-arrow-down me-1"></i>7.7%</span></td>
                  <td>1.5 days</td>
                  <td><span class="badge bg-success">On Target</span></td>
                </tr>
                <tr>
                  <td>Delivery Success Rate</td>
                  <td>98.7%</td>
                  <td>97.2%</td>
                  <td><span class="text-success"><i class="fas fa-arrow-up me-1"></i>1.5%</span></td>
                  <td>98.0%</td>
                  <td><span class="badge bg-success">On Target</span></td>
                </tr>
                <tr>
                  <td>Documents Processed per Day</td>
                  <td>42</td>
                  <td>38</td>
                  <td><span class="text-success"><i class="fas fa-arrow-up me-1"></i>10.5%</span></td>
                  <td>40</td>
                  <td><span class="badge bg-success">On Target</span></td>
                </tr>
                <tr>
                  <td>Ink Consumption per Document</td>
                  <td>0.7 ml</td>
                  <td>0.8 ml</td>
                  <td><span class="text-success"><i class="fas fa-arrow-down me-1"></i>12.5%</span></td>
                  <td>0.7 ml</td>
                  <td><span class="badge bg-success">On Target</span></td>
                </tr>
                <tr>
                  <td>Average Shipping Cost</td>
                  <td>$12.85</td>
                  <td>$12.24</td>
                  <td><span class="text-danger"><i class="fas fa-arrow-up me-1"></i>5.0%</span></td>
                  <td>$11.50</td>
                  <td><span class="badge bg-warning">Above Target</span></td>
                </tr>
                <tr>
                  <td>Document Error Rate</td>
                  <td>0.8%</td>
                  <td>1.2%</td>
                  <td><span class="text-success"><i class="fas fa-arrow-down me-1"></i>33.3%</span></td>
                  <td>1.0%</td>
                  <td><span class="badge bg-success">On Target</span></td>
                </tr>
              </tbody>
            </table>
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

      // Chart.js initialization
      // Printing & Shipping Trends Chart
      const trendsChartCtx = document.getElementById('trendsChart').getContext('2d');
      const trendsChart = new Chart(trendsChartCtx, {
        type: 'line',
        data: {
          labels: ['Aug 1', 'Aug 8', 'Aug 15', 'Aug 22', 'Aug 29', 'Sep 5'],
          datasets: [
            {
              label: 'Printed Documents',
              data: [65, 78, 52, 91, 83, 75],
              borderColor: '#4361ee',
              backgroundColor: 'rgba(67, 97, 238, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4
            },
            {
              label: 'Shipped Documents',
              data: [48, 62, 39, 75, 68, 58],
              borderColor: '#0cce6b',
              backgroundColor: 'rgba(12, 206, 107, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            },
            tooltip: {
              mode: 'index',
              intersect: false,
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
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

      // Document Type Chart
      const documentTypeChartCtx = document.getElementById('documentTypeChart').getContext('2d');
      const documentTypeChart = new Chart(documentTypeChartCtx, {
        type: 'doughnut',
        data: {
          labels: ['Passports', 'Visa Forms', 'I-Forms', 'Other Documents'],
          datasets: [
            {
              data: [32, 28, 21, 19],
              backgroundColor: ['#4361ee', '#4cc9f0', '#0cce6b', '#ff9e00'],
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          cutout: '70%'
        }
      });

      // Carrier Distribution Chart
      const carrierChartCtx = document.getElementById('carrierChart').getContext('2d');
      const carrierChart = new Chart(carrierChartCtx, {
        type: 'doughnut',
        data: {
          labels: ['FedEx', 'UPS', 'USPS', 'DHL'],
          datasets: [
            {
              data: [45, 30, 15, 10],
              backgroundColor: ['#4361ee', '#0cce6b', '#ff9e00', '#e5383b'],
              borderWidth: 0
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          cutout: '70%'
        }
      });

      // Performance Metrics Chart
      const performanceChartCtx = document.getElementById('performanceChart').getContext('2d');
      const performanceChart = new Chart(performanceChartCtx, {
        type: 'bar',
        data: {
          labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
          datasets: [
            {
              label: 'Avg. Processing Time (Days)',
              data: [1.4, 1.3, 1.2, 1.3, 1.1, 1.2],
              backgroundColor: '#4361ee',
              borderRadius: 6,
              barPercentage: 0.6
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
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

      // Chart period buttons
      const periodButtons = document.querySelectorAll('[data-period]');
      periodButtons.forEach(button => {
        button.addEventListener('click', function() {
          periodButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          
          // In a real application, this would update the chart with new data
          // For demo purposes, we'll just modify the chart labels
          if (this.getAttribute('data-period') === 'day') {
            trendsChart.data.labels = ['Sep 1', 'Sep 2', 'Sep 3', 'Sep 4', 'Sep 5', 'Sep 6', 'Sep 7'];
          } else if (this.getAttribute('data-period') === 'week') {
            trendsChart.data.labels = ['Aug 1', 'Aug 8', 'Aug 15', 'Aug 22', 'Aug 29', 'Sep 5'];
          } else {
            trendsChart.data.labels = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'];
          }
          trendsChart.update();
        });
      });

      // Chart metric buttons
      const metricButtons = document.querySelectorAll('[data-metric]');
      metricButtons.forEach(button => {
        button.addEventListener('click', function() {
          metricButtons.forEach(btn => btn.classList.remove('active'));
          this.classList.add('active');
          
          // In a real application, this would update the chart with new data
          // For demo purposes, we'll just modify the chart data
          if (this.getAttribute('data-metric') === 'time') {
            performanceChart.data.datasets[0].label = 'Avg. Processing Time (Days)';
            performanceChart.data.datasets[0].data = [1.4, 1.3, 1.2, 1.3, 1.1, 1.2];
            performanceChart.data.datasets[0].backgroundColor = '#4361ee';
          } else if (this.getAttribute('data-metric') === 'success') {
            performanceChart.data.datasets[0].label = 'Delivery Success Rate (%)';
            performanceChart.data.datasets[0].data = [96.4, 97.2, 98.1, 97.5, 98.6, 98.7];
            performanceChart.data.datasets[0].backgroundColor = '#0cce6b';
          } else {
            performanceChart.data.datasets[0].label = 'Cost per Document ($)';
            performanceChart.data.datasets[0].data = [13.2, 12.8, 12.9, 12.5, 12.7, 12.4];
            performanceChart.data.datasets[0].backgroundColor = '#ff9e00';
          }
          performanceChart.update();
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
