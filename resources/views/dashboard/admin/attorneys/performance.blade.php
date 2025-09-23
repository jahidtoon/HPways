@extends('layouts.dashboard')

@section('title', 'Attorney Performance')
@section('page-title', 'Attorney Performance')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Performance Analytics</h1>
            <p class="text-muted">Performance metrics and analytics for {{ $user->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.attorneys.view', $user) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <!-- Overall Performance Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['total_approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Rejected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['total_rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approval Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['approval_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg Resolution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $overallStats['average_resolution_days'] }} days</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Performance (Last 12 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="performanceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Summary</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="donut-chart-container" style="position: relative; height: 200px;">
                            <canvas id="approvalRateChart" width="200" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right pb-3">
                                <h5 class="text-success">{{ $overallStats['total_approved'] }}</h5>
                                <small class="text-muted">Approved</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="pb-3">
                                <h5 class="text-danger">{{ $overallStats['total_rejected'] }}</h5>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">This Month</h6>
                </div>
                <div class="card-body">
                    @php
                        $thisMonth = collect($monthlyStats)->last();
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Approved Cases</span>
                        <span class="badge bg-success fs-6">{{ $thisMonth['approved'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Rejected Cases</span>
                        <span class="badge bg-danger fs-6">{{ $thisMonth['rejected'] ?? 0 }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Monthly Rate</span>
                        @php
                            $monthlyTotal = ($thisMonth['approved'] ?? 0) + ($thisMonth['rejected'] ?? 0);
                            $monthlyRate = $monthlyTotal > 0 ? round((($thisMonth['approved'] ?? 0) / $monthlyTotal) * 100, 1) : 0;
                        @endphp
                        <span class="fw-bold text-{{ $monthlyRate >= 80 ? 'success' : ($monthlyRate >= 60 ? 'warning' : 'danger') }}">{{ $monthlyRate }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Monthly Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Approved</th>
                            <th>Rejected</th>
                            <th>Total</th>
                            <th>Approval Rate</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_reverse($monthlyStats) as $stat)
                        @php
                            $total = $stat['approved'] + $stat['rejected'];
                            $rate = $total > 0 ? round(($stat['approved'] / $total) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td>{{ $stat['month'] }}</td>
                            <td><span class="badge bg-success">{{ $stat['approved'] }}</span></td>
                            <td><span class="badge bg-danger">{{ $stat['rejected'] }}</span></td>
                            <td><strong>{{ $total }}</strong></td>
                            <td>{{ $rate }}%</td>
                            <td>
                                @if($rate >= 80)
                                    <span class="badge bg-success">Excellent</span>
                                @elseif($rate >= 60)
                                    <span class="badge bg-warning">Good</span>
                                @elseif($total > 0)
                                    <span class="badge bg-danger">Needs Improvement</span>
                                @else
                                    <span class="badge bg-secondary">No Data</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Performance Chart
const monthlyData = @json($monthlyStats);
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [{
            label: 'Approved',
            data: monthlyData.map(item => item.approved),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Rejected',
            data: monthlyData.map(item => item.rejected),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Approval Rate Donut Chart
const approvalCtx = document.getElementById('approvalRateChart').getContext('2d');
const approved = {{ $overallStats['total_approved'] }};
const rejected = {{ $overallStats['total_rejected'] }};

new Chart(approvalCtx, {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Rejected'],
        datasets: [{
            data: [approved, rejected],
            backgroundColor: [
                'rgba(75, 192, 192, 0.8)',
                'rgba(255, 99, 132, 0.8)'
            ],
            borderColor: [
                'rgb(75, 192, 192)',
                'rgb(255, 99, 132)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection