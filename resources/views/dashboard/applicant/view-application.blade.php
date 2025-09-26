@extends('layouts.dashboard')

@section('title', 'Application Details')
@section('page-title', 'Application Details')

@section('styles')
<style>
    /* Cards: force white background and stronger shadow for contrast */
    .app-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
    .app-card-header { background: #ffffff; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 14px; border-top-right-radius: 14px; }

    /* Status badge with better contrast */
    .stat-badge { font-size: .72rem; border-radius: 999px; padding: .28rem .6rem; font-weight: 600; }
    .badge-status { background: #ebf5ff; color: #0b5ed7; border: 1px solid #b6dcff; letter-spacing: .2px; }

    .progress { background-color: #eef2f7; border-radius: 999px; }
    .progress-bar { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); }
    .meta-label { font-size: .78rem; color: #6b7280; }
    .meta-value { font-weight: 600; color: #111827; }

    /* Feedback: white background, subtle border, and colored left bar by type */
    .feedback-item { border: 1px solid #e5e7eb; border-radius: 12px; padding: .75rem .9rem; background: #ffffff; border-left: 3px solid #e5e7eb; }
    .feedback-type { font-size: .7rem; padding: .2rem .5rem; border-radius: 6px; }
    .type-general { background: #ecfeff; color: #0e7490; }
    .type-document_issue { background: #fff7ed; color: #c2410c; }
    .type-legal_advice { background: #eff6ff; color: #1d4ed8; }
    .type-rfe { background: #fef2f2; color: #b91c1c; }
    .feedback-item.type-general { border-left-color: #06b6d4; }
    .feedback-item.type-document_issue { border-left-color: #f59e0b; }
    .feedback-item.type-legal_advice { border-left-color: #3b82f6; }
    .feedback-item.type-rfe { border-left-color: #ef4444; }

    .card-title-sm { font-weight: 700; font-size: .95rem; }
    .truncate-none { white-space: normal; overflow: visible; text-overflow: initial; }
    .list-gap > * + * { margin-top: .65rem; }
    .kpi { background: #ffffff; border-radius: 12px; padding: .75rem; border: 1px solid #e5e7eb; }
    .kpi .label { font-size: .7rem; color: #6b7280; }
    .kpi .value { font-weight: 700; color: #111827; }

    /* Mobile tweaks */
    @media (max-width: 576px) {
        .app-card { border-radius: 12px; }
        .app-card-header { padding: .75rem 1rem !important; }
        .p-3.p-md-4 { padding: 1rem !important; }
        .d-flex.flex-wrap.gap-2.mt-3 > a { width: 100%; }
        .row.g-2 .col-md-4, .row.g-3 .col-md-6 { width: 100%; }
        .kpi { text-align: left; }
    }
</style>
@endsection

@section('content')
@if($application)
<div class="row g-3">
    <div class="col-lg-8">
        <div class="app-card h-100">
            <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="card-title-sm">Case #{{ $application->id }}</div>
                    <div class="meta-label">Submitted {{ optional($application->created_at)->format('M d, Y') }}</div>
                </div>
                <span class="stat-badge badge-status text-uppercase">{{ str_replace('_',' ', $application->status ?? 'new') }}</span>
            </div>
            <div class="p-3 p-md-4">
                @include('components.application.progress', ['application' => $application])
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="meta-label">Visa Type</div>
                        <div class="meta-value">{{ $application->visa_type ?? '—' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-label">Selected Package</div>
                        <div class="meta-value">{{ $application->selectedPackage->name ?? 'Not selected' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="meta-label">Progress</div>
                            <div class="meta-label">{{ (int)($application->progress_pct ?? 0) }}%</div>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ (int)($application->progress_pct ?? 0) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mt-3">
                    <div class="col-md-4">
                        <div class="kpi text-center">
                            <div class="label">Case ID</div>
                            <div class="value">#{{ $application->id }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="kpi text-center">
                            <div class="label">Status</div>
                            <div class="value text-capitalize">{{ str_replace('_',' ', $application->status ?? 'new') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="kpi text-center">
                            <div class="label">Last Updated</div>
                            <div class="value">{{ optional($application->updated_at)->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('applications.packages.index', $application->id) }}" class="btn btn-outline-primary btn-sm">Choose Package</a>
                    <a href="{{ route('dashboard.applicant.documents.upload', $application->id) }}" class="btn btn-outline-secondary btn-sm">Manage Documents</a>
                    @if($application->selectedPackage && !$application->payments()->where('status', 'succeeded')->exists())
                        <button onclick="initiatePayment({{ $application->id }})" class="btn btn-success btn-sm">
                            <i class="fas fa-credit-card me-1"></i>Pay Now - ${{ number_format($application->selectedPackage->price_cents / 100, 2) }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="app-card mb-3">
            <div class="app-card-header px-3 py-2"><strong>Team</strong></div>
            <div class="p-3">
                <div class="meta-label">Case Manager</div>
                <div class="meta-value mb-2">{{ $application->caseManager->name ?? 'Not assigned' }}</div>
                <div class="meta-label">Attorney</div>
                <div class="meta-value">{{ $application->attorney->name ?? 'Not assigned' }}</div>
            </div>
        </div>
        <div class="app-card mb-3">
            <div class="app-card-header px-3 py-2 d-flex justify-content-between align-items-center">
                <strong>Feedback</strong>
                @php $countFb = ($application->feedback?->count() ?? 0); @endphp
                <span class="stat-badge badge-soft">{{ $countFb }} item{{ $countFb === 1 ? '' : 's' }}</span>
            </div>
            <div class="p-3 list-gap">
                @forelse(($application->feedback ?? []) as $fb)
                    @php
                        $type = $fb->type ?? 'general';
                        $typeClass = match($type){
                            'document_issue' => 'type-document_issue',
                            'legal_advice' => 'type-legal_advice',
                            'rfe' => 'type-rfe',
                            default => 'type-general'
                        };
                        $typeLabel = ucwords(str_replace('_',' ', $type));
                    @endphp
                    <div class="feedback-item {{ $typeClass }}">
                        <div class="feedback-item {{ $typeClass }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="feedback-type {{ $typeClass }}">{{ $typeLabel }}</span>
                            @if(!empty($fb->created_at))
                                <span class="text-muted small" title="{{ optional($fb->created_at)->toDayDateTimeString() }}">
                                    {{ optional($fb->created_at)->format('M d, Y h:i A') }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 truncate-none">{{ $fb->content ?? $fb->notes ?? '' }}</div>
                        <div class="mt-2 text-muted small">— {{ $fb->user->name ?? 'Attorney' }}</div>
                    </div>
                @empty
                    <div class="text-muted small">No feedback yet.</div>
                @endforelse
            </div>
        </div>
        <div class="app-card">
            <div class="app-card-header px-3 py-2"><strong>Shipment</strong></div>
            <div class="p-3">
                <div class="meta-label">Tracking</div>
                <div class="meta-value">{{ $application->shipment->tracking_number ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="alert alert-warning">Application not found.</div>
@endif

<script>
function initiatePayment(applicationId) {
    // Show loading
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
    button.disabled = true;

    // Create payment intent
    fetch(`/applications/${applicationId}/payment-intent`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
            button.innerHTML = originalText;
            button.disabled = false;
            return;
        }

        // Redirect to Stripe Checkout
        if (data.checkout_url) {
            window.location.href = data.checkout_url;
        } else {
            alert('Payment URL not available');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Payment intent error:', error);
        alert('Failed to initiate payment');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endsection
