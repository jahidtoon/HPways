<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Printing Department Dashboard - Horizon Pathways</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    
    /* Header Styles */
    .header {
      background: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .header-brand {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .header-brand img {
      height: 40px;
    }
    
    .header-brand h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
      margin: 0;
    }
    
    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: all var(--transition-speed);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .btn-primary {
      background: var(--primary);
      color: white;
    }
    
    .btn-primary:hover {
      background: var(--primary-light);
      transform: translateY(-1px);
    }
    
    .btn-info {
      background: var(--info);
      color: white;
    }
    
    .btn-danger {
      background: var(--danger);
      color: white;
    }
    
    /* Main Content */
    .main-content {
      padding: 2rem;
      max-width: 1400px;
      margin: 0 auto;
    }
    
    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background: white;
      border-radius: var(--card-border-radius);
      padding: 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
      transition: all var(--transition-speed);
      position: relative;
      overflow: hidden;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    
    .stat-card.primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
    }
    
    .stat-card.warning {
      background: linear-gradient(135deg, var(--warning) 0%, #f97316 100%);
      color: white;
    }
    
    .stat-card.success {
      background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
      color: white;
    }
    
    .stat-card.info {
      background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
      color: white;
    }
    
    .stat-content {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .stat-icon {
      font-size: 2.5rem;
      opacity: 0.9;
    }
    
    .stat-text h3 {
      font-size: 2.5rem;
      font-weight: 700;
      margin: 0;
      line-height: 1;
    }
    
    .stat-text p {
      font-size: 1rem;
      opacity: 0.9;
      margin: 0.5rem 0 0 0;
      font-weight: 500;
    }
    
    /* Tab Navigation */
    .tab-nav {
      display: flex;
      background: white;
      border-radius: var(--card-border-radius);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
      margin-bottom: 2rem;
      overflow: auto;
    }
    
    .tab-item {
      padding: 1rem 1.5rem;
      cursor: pointer;
      border: none;
      background: none;
      font-weight: 500;
      color: #6b7280;
      transition: all var(--transition-speed);
      border-bottom: 3px solid transparent;
      white-space: nowrap;
    }
    
    .tab-item.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
      background: rgba(67, 97, 238, 0.05);
    }
    
    .tab-item:hover {
      color: var(--primary);
      background: rgba(67, 97, 238, 0.02);
    }
    
    /* Content Card */
    .content-card {
      background: white;
      border-radius: var(--card-border-radius);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
      overflow: hidden;
    }
    
    .content-header {
      padding: 1.5rem 2rem;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: between;
      align-items: center;
    }
    
    .content-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
      color: var(--dark);
    }
    
    .content-body {
      padding: 0;
    }
    
    /* Table Styles */
    .table-responsive {
      overflow-x: auto;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 1rem 1.5rem;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    
    th {
      background: #f9fafb;
      font-weight: 600;
      color: var(--dark);
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    td {
      font-weight: 500;
    }
    
    .badge {
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    .badge.primary {
      background: rgba(67, 97, 238, 0.1);
      color: var(--primary);
    }
    
    .badge.success {
      background: rgba(12, 206, 107, 0.1);
      color: var(--success);
    }
    
    .badge.warning {
      background: rgba(255, 158, 0, 0.1);
      color: var(--warning);
    }
    
    .badge.secondary {
      background: rgba(107, 114, 128, 0.1);
      color: #6b7280;
    }
    
    /* Action Buttons */
    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
    }
    
    .btn-group {
      display: flex;
      gap: 0.5rem;
    }
    
    .d-flex {
      display: flex;
      align-items: center;
    }
    
    .justify-between {
      justify-content: space-between;
    }
    
    .gap-1 {
      gap: 0.5rem;
    }
    
    .container {
      width: 100%;
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 1rem;
    }
  </style>
</head>
<body>
    <!-- Header -->
  <header class="header">
    <div class="container d-flex justify-between">
      <div class="header-brand">
        <img src="/images/logo.png" alt="Horizon Pathways" onerror="this.style.display='none'">
        <h1>Horizon Pathways</h1>
      </div>
      <div class="header-actions">
        <span>Hi, Print Manager!</span>
        <a href="/logout" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Printing Department Dashboard</h1>
                <div class="btn-group">
                    <a href="{{ route('printing.analytics') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview Cards -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-text">
                    <h3>{{ $stats['in_queue'] }}</h3>
                    <p>In Queue</p>
                </div>
            </div>
        </div>
        <div class="stat-card warning">
            <div class="stat-content">
                <i class="fas fa-print stat-icon"></i>
                <div class="stat-text">
                    <h3>{{ $stats['printing'] }}</h3>
                    <p>Printing</p>
                </div>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-content">
                <i class="fas fa-check stat-icon"></i>
                <div class="stat-text">
                    <h3>{{ $stats['printed'] }}</h3>
                    <p>Printed</p>
                </div>
            </div>
        </div>
        <div class="stat-card info">
            <div class="stat-content">
                <i class="fas fa-shipping-fast stat-icon"></i>
                <div class="stat-text">
                    <h3>{{ $stats['ready_to_ship'] }}</h3>
                    <p>Ready to Ship</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-nav">
        <button class="tab-item active" onclick="showTab('queue', this)">
            <i class="fas fa-list"></i> Print Queue ({{ $stats['in_queue'] }})
        </button>
        <button class="tab-item" onclick="showTab('printing', this)">
            <i class="fas fa-print"></i> Currently Printing ({{ $stats['printing'] }})
        </button>
        <button class="tab-item" onclick="showTab('printed', this)">
            <i class="fas fa-check"></i> Printed Documents ({{ $stats['printed'] }})
        </button>
        <button class="tab-item" onclick="showTab('shipping', this)">
            <i class="fas fa-truck"></i> Shipping Center ({{ $stats['ready_to_ship'] }})
        </button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Print Queue Tab -->
        <div class="content-card" id="queue-content">
            <div class="content-header">
                <h2>Print Queue</h2>
                <div class="btn-group">
                    <button class="btn btn-primary btn-sm" onclick="autoAddApproved()">
                        <i class="fas fa-plus"></i> Auto-Add Approved
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="syncAssigned()" title="Move my assigned apps into queue">
                        <i class="fas fa-sync"></i> Sync Assigned
                    </button>
                    <button class="btn btn-success btn-sm" onclick="bulkPrint()">
                        <i class="fas fa-print"></i> Bulk Print
                    </button>
                </div>
            </div>
            <div class="content-body">
                @if($queueApplications->count() > 0)
                    <div class="table-responsive">
                        <table>
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>ID</th>
                                        <th>Applicant</th>
                                        <th>Visa Type</th>
                                        <th>Queue Time</th>
                                        <th>Priority</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($queueApplications as $application)
                                    <tr>
                                        <td><input type="checkbox" name="applications[]" value="{{ $application->id }}"></td>
                                        <td><strong>#{{ $application->id }}</strong></td>
                                        <td>{{ $application->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge primary">{{ strtoupper($application->visa_type) }}</span>
                                        </td>
                                        <td>
                                            <small style="color: #6b7280;">
                                                {{ $application->updated_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($application->priority ?? false)
                                                <span class="badge warning">High</span>
                                            @else
                                                <span class="badge secondary">Normal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-warning btn-sm" onclick="startPrinting({{ $application->id }})">
                                                    <i class="fas fa-play"></i> Start
                                                </button>
                                                <button class="btn btn-info btn-sm" onclick="viewApplication({{ $application->id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No applications in print queue</h5>
                            <p class="text-muted">Approved applications will automatically appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Currently Printing Tab -->
        <div class="content-card" id="printing-content" style="display: none;">
            <div class="content-header">
                <h2>Currently Printing</h2>
            </div>
            <div class="content-body">
                @if($printingApplications->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Visa Type</th>
                                    <th>Started</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($printingApplications as $application)
                                <tr>
                                    <td><strong>#{{ $application->id }}</strong></td>
                                    <td>{{ $application->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge warning">{{ strtoupper($application->visa_type) }}</span>
                                    </td>
                                    <td>{{ $application->printing_started_at ? $application->printing_started_at->format('M d, H:i') : 'N/A' }}</td>
                                    <td>
                                        @if($application->printing_started_at)
                                            {{ $application->printing_started_at->diffForHumans() }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-success btn-sm" onclick="markPrinted({{ $application->id }})">
                                                <i class="fas fa-check"></i> Mark Printed
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-print" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                        <h5>No documents currently printing</h5>
                    </div>
                @endif
            </div>
        </div>

        <!-- Printed Documents Tab -->
        <div class="content-card" id="printed-content" style="display: none;">
            <div class="content-header">
                <h2>Printed Documents</h2>
            </div>
            <div class="content-body">
                @if($printedApplications->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Visa Type</th>
                                    <th>Printed At</th>
                                    <th>Mailing Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($printedApplications as $application)
                                <tr>
                                    <td><strong>#{{ $application->id }}</strong></td>
                                    <td>{{ $application->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge success">{{ strtoupper($application->visa_type) }}</span>
                                    </td>
                                    <td>{{ $application->printed_at ? $application->printed_at->format('M d, H:i') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $lockbox = config('lockbox.' . strtoupper($application->visa_type));
                                            if ($lockbox && isset($lockbox['groups'])) {
                                                $groups = array_keys($lockbox['groups']);
                                                echo '<small class="text-muted">Available lockboxes:<br>' . implode(', ', $groups) . '</small>';
                                            } else {
                                                echo '<small class="text-muted">No lockbox data</small>';
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-primary btn-sm" onclick="openPrepareShipmentModal({{ $application->id }}, '{{ addslashes($application->user->name ?? '') }}', '{{ strtoupper($application->visa_type) }}')">
                                                <i class="fas fa-box"></i> Prepare Shipment
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-file-alt" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                        <h5>No printed documents</h5>
                    </div>
                @endif
            </div>
        </div>

        <!-- Shipping Center Tab -->
        <div class="content-card" id="shipping-content" style="display: none;">
            <div class="content-header">
                <h2>Shipping Center</h2>
            </div>
            <div class="content-body">
                @if($readyToShipApplications->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Visa Type</th>
                                    <th>Printed At</th>
                                    <th>Shipping Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($readyToShipApplications as $application)
                                <tr>
                                    <td><strong>#{{ $application->id }}</strong></td>
                                    <td>{{ $application->user->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge info">{{ strtoupper($application->visa_type) }}</span>
                                    </td>
                                    <td>{{ $application->printed_at ? $application->printed_at->format('M d, H:i') : 'N/A' }}</td>
                                    <td>
                                        @if($application->shipment)
                                            <small>{{ $application->shipment->recipient_address }}</small>
                                        @else
                                            <small class="text-muted">Not prepared</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($application->shipment)
                                            <span class="badge {{ $application->shipment->status == 'prepared' ? 'warning' : 'success' }}">
                                                {{ ucfirst($application->shipment->status) }}
                                            </span>
                                        @else
                                            <span class="badge secondary">Not Prepared</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if(!$application->shipment)
                                                <button class="btn btn-primary btn-sm" onclick="openPrepareShipmentModal({{ $application->id }}, '{{ addslashes($application->user->name ?? '') }}', '{{ strtoupper($application->visa_type) }}')">
                                                    <i class="fas fa-box"></i> Prepare
                                                </button>
                                            @elseif($application->shipment->status == 'prepared')
                                                <button class="btn btn-success btn-sm" onclick="openShipPackageModal({{ $application->shipment->id }})">
                                                    <i class="fas fa-truck"></i> Ship
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-truck" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                        <h5>No documents ready for shipping</h5>
                    </div>
                @endif

                <hr class="my-4">
                <h4 class="mb-3"><i class="fas fa-shipping-fast"></i> Recent Shipments</h4>
                @if(isset($recentShipments) && $recentShipments->count() > 0)
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Carrier</th>
                                    <th>Tracking</th>
                                    <th>Status</th>
                                    <th>Shipped</th>
                                    <th>Delivered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentShipments as $shipment)
                                    <tr>
                                        <td><strong>#{{ $shipment->application_id }}</strong></td>
                                        <td>{{ $shipment->application->user->name ?? 'N/A' }}</td>
                                        <td>{{ $shipment->actual_carrier ?? $shipment->carrier ?? 'N/A' }}</td>
                                        <td>
                                            @if($shipment->tracking_number)
                                                <code>{{ $shipment->tracking_number }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $shipment->status === 'delivered' ? 'success' : ($shipment->status === 'prepared' ? 'warning' : 'info') }}">
                                                {{ ucfirst($shipment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $shipment->shipped_at ? $shipment->shipped_at->format('M d, H:i') : '—' }}</td>
                                        <td>{{ $shipment->delivered_at ? $shipment->delivered_at->format('M d, H:i') : '—' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-outline-secondary btn-sm" onclick="viewTracking({{ $shipment->id }})">
                                                    <i class="fas fa-route"></i> Track
                                                </button>
                                                @if($shipment->status !== 'delivered')
                                                    <button class="btn btn-outline-success btn-sm" onclick="markDelivered({{ $shipment->id }})" title="Mark as Delivered">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted">No recent shipments yet.</div>
                @endif
            </div>
        </div>
</div>

@include('dashboard.printing.modals.prepare-shipment')
@include('dashboard.printing.modals.ship-package')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function refreshDashboard() {
    location.reload();
}

async function syncAssigned() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const res = await fetch('/printing/sync-assigned', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            throw new Error(data.message || 'Sync failed');
        }
        alert(data.message || 'Synced');
        window.location.reload();
    } catch (err) {
        console.error('Sync assigned error:', err);
        alert('Sync failed: ' + (err.message || 'Unknown error'));
    }
}

function autoAddApproved() {
    console.log('Auto-add function called');
    fetch('/printing/auto-add-approved', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert(`Added ${data.count} approved applications to print queue`);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding applications to queue: ' + error.message);
    });
}

function startPrinting(applicationId) {
    if (confirm('Start printing this application?')) {
        fetch(`/printing/${applicationId}/mark-printing`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async (response) => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                // Non-JSON (could be 419/redirect)
                const text = await response.text();
                throw new Error(`Unexpected response (${response.status}). ${text.substring(0, 200)}`);
            }
            if (!response.ok || !data.success) {
                throw new Error(data && data.message ? data.message : `Request failed (${response.status})`);
            }
            location.reload();
        })
        .catch(error => {
            console.error('Start printing error:', error);
            alert('Start failed: ' + (error.message || 'Unknown error'));
        });
    }
}

function bulkPrint() {
    const checkboxes = document.querySelectorAll('input[name="applications[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Please select applications to print');
        return;
    }
    
    const applicationIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm(`Start printing ${applicationIds.length} selected applications?`)) {
        fetch('/printing/bulk-print', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ applications: applicationIds })
        })
        .then(async (response) => {
            let data;
            try { data = await response.json(); } catch (e) {
                const text = await response.text();
                throw new Error(`Unexpected response (${response.status}). ${text.substring(0,200)}`);
            }
            if (!response.ok || !data.success) {
                throw new Error(data && data.message ? data.message : `Request failed (${response.status})`);
            }
            alert(`Started printing ${data.count} applications`);
            location.reload();
        })
        .catch(error => {
            console.error('Bulk print error:', error);
            alert('Bulk print failed: ' + (error.message || 'Unknown error'));
        });
    }
}

function markPrinted(applicationId) {
    if (confirm('Mark this application as printed?')) {
        fetch(`/printing/${applicationId}/mark-printed`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(async (response) => {
            let data;
            try { data = await response.json(); } catch (e) {
                const text = await response.text();
                throw new Error(`Unexpected response (${response.status}). ${text.substring(0,200)}`);
            }
            if (!response.ok || !data.success) {
                throw new Error(data && data.message ? data.message : `Request failed (${response.status})`);
            }
            location.reload();
        })
        .catch(error => {
            console.error('Mark printed error:', error);
            alert('Mark printed failed: ' + (error.message || 'Unknown error'));
        });
    }
}

function viewApplication(applicationId) {
    // Open printer-specific detail page; fallback to same-tab if popup blocked
    const url = `/printing/applications/${applicationId}`;
    const win = window.open(url, '_blank');
    if (!win || win.closed || typeof win.closed === 'undefined') {
        // Popup likely blocked; navigate in current tab
        window.location.href = url;
    }
}

function openPrepareShipmentModal(applicationId, applicantName, visaType) {
    document.getElementById('shipment_application_id').value = applicationId;
    const visaField = document.getElementById('shipment_application_visa');
    if (visaField) visaField.value = (visaType || '').toUpperCase();
    // Pre-fill recipient name if available
    const nameField = document.getElementById('recipient_name');
    if (nameField && applicantName) nameField.value = applicantName;
    const modalEl = document.getElementById('prepareShipmentModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    // Trigger lockbox computation shortly after modal is visible
    setTimeout(() => { if (window.computeLockbox) window.computeLockbox(); }, 150);
}

function openShipPackageModal(shipmentId) {
    document.getElementById('ship_shipment_id').value = shipmentId;
    new bootstrap.Modal(document.getElementById('shipPackageModal')).show();
}

async function viewTracking(shipmentId) {
    try {
        const res = await fetch(`/printing/shipment/${shipmentId}/tracking`, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
        const data = await res.json();
        if (!data.success) throw new Error('Unable to load tracking');
        const lines = data.events.map(e => `• [${(e.event_time||e.occurred_at)}] ${e.event_type.replace('_',' ')} — ${e.description}`).join('\n');
        alert(`Tracking ${data.shipment.tracking_number || ''} (${data.shipment.carrier || ''})\n\n${lines || 'No events yet.'}`);
    } catch (err) {
        console.error(err);
        alert('Failed to load tracking history');
    }
}

async function markDelivered(shipmentId) {
    if (!confirm('Mark this shipment as delivered?')) return;
    try {
        const form = new FormData();
        form.append('status', 'delivered');
        form.append('description', 'Package delivered');
        form.append('event_date', new Date().toISOString());

        const res = await fetch(`/printing/shipment/${shipmentId}/update-tracking`, {
            method: 'POST',
            body: form,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            credentials: 'same-origin'
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        // Refresh to show delivered badge
        location.reload();
    } catch (err) {
        console.error(err);
        alert('Failed to mark delivered');
    }
}

// Tab functionality
function showTab(tabName, btnEl) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.content-card');
    contents.forEach(content => content.style.display = 'none');
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab-item');
    tabs.forEach(tab => tab.classList.remove('active'));
    
    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }
    
    // Add active class to clicked tab
    if (btnEl) btnEl.classList.add('active');
}

// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="applications[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Initialize first tab as visible
document.addEventListener('DOMContentLoaded', function() {
    const contents = document.querySelectorAll('.content-card');
    contents.forEach((content, index) => {
        content.style.display = index === 0 ? 'block' : 'none';
    });
});
</script>

</main>
</body>
</html>