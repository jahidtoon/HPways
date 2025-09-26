@extends('layouts.dashboard')

@section('title', 'Payment Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment Gateway Settings
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

                    <div class="row">
                        @foreach($paymentSettings as $setting)
                        <div class="col-md-6 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fab fa-{{ $setting->gateway }} me-2"></i>
                                            {{ ucfirst($setting->gateway) }}
                                        </h5>
                                        <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $setting->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.payment-settings.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="gateway" value="{{ $setting->gateway }}">

                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       name="is_active" value="1"
                                                       {{ $setting->is_active ? 'checked' : '' }}
                                                       id="active_{{ $setting->gateway }}">
                                                <label class="form-check-label" for="active_{{ $setting->gateway }}">
                                                    Enable {{ ucfirst($setting->gateway) }} Payments
                                                </label>
                                            </div>
                                        </div>

                                        @if($setting->gateway === 'stripe')
                                            <div class="mb-3">
                                                <label class="form-label">Publishable Key</label>
                                                <input type="text" class="form-control"
                                                       name="credentials[publishable_key]"
                                                       value="{{ $setting->credentials['publishable_key'] ?? '' }}"
                                                       placeholder="pk_test_...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Secret Key</label>
                                                <input type="password" class="form-control"
                                                       name="credentials[secret_key]"
                                                       value="{{ $setting->credentials['secret_key'] ?? '' }}"
                                                       placeholder="sk_test_...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Webhook Secret</label>
                                                <input type="password" class="form-control"
                                                       name="credentials[webhook_secret]"
                                                       value="{{ $setting->credentials['webhook_secret'] ?? '' }}"
                                                       placeholder="whsec_...">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mode</label>
                                                <select class="form-select" name="settings[mode]">
                                                    <option value="test" {{ ($setting->settings['mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Test</option>
                                                    <option value="live" {{ ($setting->settings['mode'] ?? 'test') === 'live' ? 'selected' : '' }}>Live</option>
                                                </select>
                                            </div>
                                        @elseif($setting->gateway === 'paypal')
                                            <div class="mb-3">
                                                <label class="form-label">Client ID</label>
                                                <input type="text" class="form-control"
                                                       name="credentials[client_id]"
                                                       value="{{ $setting->credentials['client_id'] ?? '' }}"
                                                       placeholder="Your PayPal Client ID">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Client Secret</label>
                                                <input type="password" class="form-control"
                                                       name="credentials[client_secret]"
                                                       value="{{ $setting->credentials['client_secret'] ?? '' }}"
                                                       placeholder="Your PayPal Client Secret">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Mode</label>
                                                <select class="form-select" name="settings[mode]">
                                                    <option value="sandbox" {{ ($setting->settings['mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                                    <option value="live" {{ ($setting->settings['mode'] ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live</option>
                                                </select>
                                            </div>
                                        @endif

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Save Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Payment History</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Application</th>
                                                    <th>Gateway</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $recentPayments = \App\Models\Payment::with('application.user')
                                                        ->orderBy('created_at', 'desc')
                                                        ->limit(10)
                                                        ->get();
                                                @endphp
                                                @forelse($recentPayments as $payment)
                                                <tr>
                                                    <td>
                                                        @if($payment->application)
                                                            <a href="{{ route('admin.application-detail', $payment->application->id) }}">
                                                                #{{ $payment->application->id }}
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">{{ $payment->application->user->name ?? 'Unknown' }}</small>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ ucfirst($payment->provider) }}</td>
                                                    <td>${{ number_format($payment->amount_cents / 100, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $payment->status === 'succeeded' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No payments found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection