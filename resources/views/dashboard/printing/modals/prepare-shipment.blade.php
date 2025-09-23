<!-- Prepare Shipment Modal -->
<div class="modal fade" id="prepareShipmentModal" tabindex="-1" aria-labelledby="prepareShipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="prepareShipmentForm" action="{{ route('printing.prepare-shipment') }}" method="POST">
                @csrf
                <input type="hidden" id="shipment_application_id" name="application_id">
                <input type="hidden" id="shipment_application_visa" name="application_visa">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="prepareShipmentModalLabel">
                        <i class="fas fa-box"></i> Prepare Shipment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Recipient Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-user"></i> Recipient Information</h6>
                            
                            <div class="mb-3">
                                <label for="recipient_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="recipient_phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="recipient_phone" name="recipient_phone">
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Shipping Address</h6>
                            
                            <div class="mb-3">
                                <label for="recipient_address" class="form-label">Street Address *</label>
                                <textarea class="form-control" id="recipient_address" name="recipient_address" rows="2" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recipient_city" class="form-label">City *</label>
                                        <input type="text" class="form-control" id="recipient_city" name="recipient_city" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="recipient_state" class="form-label">State *</label>
                                        <select class="form-select" id="recipient_state" name="recipient_state" required>
                                            <option value="">Select State</option>
                                            <option value="AL">Alabama</option>
                                            <option value="AK">Alaska</option>
                                            <option value="AZ">Arizona</option>
                                            <option value="AR">Arkansas</option>
                                            <option value="CA">California</option>
                                            <option value="CO">Colorado</option>
                                            <option value="CT">Connecticut</option>
                                            <option value="DE">Delaware</option>
                                            <option value="FL">Florida</option>
                                            <option value="GA">Georgia</option>
                                            <option value="HI">Hawaii</option>
                                            <option value="ID">Idaho</option>
                                            <option value="IL">Illinois</option>
                                            <option value="IN">Indiana</option>
                                            <option value="IA">Iowa</option>
                                            <option value="KS">Kansas</option>
                                            <option value="KY">Kentucky</option>
                                            <option value="LA">Louisiana</option>
                                            <option value="ME">Maine</option>
                                            <option value="MD">Maryland</option>
                                            <option value="MA">Massachusetts</option>
                                            <option value="MI">Michigan</option>
                                            <option value="MN">Minnesota</option>
                                            <option value="MS">Mississippi</option>
                                            <option value="MO">Missouri</option>
                                            <option value="MT">Montana</option>
                                            <option value="NE">Nebraska</option>
                                            <option value="NV">Nevada</option>
                                            <option value="NH">New Hampshire</option>
                                            <option value="NJ">New Jersey</option>
                                            <option value="NM">New Mexico</option>
                                            <option value="NY">New York</option>
                                            <option value="NC">North Carolina</option>
                                            <option value="ND">North Dakota</option>
                                            <option value="OH">Ohio</option>
                                            <option value="OK">Oklahoma</option>
                                            <option value="OR">Oregon</option>
                                            <option value="PA">Pennsylvania</option>
                                            <option value="RI">Rhode Island</option>
                                            <option value="SC">South Carolina</option>
                                            <option value="SD">South Dakota</option>
                                            <option value="TN">Tennessee</option>
                                            <option value="TX">Texas</option>
                                            <option value="UT">Utah</option>
                                            <option value="VT">Vermont</option>
                                            <option value="VA">Virginia</option>
                                            <option value="WA">Washington</option>
                                            <option value="WV">West Virginia</option>
                                            <option value="WI">Wisconsin</option>
                                            <option value="WY">Wyoming</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="recipient_zip" class="form-label">ZIP Code *</label>
                                <input type="text" class="form-control" id="recipient_zip" name="recipient_zip" required>
                            </div>

                            <!-- Lockbox Suggestions (visible in this column) -->
                            <div class="card" id="lockboxCard" style="display:block; border: 1px solid #e5e7eb;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2" style="gap:.5rem;">
                                        <i class="fas fa-university"></i>
                                        <strong>USCIS Lockbox (Visa + State)</strong>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="p-2 bg-light rounded border">
                                                <div class="small text-muted d-flex justify-content-between">
                                                    <span>USPS</span>
                                                    <span class="text-secondary" id="lockboxMetaVisa"></span>
                                                </div>
                                                <div class="small" id="lockboxUsps">Select visa type and state to see address</div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="applyUspsBtn" disabled>Use USPS Address</button>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="p-2 bg-light rounded border">
                                                <div class="small text-muted">FedEx/UPS/DHL</div>
                                                <div class="small" id="lockboxCourier">Select visa type and state to see address</div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="applyCourierBtn" disabled>Use Courier Address</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <!-- Shipping Options -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-truck"></i> Shipping Options</h6>
                            
                            <div class="mb-3">
                                <label for="carrier" class="form-label">Carrier *</label>
                                <select class="form-select" id="carrier" name="carrier" required onchange="updateServiceOptions()">
                                    <option value="">Select Carrier</option>
                                    <option value="USPS">USPS</option>
                                    <option value="UPS">UPS</option>
                                    <option value="FedEx">FedEx</option>
                                    <option value="DHL">DHL</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="service" class="form-label">Service Type *</label>
                                <select class="form-select" id="service" name="service" required>
                                    <option value="">Select Service</option>
                                    <!-- Options will be populated based on carrier selection -->
                                </select>
                            </div>
                        </div>
                        
                        <!-- Special Instructions -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-sticky-note"></i> Additional Information</h6>
                            
                            <div class="mb-3">
                                <label for="special_instructions" class="form-label">Special Instructions</label>
                                <textarea class="form-control" id="special_instructions" name="special_instructions" rows="4" placeholder="Any special delivery instructions..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estimated Delivery -->
                    <div class="alert alert-info" id="deliveryEstimate" style="display: none;">
                        <i class="fas fa-info-circle"></i> <strong>Estimated Delivery:</strong> <span id="estimatedDate"></span>
                    </div>

                    <!-- (legacy) Lockbox Preview (kept for backward compatibility, hidden by default) -->
                    <div class="alert alert-secondary mt-3" id="lockboxPreview" style="display:none;">
                        <i class="fas fa-university"></i> <strong>Destination Lockbox (Based on Visa + State):</strong>
                        <div class="mt-2 small" id="lockboxAddress"></div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-box"></i> Prepare Shipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Lightweight lockbox map serialized from config for client-side preview
window.LOCKBOX = @json(config('lockbox'));
if (!window.LOCKBOX) {
    console.warn('Lockbox config not loaded. Run: php artisan config:clear && php artisan config:cache');
}

function updateServiceOptions() {
    const carrier = document.getElementById('carrier').value;
    const serviceSelect = document.getElementById('service');
    const deliveryEstimate = document.getElementById('deliveryEstimate');
    
    // Clear existing options
    serviceSelect.innerHTML = '<option value="">Select Service</option>';
    deliveryEstimate.style.display = 'none';
    
    const serviceOptions = {
        'USPS': [
            { value: 'Priority Mail Express', text: 'Priority Mail Express (1-2 days)', days: 2 },
            { value: 'Priority Mail', text: 'Priority Mail (2-3 days)', days: 3 },
            { value: 'Ground Advantage', text: 'Ground Advantage (3-5 days)', days: 5 },
            { value: 'Media Mail', text: 'Media Mail (2-8 days)', days: 6 },
        ],
        'UPS': [
            { value: 'Next Day Air', text: 'Next Day Air (1 day)', days: 1 },
            { value: '2nd Day Air', text: '2nd Day Air (2 days)', days: 2 },
            { value: '3 Day Select', text: '3 Day Select (3 days)', days: 3 },
            { value: 'Ground', text: 'Ground (1-5 days)', days: 5 },
        ],
        'FedEx': [
            { value: 'Priority Overnight', text: 'Priority Overnight (1 day)', days: 1 },
            { value: 'Standard Overnight', text: 'Standard Overnight (1 day)', days: 1 },
            { value: '2Day', text: '2Day (2 days)', days: 2 },
            { value: 'Express Saver', text: 'Express Saver (3 days)', days: 3 },
            { value: 'Ground', text: 'Ground (1-5 days)', days: 5 },
        ],
        'DHL': [
            { value: 'Express Worldwide', text: 'Express Worldwide (1-3 days)', days: 3 },
            { value: 'Economy Select', text: 'Economy Select (4-8 days)', days: 6 },
        ]
    };
    
    if (serviceOptions[carrier]) {
        serviceOptions[carrier].forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.text;
            optionElement.dataset.days = option.days;
            serviceSelect.appendChild(optionElement);
        });
    }
}

// Update estimated delivery when service is selected
document.getElementById('service').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const days = selectedOption.dataset.days;
    
    if (days) {
        const estimatedDate = new Date();
        estimatedDate.setDate(estimatedDate.getDate() + parseInt(days));
        
        document.getElementById('estimatedDate').textContent = estimatedDate.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('deliveryEstimate').style.display = 'block';
    } else {
        document.getElementById('deliveryEstimate').style.display = 'none';
    }
});

function computeLockbox() {
    const visa = (document.getElementById('shipment_application_visa').value || '').toUpperCase();
    const state = (document.getElementById('recipient_state').value || '').toUpperCase();
    const data = window.LOCKBOX?.[visa];
    if (!window.LOCKBOX) {
        const warnEl = document.getElementById('lockboxCard');
        if (warnEl) {
            warnEl.style.display = 'block';
            document.getElementById('lockboxUsps').textContent = 'Lockbox data unavailable. Please clear config cache.';
            document.getElementById('lockboxCourier').textContent = 'Run: php artisan config:clear && php artisan config:cache';
        }
        return;
    }
    // Always show the card, even if no data
    let group = null;
    if (data && data.groups) {
        if (data.groups.ALL) group = data.groups.ALL;
        if (!group && state) {
            for (const key in data.groups) {
                const g = data.groups[key];
                if (g.states && g.states.includes(state)) { group = g; break; }
            }
        }
        if (!group && data.groups.TX && state === 'TX') group = data.groups.TX;
        if (!group && data.groups['OUTSIDE_US'] && state === 'OUTSIDE_US') group = data.groups['OUTSIDE_US'];
    }
    
    // Update the card
    const lbCard = document.getElementById('lockboxCard');
    const uspsDiv = document.getElementById('lockboxUsps');
    const courierDiv = document.getElementById('lockboxCourier');
    const applyUspsBtn = document.getElementById('applyUspsBtn');
    const applyCourierBtn = document.getElementById('applyCourierBtn');
    const metaVisa = document.getElementById('lockboxMetaVisa');
    if (lbCard && uspsDiv && courierDiv) {
        if (metaVisa) metaVisa.textContent = visa ? `VISA: ${visa}${state ? ' · STATE: ' + state : ''}` : '';
        if (group) {
            uspsDiv.textContent = `${group.usps.recipient}, ${group.usps.address}, ${group.usps.city}, ${group.usps.state} ${group.usps.zip}`;
            courierDiv.textContent = `${group.courier.recipient}, ${group.courier.address}, ${group.courier.city}, ${group.courier.state} ${group.courier.zip}`;
            applyUspsBtn.disabled = false;
            applyCourierBtn.disabled = false;
        } else {
            if (!data) {
                uspsDiv.textContent = `No lockbox data available for visa type: ${visa}`;
                courierDiv.textContent = `No lockbox data available for visa type: ${visa}`;
            } else if (!state) {
                uspsDiv.textContent = 'Select a state to see lockbox address';
                courierDiv.textContent = 'Select a state to see lockbox address';
            } else {
                uspsDiv.textContent = `No lockbox found for ${visa} applications in ${state}`;
                courierDiv.textContent = `No lockbox found for ${visa} applications in ${state}`;
            }
            applyUspsBtn.disabled = true;
            applyCourierBtn.disabled = true;
        }
        lbCard.style.display = 'block';
    }

    // Keep in globals to let carrier change switch addresses
    window.CURRENT_LOCKBOX_GROUP = group;
    window.CURRENT_LOCKBOX_ATTN = data ? (data.attn || '') : '';
    fillAddressFromLockbox();
}

// Make computeLockbox globally available
window.computeLockbox = computeLockbox;

document.getElementById('recipient_state').addEventListener('change', computeLockbox);

// When the modal opens, default carrier to USPS and compute once
document.getElementById('prepareShipmentModal').addEventListener('shown.bs.modal', function () {
    const carrierEl = document.getElementById('carrier');
    if (carrierEl && !carrierEl.value) carrierEl.value = 'USPS';
    // Reset state selection to trigger lockbox computation
    const stateEl = document.getElementById('recipient_state');
    if (stateEl && !stateEl.value) {
        stateEl.value = '';
    }
    updateServiceOptions();
    computeLockbox();
});

document.getElementById('carrier').addEventListener('change', function() {
    updateServiceOptions();
    fillAddressFromLockbox();
});

function fillAddressFromLockbox() {
    if (!window.CURRENT_LOCKBOX_GROUP) return;
    const carrier = (document.getElementById('carrier').value || '').toUpperCase();
    const useUsps = (carrier === 'USPS' || carrier === '');
    const addr = useUsps ? window.CURRENT_LOCKBOX_GROUP.usps : window.CURRENT_LOCKBOX_GROUP.courier;
    // Recipient name defaults to USCIS Attn
    const attn = window.CURRENT_LOCKBOX_ATTN ? `USCIS Attn: ${window.CURRENT_LOCKBOX_ATTN}` : (addr.recipient || 'USCIS');
    const nameEl = document.getElementById('recipient_name');
    const streetEl = document.getElementById('recipient_address');
    const cityEl = document.getElementById('recipient_city');
    const stateEl = document.getElementById('recipient_state');
    const zipEl = document.getElementById('recipient_zip');
    if (nameEl) nameEl.value = attn;
    if (streetEl) streetEl.value = addr.address || '';
    if (cityEl) cityEl.value = addr.city || '';
    if (stateEl && addr.state) stateEl.value = addr.state;
    if (zipEl) zipEl.value = addr.zip || '';
}

// Apply buttons to copy addresses explicitly
var applyUspsBtnEl = document.getElementById('applyUspsBtn');
if (applyUspsBtnEl) applyUspsBtnEl.addEventListener('click', function() {
    if (!window.CURRENT_LOCKBOX_GROUP) return;
    const data = window.CURRENT_LOCKBOX_GROUP.usps;
    applyAddress(data);
});

var applyCourierBtnEl = document.getElementById('applyCourierBtn');
if (applyCourierBtnEl) applyCourierBtnEl.addEventListener('click', function() {
    if (!window.CURRENT_LOCKBOX_GROUP) return;
    const data = window.CURRENT_LOCKBOX_GROUP.courier;
    applyAddress(data);
});

function applyAddress(addr) {
    const attn = window.CURRENT_LOCKBOX_ATTN ? `USCIS Attn: ${window.CURRENT_LOCKBOX_ATTN}` : (addr.recipient || 'USCIS');
    document.getElementById('recipient_name').value = attn;
    document.getElementById('recipient_address').value = addr.address || '';
    document.getElementById('recipient_city').value = addr.city || '';
    document.getElementById('recipient_state').value = addr.state || '';
    document.getElementById('recipient_zip').value = addr.zip || '';
}

// Handle form submission
document.getElementById('prepareShipmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('prepareShipmentModal')).hide();
            alert('Shipment prepared successfully! Tracking: ' + data.tracking_number);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while preparing shipment');
    });
});
</script>