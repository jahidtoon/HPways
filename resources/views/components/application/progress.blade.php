@php
    /**
     * Shared Application Progress widget
     * Expects: $application (App\Models\Application)
     * Shows: Queue → Printing → Printed → Ready to Ship → Shipped → Delivered → Received
     * Actions: Mark Printed, Mark Delivered (admin/case_manager/attorney/printing_department)
     *          Confirm Received (applicant when delivered)
     */
    $app = $application;
    $status = $app->status ?? 'new';
    $hasShipment = !!($app->shipment);
    $shipment = $app->shipment;

    // Event helpers
    $hasEvent = function($type) use ($app) {
        try {
            return $app->trackingEvents()->where('event_type', $type)->exists();
        } catch (Throwable $e) { return false; }
    };

    $isQueued     = in_array($status, ['in_print_queue','printing','printed','ready_to_ship','shipped','delivered']);
    $isPrinting   = in_array($status, ['printing','printed','ready_to_ship','shipped','delivered']);
    $isPrinted    = in_array($status, ['printed','ready_to_ship','shipped','delivered']) || $hasEvent('printing_completed');
    $isReadyToShip= in_array($status, ['ready_to_ship','shipped','delivered']) || $hasEvent('shipment_prepared');
    $isShipped    = in_array($status, ['shipped','delivered']) || ($hasShipment && $shipment->shipped_at) || $hasEvent('shipped');
    $isDelivered  = $status === 'delivered' || ($hasShipment && $shipment->delivered_at) || $hasEvent('delivered');
    $isReceived   = $hasEvent('received_confirmed');

    $user = auth()->user();
    $canStaffAction = $user && ($user->hasRole('admin') || $user->hasRole('case_manager') || $user->hasRole('attorney') || $user->hasRole('printing_department'));
    $isApplicant = $user && $user->id === ($app->user_id ?? 0) && $user->hasRole('applicant');

    $csrf = csrf_token();
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Case Progress</strong>
        <div class="small text-muted">#APP-{{ $app->id }}</div>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-2">
            @php
                $steps = [
                    ['key'=>'queued','label'=>'Queued'],
                    ['key'=>'printing','label'=>'Printing'],
                    ['key'=>'printed','label'=>'Printed'],
                    ['key'=>'ready','label'=>'Ready to Ship'],
                    ['key'=>'shipped','label'=>'Shipped'],
                    ['key'=>'delivered','label'=>'Delivered'],
                    ['key'=>'received','label'=>'Received']
                ];
                $state = [
                    'queued'   => $isQueued,
                    'printing' => $isPrinting,
                    'printed'  => $isPrinted,
                    'ready'    => $isReadyToShip,
                    'shipped'  => $isShipped,
                    'delivered'=> $isDelivered,
                    'received' => $isReceived,
                ];
            @endphp
            @foreach($steps as $i => $step)
                @php
                    $done = $state[$step['key']] ?? false;
                @endphp
                <div class="d-flex align-items-center">
                    <span class="badge {{ $done ? 'bg-success' : 'bg-light text-muted' }}">
                        @if($done)
                            <i class="fas fa-check me-1"></i>
                        @else
                            <i class="fas fa-circle me-1" style="font-size:.5rem"></i>
                        @endif
                        {{ $step['label'] }}
                    </span>
                </div>
                @if($i < count($steps)-1)
                    <span class="mx-2 text-muted">›</span>
                @endif
            @endforeach
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
            @if($canStaffAction)
                @if(!$isPrinted)
                    <button class="btn btn-sm btn-outline-primary" onclick="markPrinted({{ $app->id }})">
                        <i class="fas fa-print me-1"></i> Mark Printed
                    </button>
                @endif
                @if(!$isDelivered)
                    <button class="btn btn-sm btn-outline-success" onclick="markDelivered({{ $app->id }})">
                        <i class="fas fa-truck me-1"></i> Mark Delivered
                    </button>
                @endif
            @endif
            @if($isApplicant && $isDelivered && !$isReceived)
                <button class="btn btn-sm btn-outline-dark" onclick="confirmReceived({{ $app->id }})">
                    <i class="fas fa-check-double me-1"></i> I’ve received the documents
                </button>
            @endif
        </div>

        @if($hasShipment)
            <div class="mt-3 small text-muted">
                Tracking: <strong>{{ $shipment->tracking_number }}</strong> · Carrier: {{ $shipment->actual_carrier ?: $shipment->carrier }} · Status: {{ $shipment->status }}
            </div>
        @endif
    </div>
</div>

<script>
async function markPrinted(id){
  try{
    const res = await fetch(`/applications/${id}/progress/mark-printed`, {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrf }}' }
    });
    const data = await res.json();
    if(!res.ok || !data.success) throw new Error(data.message || 'Failed');
    location.reload();
  }catch(e){ alert('Error: '+e.message); }
}
async function markDelivered(id){
  try{
    const res = await fetch(`/applications/${id}/progress/mark-delivered`, {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrf }}' }
    });
    const data = await res.json();
    if(!res.ok || !data.success) throw new Error(data.message || 'Failed');
    location.reload();
  }catch(e){ alert('Error: '+e.message); }
}
async function confirmReceived(id){
  try{
    const res = await fetch(`/applications/${id}/progress/confirm-received`, {
      method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ $csrf }}' }
    });
    const data = await res.json();
    if(!res.ok || !data.success) throw new Error(data.message || 'Failed');
    location.reload();
  }catch(e){ alert('Error: '+e.message); }
}
</script>
