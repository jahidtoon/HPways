@extends('layouts.printing')

@section('title', 'Lockbox Search Engine')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search me-2"></i>
                        USCIS Lockbox Address Finder
                    </h3>
                    <p class="card-subtitle text-muted mb-0">
                        Find the correct mailing address for USCIS applications based on form type and applicant state
                    </p>
                </div>
                <div class="card-body">
                    <form id="lockboxSearchForm" class="row g-3">
                        <div class="col-md-6">
                            <label for="visa_type" class="form-label">Form/Visa Type</label>
                            <select id="visa_type" name="visa_type" class="form-select" required>
                                <option value="">Select Form Type</option>
                                @foreach($visaTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="state" class="form-label">Applicant State/Territory</label>
                            <select id="state" name="state" class="form-select" required>
                                <option value="">Select State</option>
                                @foreach($states as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Find Lockbox Address
                            </button>
                            <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
                                <i class="fas fa-times me-2"></i>Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row mt-4" id="resultsSection" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Mailing Addresses
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-mail-bulk me-2"></i>USPS Mail
                            </h5>
                            <div class="address-box p-3 bg-light rounded">
                                <pre id="uspsAddress" class="mb-0"></pre>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-success">
                                <i class="fas fa-shipping-fast me-2"></i>FedEx, UPS, DHL
                            </h5>
                            <div class="address-box p-3 bg-light rounded">
                                <pre id="courierAddress" class="mb-0"></pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Important:</strong> Always verify the current addresses on the USCIS website before mailing, as lockbox addresses may change.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Section -->
    <div class="row mt-4" id="errorSection" style="display: none;">
        <div class="col-12">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="errorMessage"></span>
            </div>
        </div>
    </div>
</div>

<style>
.address-box {
    border: 2px solid #e3e6f0;
    min-height: 120px;
}
.address-box pre {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.4;
    color: #333;
}
.card-subtitle {
    font-size: 0.9rem;
}
</style>

<script>
document.getElementById('lockboxSearchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Hide previous results/errors
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('errorSection').style.display = 'none';
    
    try {
        const response = await fetch('/printing/lockbox/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Show results
            document.getElementById('uspsAddress').textContent = result.usps_address;
            document.getElementById('courierAddress').textContent = result.courier_address;
            document.getElementById('resultsSection').style.display = 'block';
        } else {
            // Show error
            document.getElementById('errorMessage').textContent = result.error || 'An error occurred while searching.';
            document.getElementById('errorSection').style.display = 'block';
        }
    } catch (error) {
        document.getElementById('errorMessage').textContent = 'Network error occurred. Please try again.';
        document.getElementById('errorSection').style.display = 'block';
    }
});

function clearForm() {
    document.getElementById('lockboxSearchForm').reset();
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('errorSection').style.display = 'none';
}
</script>
@endsection
