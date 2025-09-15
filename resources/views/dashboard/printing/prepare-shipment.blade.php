@extends('layouts.dashboard')

@section('title', 'Prepare Shipment')
@section('page-title', 'Prepare Shipment')
@section('sidebar')
    @parent
@endsection
@section('styles')
<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
    }
    .shipment-hero {
        background: rgba(28, 200, 138, 0.92);
        color: #fff;
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.18);
    }
    .shipment-hero .content {
        position: relative;
        z-index: 1;
    }
    .card-glass {
        background: rgba(255,255,255,0.92);
        border-radius: 1.25rem;
        box-shadow: 0 4px 24px rgba(78,115,223,0.10);
        backdrop-filter: blur(4px);
        border: 1px solid #e0e7ff;
        margin-bottom: 1.5rem;
    }
    .document-list {
        max-height: 300px;
        overflow-y: auto;
    }
    .document-item {
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .document-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .address-box {
        background-color: #f8f9fc;
        padding: 1.25rem;
        border-radius: 0.75rem;
        border: 1px dashed #d1d3e2;
    }
    .shipping-option {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .shipping-option:hover {
        background-color: #f8f9fc;
        border-color: #4e73df;
    }
    .shipping-option.selected {
        background-color: #e8f0fe;
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
    }
    .shipping-option .shipping-logo {
        width: 60px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #4e73df;
    }
    .shipping-rate {
        font-weight: 600;
        color: #2e59d9;
    }
</style>
@endsection
@section('content')
<div class="shipment-hero">
    <div class="content">
        <h2 class="mb-2">Prepare Shipment</h2>
        <p class="lead mb-0">Package and ship documents for Application #{{ $application->id }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Shipping Form -->
        <div class="card card-glass mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-box text-success me-2"></i>Package Information</h5>
            </div>
            <div class="card-body">
                <form id="shippingForm" action="{{ route('printing.update-shipping', $application->id) }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Applicant Details</h6>
                            <p><strong>Name:</strong> {{ $application->applicant_name }}</p>
                            <p><strong>Email:</strong> {{ $application->applicant_email }}</p>
                            <p><strong>Phone:</strong> {{ $application->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Shipping Address</h6>
                            <div class="address-box">
                                {{ $application->shipping_address }}
                                <input type="hidden" name="shipping_address" value="{{ $application->shipping_address }}">
                                <input type="hidden" name="applicant_email" value="{{ $application->applicant_email }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold mb-3">Package Contents</h6>
                            <div class="document-list p-3 bg-light rounded">
                                @foreach($application->documents as $document)
                                <div class="document-item d-flex align-items-center p-2 mb-2 bg-white rounded">
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                    <span>{{ $document }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_weight" class="form-label">Package Weight (lbs)</label>
                                <input type="number" class="form-control" id="package_weight" name="package_weight" value="0.5" step="0.1" min="0.1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="package_type" class="form-label">Package Type</label>
                                <select class="form-select" id="package_type" name="package_type" required>
                                    <option value="envelope">Envelope</option>
                                    <option value="small_box">Small Box</option>
                                    <option value="medium_box">Medium Box</option>
                                    <option value="large_box">Large Box</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_carrier" class="form-label">Shipping Carrier</label>
                        <select class="form-select" id="shipping_carrier" name="shipping_carrier" required>
                            <option value="">Select a carrier</option>
                            @foreach($shippingCarriers as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="shippingRatesContainer" class="mb-3 d-none">
                        <label class="form-label">Shipping Options</label>
                        <div id="shippingOptions">
                            <!-- Shipping options will be loaded here -->
                            <div class="text-center p-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading shipping rates...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estimated_delivery" class="form-label">Estimated Delivery Date</label>
                                <input type="date" class="form-control" id="estimated_delivery" name="estimated_delivery" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="shipping_notes" class="form-label">Shipping Notes</label>
                        <textarea class="form-control" id="shipping_notes" name="shipping_notes" rows="3" placeholder="Enter any special instructions or notes for this shipment"></textarea>
                    </div>
                    
                    <input type="hidden" name="shipping_method" id="shipping_method" value="">
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('printing.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-shipping-fast me-1"></i> Process Shipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Shipping Preview -->
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-truck text-success me-2"></i>Shipping Preview</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="shipping-label p-4 border rounded mx-auto" style="max-width: 300px;">
                        <div class="mb-3">
                            <div class="fw-bold">SHIP TO:</div>
                            <div>{{ $application->applicant_name }}</div>
                            <div style="white-space: pre-line;">{{ $application->shipping_address }}</div>
                        </div>
                        <div class="mb-3 text-start">
                            <div><strong>Package:</strong> <span id="previewPackageType">Envelope</span></div>
                            <div><strong>Weight:</strong> <span id="previewWeight">0.5</span> lbs</div>
                        </div>
                        <div class="text-start">
                            <div class="fw-bold">Carrier: <span id="previewCarrier">-</span></div>
                            <div class="fw-bold">Service: <span id="previewService">-</span></div>
                        </div>
                        <div class="mt-3">
                            <div class="tracking-number fw-bold" id="previewTracking">Tracking # will appear here</div>
                        </div>
                    </div>
                </div>
                
                <div class="shipping-summary p-3 bg-light rounded mb-3">
                    <h6 class="border-bottom pb-2 mb-3">Shipping Summary</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Carrier:</span>
                        <span id="summaryCarrier" class="fw-bold">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service:</span>
                        <span id="summaryService" class="fw-bold">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estimated Delivery:</span>
                        <span id="summaryDelivery" class="fw-bold">-</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-primary">
                        <span>Total Cost:</span>
                        <span id="summaryRate">$0.00</span>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    The applicant will be notified via email once the package has been shipped with tracking information.
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingForm = document.getElementById('shippingForm');
        const carrierSelect = document.getElementById('shipping_carrier');
        const packageWeight = document.getElementById('package_weight');
        const shippingRatesContainer = document.getElementById('shippingRatesContainer');
        const shippingOptions = document.getElementById('shippingOptions');
        const shippingMethodInput = document.getElementById('shipping_method');
        const trackingNumberInput = document.getElementById('tracking_number');
        const estimatedDeliveryInput = document.getElementById('estimated_delivery');
        const packageTypeSelect = document.getElementById('package_type');
        
        // Preview elements
        const previewPackageType = document.getElementById('previewPackageType');
        const previewWeight = document.getElementById('previewWeight');
        const previewCarrier = document.getElementById('previewCarrier');
        const previewService = document.getElementById('previewService');
        const previewTracking = document.getElementById('previewTracking');
        
        // Summary elements
        const summaryCarrier = document.getElementById('summaryCarrier');
        const summaryService = document.getElementById('summaryService');
        const summaryDelivery = document.getElementById('summaryDelivery');
        const summaryRate = document.getElementById('summaryRate');
        
        // Update package type in preview
        packageTypeSelect.addEventListener('change', function() {
            const packageTypeLabels = {
                'envelope': 'Envelope',
                'small_box': 'Small Box',
                'medium_box': 'Medium Box',
                'large_box': 'Large Box'
            };
            previewPackageType.textContent = packageTypeLabels[this.value] || this.value;
        });
        
        // Update weight in preview
        packageWeight.addEventListener('input', function() {
            previewWeight.textContent = this.value;
        });
        
        // Update tracking number in preview
        trackingNumberInput.addEventListener('input', function() {
            previewTracking.textContent = this.value ? `Tracking # ${this.value}` : 'Tracking # will appear here';
        });
        
        // Handle carrier selection and load shipping rates
        carrierSelect.addEventListener('change', function() {
            const carrier = this.value;
            
            if (!carrier) {
                shippingRatesContainer.classList.add('d-none');
                return;
            }
            
            // Update carrier in preview
            previewCarrier.textContent = this.options[this.selectedIndex].text;
            summaryCarrier.textContent = this.options[this.selectedIndex].text;
            
            // Show rates container
            shippingRatesContainer.classList.remove('d-none');
            
            // Generate random tracking number based on carrier
            let trackingPrefix = '';
            switch(carrier) {
                case 'fedex':
                    trackingPrefix = 'FDX';
                    break;
                case 'ups':
                    trackingPrefix = 'UPS';
                    break;
                case 'dhl':
                    trackingPrefix = 'DHL';
                    break;
                case 'usps':
                    trackingPrefix = 'USPS';
                    break;
            }
            
            const randomNum = Math.floor(10000000 + Math.random() * 90000000);
            trackingNumberInput.value = `${trackingPrefix}${randomNum}`;
            previewTracking.textContent = `Tracking # ${trackingNumberInput.value}`;
            
            // In a real app, this would make an API call to get shipping rates
            // For this demo, we'll simulate it with dummy data
            
            setTimeout(() => {
                let rates = [];
                
                switch(carrier) {
                    case 'fedex':
                        rates = [
                            { service: 'FedEx Ground', rate: 12.99, delivery_estimate: '3-5 business days' },
                            { service: 'FedEx 2Day', rate: 24.99, delivery_estimate: '2 business days' },
                            { service: 'FedEx Priority Overnight', rate: 39.99, delivery_estimate: 'Next business day' }
                        ];
                        break;
                        
                    case 'ups':
                        rates = [
                            { service: 'UPS Ground', rate: 11.99, delivery_estimate: '1-5 business days' },
                            { service: 'UPS 3 Day Select', rate: 22.99, delivery_estimate: '3 business days' },
                            { service: 'UPS Next Day Air', rate: 44.99, delivery_estimate: 'Next business day' }
                        ];
                        break;
                        
                    case 'dhl':
                        rates = [
                            { service: 'DHL Express', rate: 29.99, delivery_estimate: '1-3 business days' },
                            { service: 'DHL International', rate: 49.99, delivery_estimate: '3-7 business days' }
                        ];
                        break;
                        
                    case 'usps':
                        rates = [
                            { service: 'USPS First Class', rate: 7.99, delivery_estimate: '3-5 business days' },
                            { service: 'USPS Priority', rate: 13.99, delivery_estimate: '1-3 business days' },
                            { service: 'USPS Priority Express', rate: 26.99, delivery_estimate: '1-2 business days' }
                        ];
                        break;
                }
                
                displayShippingRates(rates);
            }, 1000);
        });
        
        // Display shipping rates
        function displayShippingRates(rates) {
            shippingOptions.innerHTML = '';
            
            rates.forEach((rate, index) => {
                const option = document.createElement('div');
                option.className = 'shipping-option' + (index === 0 ? ' selected' : '');
                option.dataset.service = rate.service;
                option.dataset.rate = rate.rate.toFixed(2);
                option.dataset.delivery = rate.delivery_estimate;
                
                let carrierLogo = '';
                if (carrierSelect.value === 'fedex') carrierLogo = 'F';
                if (carrierSelect.value === 'ups') carrierLogo = 'U';
                if (carrierSelect.value === 'dhl') carrierLogo = 'D';
                if (carrierSelect.value === 'usps') carrierLogo = 'US';
                
                option.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="shipping-logo me-3 bg-light rounded">
                                ${carrierLogo}
                            </div>
                            <div>
                                <div class="fw-bold">${rate.service}</div>
                                <div class="text-muted small">${rate.delivery_estimate}</div>
                            </div>
                        </div>
                        <div class="shipping-rate">$${rate.rate.toFixed(2)}</div>
                    </div>
                `;
                
                shippingOptions.appendChild(option);
                
                // If first option, select it by default
                if (index === 0) {
                    shippingMethodInput.value = rate.service;
                    previewService.textContent = rate.service;
                    summaryService.textContent = rate.service;
                    summaryRate.textContent = `$${rate.rate.toFixed(2)}`;
                    
                    // Set estimated delivery date
                    let days = 3; // Default
                    if (rate.delivery_estimate.includes('Next')) days = 1;
                    else if (rate.delivery_estimate.includes('2')) days = 2;
                    else if (rate.delivery_estimate.includes('3-5')) days = 4;
                    else if (rate.delivery_estimate.includes('3-7')) days = 5;
                    
                    const deliveryDate = new Date();
                    deliveryDate.setDate(deliveryDate.getDate() + days);
                    
                    const formattedDate = deliveryDate.toISOString().split('T')[0];
                    estimatedDeliveryInput.value = formattedDate;
                    summaryDelivery.textContent = deliveryDate.toLocaleDateString();
                }
                
                // Add click event to select shipping option
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    document.querySelectorAll('.shipping-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Update hidden input with selected service
                    shippingMethodInput.value = this.dataset.service;
                    
                    // Update preview and summary
                    previewService.textContent = this.dataset.service;
                    summaryService.textContent = this.dataset.service;
                    summaryRate.textContent = `$${this.dataset.rate}`;
                    
                    // Set estimated delivery date based on delivery estimate
                    let days = 3; // Default
                    if (this.dataset.delivery.includes('Next')) days = 1;
                    else if (this.dataset.delivery.includes('2')) days = 2;
                    else if (this.dataset.delivery.includes('3-5')) days = 4;
                    else if (this.dataset.delivery.includes('3-7')) days = 5;
                    
                    const deliveryDate = new Date();
                    deliveryDate.setDate(deliveryDate.getDate() + days);
                    
                    const formattedDate = deliveryDate.toISOString().split('T')[0];
                    estimatedDeliveryInput.value = formattedDate;
                    summaryDelivery.textContent = deliveryDate.toLocaleDateString();
                });
            });
        }
        
        // Handle form submission
        shippingForm.addEventListener('submit', function(e) {
            // In a real app, this would submit to the server
            // For this demo, we'll just validate
            
            if (!carrierSelect.value) {
                e.preventDefault();
                alert('Please select a shipping carrier');
                return;
            }
            
            if (!shippingMethodInput.value) {
                e.preventDefault();
                alert('Please select a shipping method');
                return;
            }
            
            if (!trackingNumberInput.value) {
                e.preventDefault();
                alert('Please enter a tracking number');
                return;
            }
            
            if (!estimatedDeliveryInput.value) {
                e.preventDefault();
                alert('Please enter an estimated delivery date');
                return;
            }
            
            // All validations passed, form will submit
        });
    });
</script>
@endsection
@endsection
