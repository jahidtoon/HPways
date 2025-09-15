<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documents - Horizon Pathways</title>
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
      background: linear-gradient(135deg, #3f37c9 0%, #4361ee 100%);
      color: #fff;
      border-radius: 1rem;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 24px rgba(63, 55, 201, 0.15);
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
    
    /* Document specific styles */
    .document-item {
      border-left: 4px solid transparent;
      transition: all 0.2s ease;
    }
    
    .document-item:hover {
      background-color: #f7fafc;
      transform: translateX(5px);
    }
    
    .document-item.verified {
      border-left-color: var(--success);
    }
    
    .document-item.pending {
      border-left-color: var(--warning);
    }
    
    .document-item.rejected {
      border-left-color: var(--danger);
    }
    
    .document-icon {
      width: 42px;
      height: 42px;
      background-color: #edf2f7;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
    }
    
    .doc-pdf {
      color: #e53e3e;
    }
    
    .doc-image {
      color: #3182ce;
    }
    
    .doc-file {
      color: #805ad5;
    }
    
    .doc-excel {
      color: #38a169;
    }
    
    .doc-word {
      color: #4299e1;
    }
    
    .filter-btn.active {
      background-color: var(--primary);
      color: white;
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
      <a href="{{ route('dashboard.printing.documents') }}" class="sidebar-menu-item active">
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
        <input type="text" placeholder="Search documents...">
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
        <h1 class="welcome">Document Management</h1>
        <p class="subtitle">View, organize, and manage all application documents</p>
      </div>
      <div class="ms-auto">
        <button class="btn btn-light btn-lg">
          <i class="fas fa-upload me-2"></i> Upload Document
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
                  <i class="fas fa-file-alt fa-2x text-primary"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Total Documents</h6>
                  <h3 class="mb-0">457</h3>
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
                  <h6 class="text-muted mb-1">Verified</h6>
                  <h3 class="mb-0">302</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 p-3 bg-warning bg-opacity-10 rounded me-3">
                  <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Pending</h6>
                  <h3 class="mb-0">142</h3>
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
                  <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <div>
                  <h6 class="text-muted mb-1">Rejected</h6>
                  <h3 class="mb-0">13</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Management -->
      <div class="row mb-4">
        <div class="col-xl-8 mb-4 mb-xl-0">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="card-title">
                <i class="fas fa-file-alt text-primary"></i> Recent Documents
              </h5>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary filter-btn active" data-filter="all">
                  All
                </button>
                <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="verified">
                  Verified
                </button>
                <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="pending">
                  Pending
                </button>
                <button class="btn btn-sm btn-outline-primary filter-btn" data-filter="rejected">
                  Rejected
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <div class="list-group-item p-3 document-item verified">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="document-icon">
                        <i class="fas fa-file-pdf fa-lg doc-pdf"></i>
                      </div>
                      <div>
                        <h6 class="mb-1">Passport.pdf</h6>
                        <p class="mb-0 text-muted small">
                          <span class="me-2">John Doe • APP-101</span>
                          <span>2.4 MB</span>
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-success me-3">Verified</span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                          <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 document-item pending">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="document-icon">
                        <i class="fas fa-file-image fa-lg doc-image"></i>
                      </div>
                      <div>
                        <h6 class="mb-1">Visa_Photo.jpg</h6>
                        <p class="mb-0 text-muted small">
                          <span class="me-2">Jane Smith • APP-102</span>
                          <span>1.8 MB</span>
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-warning me-3">Pending</span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                          <li><a class="dropdown-item" href="#"><i class="fas fa-check me-2"></i>Verify</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 document-item rejected">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="document-icon">
                        <i class="fas fa-file-alt fa-lg doc-file"></i>
                      </div>
                      <div>
                        <h6 class="mb-1">Tax_Document.pdf</h6>
                        <p class="mb-0 text-muted small">
                          <span class="me-2">Robert Johnson • APP-103</span>
                          <span>4.2 MB</span>
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-danger me-3">Rejected</span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                          <li><a class="dropdown-item" href="#"><i class="fas fa-redo me-2"></i>Request New</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Delete</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 document-item verified">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="document-icon">
                        <i class="fas fa-file-excel fa-lg doc-excel"></i>
                      </div>
                      <div>
                        <h6 class="mb-1">Financial_Records.xlsx</h6>
                        <p class="mb-0 text-muted small">
                          <span class="me-2">Maria Garcia • APP-104</span>
                          <span>1.2 MB</span>
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-success me-3">Verified</span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton4">
                          <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="list-group-item p-3 document-item pending">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                      <div class="document-icon">
                        <i class="fas fa-file-word fa-lg doc-word"></i>
                      </div>
                      <div>
                        <h6 class="mb-1">Employment_Letter.docx</h6>
                        <p class="mb-0 text-muted small">
                          <span class="me-2">William Brown • APP-105</span>
                          <span>0.8 MB</span>
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-warning me-3">Pending</span>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton5" data-bs-toggle="dropdown" aria-expanded="false">
                          Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton5">
                          <li><a class="dropdown-item" href="#"><i class="fas fa-check me-2"></i>Verify</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white text-center">
              <button class="btn btn-link text-primary">View All Documents</button>
            </div>
          </div>
        </div>
        
        <div class="col-xl-4">
          <!-- Document Categories -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-folder text-primary"></i> Document Categories
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-passport text-primary me-3"></i>
                    <span>Identification Documents</span>
                  </div>
                  <span class="badge bg-primary rounded-pill">87</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar text-success me-3"></i>
                    <span>Financial Documents</span>
                  </div>
                  <span class="badge bg-success rounded-pill">64</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-medical text-danger me-3"></i>
                    <span>Medical Records</span>
                  </div>
                  <span class="badge bg-danger rounded-pill">43</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-university text-info me-3"></i>
                    <span>Educational Documents</span>
                  </div>
                  <span class="badge bg-info rounded-pill">51</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-briefcase text-warning me-3"></i>
                    <span>Employment Documents</span>
                  </div>
                  <span class="badge bg-warning rounded-pill">72</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-contract text-secondary me-3"></i>
                    <span>Legal Documents</span>
                  </div>
                  <span class="badge bg-secondary rounded-pill">38</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Storage Usage -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-hdd text-primary"></i> Storage Usage
              </h5>
            </div>
            <div class="card-body">
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0">Storage Space</h6>
                  <span class="text-muted">12.8 GB / 20 GB</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: 64%;" aria-valuenow="64" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>

              <h6 class="mb-3">File Types</h6>
              
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-pdf text-danger me-2"></i>
                    <span>PDF Documents</span>
                  </div>
                  <span class="text-muted">7.2 GB</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-danger" role="progressbar" style="width: 56%;" aria-valuenow="56" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-image text-info me-2"></i>
                    <span>Images</span>
                  </div>
                  <span class="text-muted">3.5 GB</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-info" role="progressbar" style="width: 27%;" aria-valuenow="27" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-word text-primary me-2"></i>
                    <span>Word Documents</span>
                  </div>
                  <span class="text-muted">1.2 GB</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-primary" role="progressbar" style="width: 9%;" aria-valuenow="9" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              
              <div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="d-flex align-items-center">
                    <i class="fas fa-file-excel text-success me-2"></i>
                    <span>Excel Documents</span>
                  </div>
                  <span class="text-muted">0.9 GB</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: 7%;" aria-valuenow="7" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white text-center">
              <button class="btn btn-sm btn-primary">
                <i class="fas fa-trash-alt me-1"></i> Clean Storage
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Document Preview -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title">
            <i class="fas fa-eye text-primary"></i> Document Preview
          </h5>
          <div>
            <button class="btn btn-sm btn-outline-primary me-2">
              <i class="fas fa-download me-1"></i> Download
            </button>
            <button class="btn btn-sm btn-primary">
              <i class="fas fa-print me-1"></i> Print
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="border rounded p-3 mb-3 mb-md-0">
                <div class="d-flex align-items-center mb-3">
                  <div class="document-icon">
                    <i class="fas fa-file-pdf fa-lg doc-pdf"></i>
                  </div>
                  <div>
                    <h6 class="mb-1">Passport.pdf</h6>
                    <p class="mb-0 text-muted small">John Doe • APP-101</p>
                  </div>
                </div>
                <div class="mb-3">
                  <span class="badge bg-success mb-2">Verified</span>
                  <p class="mb-1 text-muted small"><strong>Uploaded:</strong> Sep 3, 2025</p>
                  <p class="mb-1 text-muted small"><strong>Size:</strong> 2.4 MB</p>
                  <p class="mb-0 text-muted small"><strong>Type:</strong> PDF Document</p>
                </div>
                <div>
                  <h6 class="mb-2">Document Notes</h6>
                  <p class="text-muted small mb-0">Passport verified and validated. Expiration date: March 15, 2030. All pages are legible and properly scanned.</p>
                </div>
              </div>
            </div>
            <div class="col-md-9">
              <div class="border rounded p-3 h-100 d-flex align-items-center justify-content-center">
                <div class="text-center">
                  <img src="https://via.placeholder.com/800x500" alt="Document Preview" class="img-fluid rounded" style="max-height: 400px;">
                  <p class="mt-3 text-muted">Passport document preview - Page 1 of 4</p>
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary">
                      <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary">
                      <i class="fas fa-chevron-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Document View Modal -->
  <div class="modal fade" id="documentViewModal" tabindex="-1" aria-labelledby="documentViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="documentViewModalLabel">Document View</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img src="https://via.placeholder.com/800x600" alt="Document Preview" class="img-fluid rounded">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">
            <i class="fas fa-print me-1"></i> Print Document
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

      // Document filter buttons
      const filterBtns = document.querySelectorAll('.filter-btn');
      const documentItems = document.querySelectorAll('.document-item');
      
      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          // Remove active class from all buttons
          filterBtns.forEach(b => b.classList.remove('active'));
          
          // Add active class to clicked button
          this.classList.add('active');
          
          const filter = this.getAttribute('data-filter');
          
          // Show/hide documents based on filter
          documentItems.forEach(item => {
            if (filter === 'all') {
              item.style.display = 'block';
            } else {
              if (item.classList.contains(filter)) {
                item.style.display = 'block';
              } else {
                item.style.display = 'none';
              }
            }
          });
        });
      });
    });
  </script>
</body>
</html>
