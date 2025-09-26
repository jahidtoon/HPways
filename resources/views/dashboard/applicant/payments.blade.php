@extends('layouts.dashboard')

@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2 text-primary"></i>Payment Information
                        </h5>
                        <div>
                            @if($payments->count() > 0)
                                <span class="badge bg-success me-1">{{ $payments->count() }} completed</span>
                            @endif
                            @if(isset($pendingPayments) && $pendingPayments->count() > 0)
                                <span class="badge bg-warning">{{ $pendingPayments->count() }} pending</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pending Payments Section -->
                    @if(isset($pendingPayments) && $pendingPayments->count() > 0)
                        <div class="alert alert-warning border-warning">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>Pending Payments
                            </h6>
                            <p class="mb-2">You have selected packages for the following applications but haven't completed payment yet:</p>
                            <div class="row g-3">
                                @foreach($pendingPayments as $application)
                                    <div class="col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="card-title mb-1">{{ $application->visa_type }} Application</h6>
                                                        <small class="text-muted">App #{{ $application->id }}</small>
                                                    </div>
                                                    <span class="badge bg-warning">Pending</span>
                                                </div>
                                                @if($application->selectedPackage)
                                                    <div class="mb-2">
                                                        <strong>Package:</strong> {{ $application->selectedPackage->name }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Amount:</strong>
                                                        <span class="text-success fw-bold fs-5">${{ number_format($application->selectedPackage->price_cents / 100, 2) }}</span>
                                                    </div>
                                                    <a href="{{ route('dashboard.applicant.application.view', $application->id) }}" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-credit-card me-1"></i>Pay Now
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Completed Payments Section -->
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Application</th>
                                        <th>Gateway</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>
                                            <code class="small">#{{ $payment->id }}</code>
                                        </td>
                                        <td>
                                            @if($payment->application)
                                                <div>
                                                    <strong>{{ $payment->application->visa_type }}</strong>
                                                    <br>
                                                    <small class="text-muted">App #{{ $payment->application->id }}</small>
                                                </div>
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
                                            <div class="fw-bold text-success">
                                                ${{ number_format($payment->amount_cents / 100, 2) }}
                                            </div>
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
                                                $statusIcons = [
                                                    'pending' => 'clock',
                                                    'succeeded' => 'check-circle',
                                                    'failed' => 'times-circle',
                                                    'refunded' => 'undo'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                                <i class="fas fa-{{ $statusIcons[$payment->status] ?? 'question' }} me-1"></i>
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>{{ $payment->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                            @if($payment->paid_at)
                                                <br>
                                                <small class="text-success">Paid: {{ $payment->paid_at->format('M d, Y h:i A') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->status === 'succeeded')
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewPaymentDetails({{ $payment->id }})">
                                                    <i class="fas fa-eye"></i> Details
                                                </button>
                                            @elseif($payment->status === 'pending')
                                                <button class="btn btn-sm btn-warning" onclick="retryPayment({{ $payment->id }})">
                                                    <i class="fas fa-redo"></i> Retry
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment Summary -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-success">
                                        ${{ number_format($payments->where('status', 'succeeded')->sum('amount_cents') / 100, 2) }}
                                    </div>
                                    <small class="text-muted">Total Paid</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-warning">
                                        {{ $payments->where('status', 'pending')->count() }}
                                    </div>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-success">
                                        {{ $payments->where('status', 'succeeded')->count() }}
                                    </div>
                                    <small class="text-muted">Successful</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-danger">
                                        {{ $payments->where('status', 'failed')->count() }}
                                    </div>
                                    <small class="text-muted">Failed</small>
                                </div>
                            </div>
                        </div>
                    @else
                        @if(!isset($pendingPayments) || $pendingPayments->count() == 0)
                            <div class="text-center py-5">
                                <i class="fas fa-credit-card text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">No Payment Information</h5>
                                <p class="text-muted">You haven't selected any packages or made any payments yet.</p>
                                @if($currentApplication ?? false)
                                    <a href="{{ route('dashboard.applicant.application.view', $currentApplication->id) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>View Your Application
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
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
    // This would typically make an AJAX call to get payment details
    alert('Payment details for ID: ' + paymentId);
}

function retryPayment(paymentId) {
    if (confirm('Are you sure you want to retry this payment?')) {
        // This would typically make an AJAX call to retry the payment
        alert('Retrying payment ID: ' + paymentId);
    }
}
</script>
@endsection
