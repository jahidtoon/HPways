<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Horizon Pathways</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #3a36d8;
            --primary-light: #5e57e8;
            --primary-dark: #2a2aa0;
            --secondary: #2a2aa0;
            --success: #0cce6b;
            --danger: #e5383b;
            --warning: #ff9e00;
            --info: #4cc9f0;
            --light-bg: #f8fafc;
            --dark: #1a2436;
            --transition-speed: 0.3s;
        }
        
        html, body { margin: 0; padding: 0; }
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            overscroll-behavior-x: none; /* prevent back/forward swipe overlay */
            overflow-x: hidden; /* avoid accidental horizontal scroll */
            touch-action: pan-y; /* allow vertical scroll only */
            margin: 0; /* remove default browser margin causing top gap */
        }

        /* Defensive: prevent any oversized decorative icons from leaking between pages */
        .icon-bg { display: none !important; }
        i.fas, i.far, i.fal, i.fad, i.fab { font-size: 1rem; line-height: 1; }
        i[class^="fa-"]::before, i[class*=" fa-"]::before { font-size: 1em; }
        .main-content { overflow-x: hidden; overscroll-behavior: contain; }
        
        /* Remove any demo/tutorial overlays (scoped, non-destructive) */
        .demo-arrow, .tutorial-arrow, .highlight-indicator, .guide-pointer,
        [data-demo], [data-tutorial],
        .demo-overlay, .tutorial-overlay { display: none !important; }
        
        /* Prevent external overlay elements (avoid touching FA icons) */
        div[style*="position: absolute"][style*="arrow"],
        div[style*="position: fixed"][style*="arrow"],
        div[style*="border-left"][style*="red"],
        div[style*="border-right"][style*="red"],
        div[style*="background: red"],
        div[style*="background-color: red"],
        [style*="border-color: red"] { 
            display: none !important; 
            visibility: hidden !important;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: white;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
        }

        /* Custom scrollbar for sidebar */
        .sidebar-nav-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .sidebar-nav-container::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1.5rem 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
            flex-shrink: 0;
        }
        
        .sidebar-logo {
            height: 45px;
            width: 45px;
            margin-right: 0.75rem;
            border-radius: 8px;
            object-fit: contain;
        }
        /* Larger logo variant when text is hidden (applicant view) */
        .sidebar-logo--large { height: 64px; width: 64px; margin-right: 0; }
        .sidebar-brand.only-logo { padding: 1.25rem 1.25rem; justify-content: center; }
        .sidebar-brand.only-logo .sidebar-logo { 
            width: 100%;
            height: auto;
            max-height: 120px; /* fills the red box area without overflow */
            margin: 0 auto; 
            display: block; 
            object-fit: contain;
        }
        
        .brand-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }
        
        .brand-text span {
            font-size: 0.875rem;
            color: #718096;
        }
        
        .sidebar-nav {
            padding: 0 1rem;
            flex: 1;
        }

        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 1rem;
        }
        
        .sidebar-nav .nav-item {
            margin-bottom: 0.375rem;
        }
        
        .sidebar-nav .nav-link {
            color: #4a5568;
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all var(--transition-speed) ease;
            display: flex;
            align-items: center;
        }
        
        .sidebar-nav .nav-link:hover {
            background-color: #f7fafc;
            color: var(--primary);
        }
        
        .sidebar-nav .nav-link.active {
            background-color: rgba(58, 54, 216, 0.1);
            color: var(--primary);
            font-weight: 600;
        }
        
        .sidebar-nav .nav-link i {
            font-size: 1.125rem;
            margin-right: 0.75rem;
            width: 1.5rem;
            text-align: center;
            color: #a0aec0;
            transition: color var(--transition-speed) ease;
        }
        
        .sidebar-nav .nav-link:hover i,
        .sidebar-nav .nav-link.active i {
            color: var(--primary);
        }
        
        .sidebar-nav .nav-link .badge {
            margin-left: auto;
            background-color: rgba(58, 54, 216, 0.1);
            color: var(--primary);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        
        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a0aec0;
            font-weight: 700;
            padding: 1.5rem 1.5rem 0.75rem;
            margin-top: 1rem;
        }
        
        .sidebar-heading:first-child {
            margin-top: 0;
        }
        
        /* Nav Section Titles */
        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6c757d;
            font-weight: 700;
            padding: 1rem 1.5rem 0.5rem;
            margin: 0.5rem 0 0;
            display: block;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        
        .nav-section-title:first-child {
            border-top: none;
            margin-top: 0;
        }
        
        /* Sidebar Profile Section */
        .profile-section {
            border: 1px solid rgba(226, 232, 240, 0.7);
            transition: all var(--transition-speed) ease;
        }
        
        .profile-section:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: white !important;
        }
        
        .profile-section .btn {
            font-size: 0.75rem;
            border-radius: 0.375rem;
            transition: all var(--transition-speed) ease;
        }
        
        .profile-section .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .profile-section .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .profile-section .btn-outline-danger {
            color: var(--danger);
            border-color: var(--danger);
        }
        
        .profile-section .btn-outline-danger:hover {
            background-color: var(--danger);
            color: white;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 1rem;
            transition: margin-left var(--transition-speed) ease;
            width: calc(100% - 280px);
            max-width: calc(100% - 280px);
            box-sizing: border-box;
        }
        
        /* Top Navigation */
        .top-nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .user-dropdown {
            background: white;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all var(--transition-speed) ease;
        }
        
        .user-dropdown:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .user-avatar {
            width: 38px;
            height: 38px;
            background: rgba(58, 54, 216, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1rem;
            margin-right: 0.75rem;
        }
        
        .user-info {
            margin-right: 0.75rem;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--dark);
            margin-bottom: 0.125rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: #718096;
        }
        
        .dropdown-menu {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            min-width: 220px;
        }
        
        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            transition: all var(--transition-speed) ease;
        }
        
        .dropdown-item i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
            color: #a0aec0;
        }
        
        .dropdown-item:hover {
            background-color: #f7fafc;
        }
        
        .dropdown-item:hover i {
            color: var(--primary);
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: #f0f4f8;
        }
        
        .dropdown-item.logout {
            color: var(--danger);
        }
        
        .dropdown-item.logout i {
            color: var(--danger);
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
                padding: 1rem 0.5rem;
            }
            
            .navbar-toggler {
                display: block;
            }
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
                max-width: calc(100% - 280px);
            }
        }
        
        /* Mobile Toggle Button */
        .navbar-toggler {
            background-color: white;
            border: none;
            padding: 0.5rem;
            font-size: 1.25rem;
            border-radius: 0.375rem;
            margin-right: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: none;
        }
        
        /* Mobile Header */
        .mobile-header {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #ffffff;
            border-bottom: 1px solid rgba(226,232,240,0.7);
            z-index: 1100; /* above sidebar (1000) */
            display: none; /* hidden by default, shown on mobile */
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .mobile-header .mobile-header-inner {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-header .mobile-menu-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(58,54,216,0.25);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .mobile-header .mobile-menu-toggle i { font-size: 1.2rem; line-height: 1; }
        .mobile-header .mobile-logo {
            height: 32px;
            object-fit: contain;
        }
        
        @media (max-width: 991.98px) {
            .mobile-header { display: flex; }
            /* Give room under sticky header */
            .main-content { padding-top: 0.5rem; }
            /* Hide empty top nav on mobile to avoid extra spacing */
            .top-nav { display: none; }
            /* Hide sidebar brand text on mobile */
            .sidebar-brand .brand-text { display: none !important; }
            .sidebar-brand { justify-content: center; }
            .sidebar-brand .sidebar-logo { height: 56px; width: 56px; }
        }
        
        /* Page Title Styles */
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 1rem;
            color: #718096;
            margin-bottom: 2rem;
        }
        
        /* Alerts */
        .alert {
            border-radius: 0.5rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .alert-success {
            background-color: rgba(12, 206, 107, 0.1);
            color: var(--success);
        }
        
        .alert-danger {
            background-color: rgba(229, 56, 59, 0.1);
            color: var(--danger);
        }
        
        .alert-warning {
            background-color: rgba(255, 158, 0, 0.1);
            color: var(--warning);
        }
        
        .alert-info {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--info);
        }
        
        .alert .btn-close {
            font-size: 0.875rem;
            padding: 1rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            @php
                $__user = auth()->user();
                $__role = $__user && $__user->getRoleNames()->isNotEmpty() ? $__user->getRoleNames()->first() : 'applicant';
            @endphp
            <div class="sidebar-brand d-flex align-items-center only-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Horizon Pathways Logo" class="sidebar-logo">
            </div>
            
            <div class="sidebar-nav-container" style="flex: 1; overflow-y: auto; overflow-x: hidden;">
                <ul class="sidebar-nav nav flex-column">
                @php
                    $user = auth()->user();
                    $userRole = $user && $user->getRoleNames()->isNotEmpty() ? $user->getRoleNames()->first() : 'applicant';
                @endphp

                @if($userRole === 'applicant' || $userRole === 'user')
                    <!-- Applicant Menu -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.applicant.index') }}" class="nav-link {{ request()->routeIs('dashboard.applicant.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.applicant.applications') }}" class="nav-link {{ request()->routeIs('dashboard.applicant.applications') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>My Applications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.applicant.documents') }}" class="nav-link {{ request()->routeIs('dashboard.applicant.documents') ? 'active' : '' }}">
                            <i class="fas fa-file-upload"></i>
                            <span>Documents</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.applicant.payments') }}" class="nav-link {{ request()->routeIs('dashboard.applicant.payments') ? 'active' : '' }}">
                            <i class="fas fa-credit-card"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.applicant.meetings.index') }}" class="nav-link {{ request()->routeIs('dashboard.applicant.meetings.*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i>
                            <span>Meetings</span>
                        </a>
                    </li>
                    {{-- Resources/Support/Settings removed as per request --}}

                @elseif($userRole === 'case_manager')
                    <!-- Case Manager Menu -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.index') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Applications Management Section -->
                    <li class="nav-item">
                        <span class="nav-section-title">Applications</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.applications') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.applications') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>My Applications</span>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.all-applications') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.all-applications') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span>All Applications</span>
                        </a>
                    </li> --}}
                    
                    <!-- Team Management Section -->
                    <li class="nav-item">
                        <span class="nav-section-title">Team</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.attorneys') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.attorneys') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Attorneys</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.meetings.index') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.meetings.*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i>
                            <span>Meetings</span>
                        </a>
                    </li>
                    
                    {{-- <!-- Reports Section -->
                    <li class="nav-item">
                        <span class="nav-section-title">Reports</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.reports') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.reports') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.analytics') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.analytics') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li> --}}
                    
                    {{-- <!-- Tools Section -->
                    <li class="nav-item">
                        <span class="nav-section-title">Tools</span>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.documents') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.documents') ? 'active' : '' }}">
                            <i class="fas fa-folder-open"></i>
                            <span>Document Manager</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.notifications') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.notifications') ? 'active' : '' }}">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.case-manager.settings') }}" class="nav-link {{ request()->routeIs('dashboard.case-manager.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li> --}}

                @elseif($userRole === 'attorney')
                    <!-- Attorney Menu -->
                    <li class="nav-item">
                        <a href="/dashboard/attorney" class="nav-link {{ request()->routeIs('dashboard.attorney.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/dashboard/attorney/cases" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>My Cases</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.attorney.meetings.index') }}" class="nav-link {{ request()->routeIs('dashboard.attorney.meetings.*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i>
                            <span>Meetings</span>
                        </a>
                    </li>

                @elseif($userRole === 'printing_department')
                    <!-- Printing Department Menu -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard.printing.index') }}" class="nav-link {{ request()->routeIs('dashboard.printing.index') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.printing.queue') }}" class="nav-link {{ request()->routeIs('dashboard.printing.queue') ? 'active' : '' }}">
                            <i class="fas fa-print"></i>
                            <span>Print Queue</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.printing.shipping') }}" class="nav-link {{ request()->routeIs('dashboard.printing.shipping') ? 'active' : '' }}">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Shipping & Tracking</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard.printing.management') }}" class="nav-link {{ request()->routeIs('dashboard.printing.management') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span>Management</span>
                        </a>
                    </li>

                @elseif($userRole === 'big_admin' || $userRole === 'admin')
                    <!-- Admin Menu -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>All Applications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>All Applicants</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.case-managers') }}" class="nav-link {{ request()->routeIs('admin.case-managers') ? 'active' : '' }}">
                            <i class="fas fa-briefcase"></i>
                            <span>Case Managers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.attorneys') }}" class="nav-link {{ request()->routeIs('admin.attorneys') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i>
                            <span>Attorneys</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.printing-staff') }}" class="nav-link {{ request()->routeIs('admin.printing-staff') ? 'active' : '' }}">
                            <i class="fas fa-print"></i>
                            <span>Printing Staff</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.payment-settings') }}" class="nav-link {{ request()->routeIs('admin.payment-settings') ? 'active' : '' }}">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.shipment-tracking') }}" class="nav-link {{ request()->routeIs('admin.shipment-tracking') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i>
                            <span>Shipment Tracking</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span>Packages</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.quizzes.index') }}" class="nav-link {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                            <i class="fas fa-question-circle"></i>
                            <span>Quiz Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.meetings.index') }}" class="nav-link {{ request()->routeIs('admin.meetings.*') ? 'active' : '' }}">
                            <i class="fas fa-video"></i>
                            <span>Meetings</span>
                        </a>
                    </li>


                @else
                    <!-- Default fallback menu -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endif
                
                <!-- User Profile Section -->
                <div class="px-3 pt-4 mb-4">
                    <div class="sidebar-heading">Account</div>
                    <div class="profile-section mt-2 p-3 bg-light rounded-3">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="user-role">
                                    @php
                                        $user = auth()->user();
                                        $displayRole = 'Guest';
                                        if ($user && $user->getRoleNames()->isNotEmpty()) {
                                            $role = $user->getRoleNames()->first();
                                            $displayRole = match($role) {
                                                'big_admin' => 'Administrator',
                                                'case_manager' => 'Case Manager',
                                                'attorney' => 'Attorney',
                                                'printing_department' => 'Printing Dept.',
                                                default => 'Applicant'
                                            };
                                        } else {
                                            $displayRole = 'Applicant';
                                        }
                                    @endphp
                                    {{ $displayRole }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-between">
                            <a href="#" class="btn btn-sm btn-outline-primary px-3">
                                <i class="fas fa-user me-1"></i> Profile
                            </a>
                            <a href="/logout.php" class="btn btn-sm btn-outline-danger px-3">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-auto"></div>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Mobile Header -->
            <div class="mobile-header d-lg-none">
                <div class="mobile-header-inner">
                    <button class="mobile-menu-toggle" type="button" aria-label="Open menu">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                    <img src="{{ asset('images/logo.png') }}" alt="Horizon Pathways" class="mobile-logo">
                </div>
            </div>
            
            <!-- Top Navigation (kept minimal) -->
            <nav class="top-nav">
                <!-- Old mobile toggler removed; using mobile header toggle instead -->
            </nav>
            
            <!-- Page Title -->
            <div class="mb-4">
                <h1 class="page-title">@yield('page-title')</h1>
                @hasSection('page-subtitle')
                <p class="page-subtitle">@yield('page-subtitle')</p>
                @endif
            </div>
            
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Main Content -->
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            // Remove only known demo/tutorial overlays (do not touch FA icons)
            const removeArrowIndicators = () => {
                document.querySelectorAll('.demo-arrow, .tutorial-arrow, .highlight-indicator, .guide-pointer, [data-demo], [data-tutorial], .demo-overlay, .tutorial-overlay').forEach(el => {
                    el.remove();
                });
                // Avoid removing legitimate icons like fa-arrow-up
            };
            
            // Defensive runtime fix: clamp any stray giant icons (e.g., injected chevrons) inside content area
            const clampStrayIcons = () => {
                document.querySelectorAll('.main-content svg, .main-content i').forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.width > 200 || rect.height > 200) {
                        el.style.maxWidth = '120px';
                        el.style.maxHeight = '120px';
                        el.style.opacity = '0.08';
                        el.style.pointerEvents = 'none';
                    }
                });
            };
            
            // Run cleanups
            removeArrowIndicators();
            clampStrayIcons();
            setTimeout(() => {
                removeArrowIndicators();
                clampStrayIcons();
            }, 300);
            setTimeout(() => {
                removeArrowIndicators();
                clampStrayIcons();
            }, 1000);
            
            // Set up observer to remove arrows if they're added dynamically
            const observer = new MutationObserver(() => {
                removeArrowIndicators();
            });
            observer.observe(document.body, { childList: true, subtree: true });
            // Mobile sidebar toggle
            const sidebar = document.querySelector('.sidebar');
            const togglers = document.querySelectorAll('.navbar-toggler, .mobile-menu-toggle');

            togglers.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('show');
                });
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const clickedToggle = event.target.closest('.navbar-toggler, .mobile-menu-toggle');
                if (window.innerWidth < 992 && sidebar.classList.contains('show') && 
                    !sidebar.contains(event.target) && !clickedToggle) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
    
    <!-- Page specific scripts: support both section and stack -->
    @yield('scripts')
    @stack('scripts')
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</body>
</html>
