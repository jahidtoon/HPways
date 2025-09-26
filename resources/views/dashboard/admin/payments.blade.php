@extends('layouts.dashboard')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment History & Analytics
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Payments</h6>
                                            <h3 class="mb-0">{{ number_format($stats['total_payments']) }}</h3>
                                        </div>
                                        <i class="fas fa-credit-card fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Successful</h6>
                                            <h3 class="mb-0">{{ number_format($stats['successful_payments']) }}</h3>
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
                                            <h3 class="mb-0">{{ number_format($stats['pending_payments']) }}</h3>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Revenue</h6>
                                            <h3 class="mb-0">${{ number_format($stats['total_amount'], 2) }}</h3>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Payments</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Applicant</th>
                                            <th>Application</th>
                                            <th>Gateway</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                        <tr>
                                            <td>
                                                <code>#{{ $payment->id }}</code>
                                            </td>
                                            <td>
                                                @if($payment->application && $payment->application->user)
                                                    <div>
                                                        <strong>{{ $payment->application->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $payment->application->user->email }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->application)
                                                    <a href="{{ route('admin.applications.show', $payment->application->id) }}">
                                                        #{{ $payment->application->id }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $payment->application->visa_type }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fab fa-{{ $payment->provider }} me-1"></i>
                                                    {{ ucfirst($payment->provider) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($payment->amount_cents / 100, 2) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ strtoupper($payment->currency) }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'warning',
                                                        'succeeded' => 'success',
                                                        'failed' => 'danger',
                                                        'refunded' => 'info'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $payment->created_at->format('M d, Y H:i') }}
                                                @if($payment->paid_at)
                                                    <br>
                                                    <small class="text-success">Paid: {{ $payment->paid_at->format('M d, Y H:i') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="viewPaymentDetails({{ $payment->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($payment->status === 'pending')
                                                        <button class="btn btn-sm btn-outline-warning" onclick="retryPayment({{ $payment->id }})">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                    @if($payment->status === 'succeeded')
                                                        <button class="btn btn-sm btn-outline-danger" onclick="refundPayment({{ $payment->id }})">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted p-4">
                                                <i class="fas fa-credit-card fa-3x mb-3 text-muted"></i>
                                                <br>
                                                No payments found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($payments->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $payments->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewPaymentDetails(paymentId) {
    // Load payment details via AJAX
    fetch(`/admin/payments/${paymentId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('paymentDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Provider:</strong></td><td>${data.provider}</td></tr>
                            <tr><td><strong>Reference:</strong></td><td>${data.provider_ref}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>$${data.amount}</td></tr>
                            <tr><td><strong>Status:</strong></td><td>${data.status}</td></tr>
                            <tr><td><strong>Created:</strong></td><td>${data.created_at}</td></tr>
                            ${data.paid_at ? `<tr><td><strong>Paid At:</strong></td><td>${data.paid_at}</td></tr>` : ''}
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Payload Data</h6>
                        <pre class="bg-light p-2 rounded"><code>${JSON.stringify(data.payload, null, 2)}</code></pre>
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
        })
        .catch(error => {
            alert('Error loading payment details');
        });
}

function retryPayment(paymentId) {
    if (confirm('Retry this payment?')) {
        // Implement retry logic
        alert('Retry functionality not implemented yet');
    }
}

function refundPayment(paymentId) {
    if (confirm('Are you sure you want to refund this payment?')) {
        // Implement refund logic
        alert('Refund functionality not implemented yet');
    }
}
</script>
@endsection