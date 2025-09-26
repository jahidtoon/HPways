@extends('layouts.dashboard')

@section('title', 'Shipment Tracking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-truck me-2"></i>Shipment Tracking
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Shipments</h6>
                                            <h3 class="mb-0">{{ $stats['total_shipments'] }}</h3>
                                        </div>
                                        <i class="fas fa-box fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Shipped</h6>
                                            <h3 class="mb-0">{{ $stats['shipped_shipments'] }}</h3>
                                        </div>
                                        <i class="fas fa-shipping-fast fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Delivered</h6>
                                            <h3 class="mb-0">{{ $stats['delivered_shipments'] }}</h3>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pending</h6>
                                            <h3 class="mb-0">{{ $stats['pending_shipments'] }}</h3>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Shipments</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Application</th>
                                            <th>Tracking Number</th>
                                            <th>Carrier</th>
                                            <th>Status</th>
                                            <th>Shipped Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($shipments as $shipment)
                                        <tr>
                                            <td>#{{ $shipment->id }}</td>
                                            <td>
                                                @if($shipment->application)
                                                    <a href="{{ route('admin.application-detail', $shipment->application->id) }}">
                                                        #{{ $shipment->application->id }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $shipment->application->user->name ?? 'Unknown' }}</small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{ $shipment->tracking_number ?? 'N/A' }}
                                                @if($shipment->tracking_number)
                                                    <small class="text-muted d-block">#{{ $shipment->id }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $shipment->carrier ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $shipment->status === 'delivered' ? 'success' : ($shipment->status === 'shipped' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($shipment->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ $shipment->shipped_at ? $shipment->shipped_at->format('M d, Y H:i') : 'Not shipped' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewTracking({{ $shipment->id }})">
                                                        <i class="fas fa-eye"></i> Track
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="refreshTracking({{ $shipment->id }})" title="Refresh from carrier">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted p-4">No shipments found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $shipments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tracking Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="trackingContent">
                    <p class="text-center">Loading tracking information...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function viewTracking(shipmentId){
    try{
        const res = await fetch(`/printing/shipment/${shipmentId}/tracking`, {headers:{'Accept':'application/json'}, credentials:'same-origin'});
        const data = await res.json();
        const lines = (data.events||[]).map(e => `
            <div class="d-flex align-items-start gap-2 py-1">
                <i class="fas fa-circle text-secondary" style="font-size:.5rem; margin-top:.5rem"></i>
                <div>
                    <div><strong>${(e.event_type||'update').replaceAll('_',' ')}</strong> — ${e.description||''}</div>
                    <div class="text-muted small">${e.event_time||e.occurred_at||''} ${e.location? ' · '+e.location:''}</div>
                </div>
            </div>
        `).join('');
        document.getElementById('trackingContent').innerHTML = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">${data.shipment.carrier||''}</div>
                        <div class="small text-muted">${data.shipment.tracking_number||''}</div>
                    </div>
                    <span class="badge ${data.shipment.status==='delivered'?'bg-success':'bg-info'}">${data.shipment.status}</span>
                </div>
            </div>
            <div class="border-top pt-2">${lines || '<div class="text-muted">No events yet.</div>'}</div>
        `;
        new bootstrap.Modal(document.getElementById('trackingModal')).show();
    }catch(e){
        document.getElementById('trackingContent').innerHTML = `<div class="text-danger">Failed to load tracking</div>`;
        new bootstrap.Modal(document.getElementById('trackingModal')).show();
    }
}

async function refreshTracking(id){
    try{
        const res = await fetch(`/printing/shipment/${id}/refresh-tracking`, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}, credentials:'same-origin'});
        const data = await res.json();
        if(!res.ok || data.success===false) throw new Error(data.message||'Failed');
        // After refresh, reopen the tracking view
        await viewTracking(id);
    }catch(e){
        alert('Refresh failed: '+e.message);
    }
}
</script>
@endsection