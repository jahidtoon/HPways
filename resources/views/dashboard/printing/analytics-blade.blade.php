@extends('layouts.app')

@section('title', 'Printing Department Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Printing Department Analytics</h1>
                <div class="btn-group">
                    <a href="{{ route('printing.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-primary" onclick="location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-queue fa-2x me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Total in Queue</h5>
                            <h3 class="mb-0">{{ $stats['total_in_queue'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-print fa-2x me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Currently Printing</h5>
                            <h3 class="mb-0">{{ $stats['currently_printing'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check fa-2x me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Printed Today</h5>
                            <h3 class="mb-0">{{ $stats['printed_today'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-truck fa-2x me-3"></i>
                        <div>
                            <h5 class="card-title mb-1">Shipped Today</h5>
                            <h3 class="mb-0">{{ $stats['shipped_today'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <div>
                                    <small class="text-muted">Avg Print Time</small>
                                    <div class="fw-bold">{{ $performance['avg_print_time'] }} min</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-shipping-fast text-info me-2"></i>
                                <div>
                                    <small class="text-muted">Avg Ship Time</small>
                                    <div class="fw-bold">{{ $performance['avg_ship_time'] }} hrs</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-percentage text-success me-2"></i>
                                <div>
                                    <small class="text-muted">Completion Rate</small>
                                    <div class="fw-bold">{{ $performance['completion_rate'] }}%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-target text-warning me-2"></i>
                                <div>
                                    <small class="text-muted">On-Time Delivery</small>
                                    <div class="fw-bold">{{ $performance['on_time_delivery'] }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Queue Analysis by Visa Type</h5>
                </div>
                <div class="card-body">
                    @if($queueAnalysis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Visa Type</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = $queueAnalysis->sum('count');
                                    @endphp
                                    @foreach($queueAnalysis as $item)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ strtoupper($item->visa_type) }}</span>
                                        </td>
                                        <td>{{ $item->count }}</td>
                                        <td>
                                            @php
                                                $percentage = $total > 0 ? round(($item->count / $total) * 100, 1) : 0;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" style="width: {{ $percentage }}%">{{ $percentage }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No items in print queue</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Application</th>
                                        <th>Event</th>
                                        <th>Description</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivity as $activity)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $activity->occurred_at ? $activity->occurred_at->format('M d, H:i') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <strong>#{{ $activity->application->id ?? 'N/A' }}</strong>
                                            @if($activity->application)
                                                <br><small class="text-muted">{{ strtoupper($activity->application->visa_type) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $eventBadges = [
                                                    'printed' => 'success',
                                                    'shipped' => 'info',
                                                    'delivered' => 'primary',
                                                    'tracking_update' => 'warning'
                                                ];
                                                $badgeClass = $eventBadges[$activity->event_type] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $activity->event_type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->description }}</td>
                                        <td>{{ $activity->user->name ?? 'System' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5>No recent activity</h5>
                            <p class="text-muted">Recent printing and shipping activities will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh every 30 seconds for real-time updates
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection