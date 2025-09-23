<!-- Ship Package Modal -->
<div class="modal fade" id="shipPackageModal" tabindex="-1" aria-labelledby="shipPackageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="shipPackageForm" action="{{ route('printing.ship', ['shipment' => 'PLACEHOLDER']) }}" method="POST">
                @csrf
                <input type="hidden" id="ship_shipment_id" name="shipment_id">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="shipPackageModalLabel">
                        <i class="fas fa-truck"></i> Ship Package
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Confirm that the package has been handed over to the carrier and update shipping details.
                    </div>
                    
                    <div class="mb-3">
                        <label for="actual_carrier" class="form-label">Actual Carrier Used</label>
                        <select class="form-select" id="actual_carrier" name="actual_carrier">
                            <option value="">Same as prepared</option>
                            <option value="USPS">USPS</option>
                            <option value="UPS">UPS</option>
                            <option value="FedEx">FedEx</option>
                            <option value="DHL">DHL</option>
                        </select>
                        <div class="form-text">Leave blank if using the same carrier as prepared</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="actual_service" class="form-label">Actual Service Used</label>
                        <input type="text" class="form-control" id="actual_service" name="actual_service" placeholder="e.g., Priority Mail, Ground, etc.">
                        <div class="form-text">Leave blank if using the same service as prepared</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="shipped_at" class="form-label">Ship Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="shipped_at" name="shipped_at" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="shipping_notes" class="form-label">Shipping Notes</label>
                        <textarea class="form-control" id="shipping_notes" name="notes" rows="3" placeholder="Any additional notes about the shipment..."></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_handover" required>
                        <label class="form-check-label" for="confirm_handover">
                            I confirm that the package has been handed over to the carrier
                        </label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-truck"></i> Confirm Shipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set current date/time when modal opens
document.getElementById('shipPackageModal').addEventListener('show.bs.modal', function () {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.getElementById('shipped_at').value = localDateTime;
});

// Handle form submission
document.getElementById('shipPackageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const shipmentId = document.getElementById('ship_shipment_id').value;
    const formData = new FormData(this);
    
    // Update the form action with the actual shipment ID
    const actionUrl = this.action.replace('PLACEHOLDER', shipmentId);
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('shipPackageModal')).hide();
            alert('Package shipped successfully! ' + data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while shipping package');
    });
});
</script>