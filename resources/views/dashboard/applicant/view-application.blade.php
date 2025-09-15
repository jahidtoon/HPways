@extends('layouts.dashboard')

@section('title', 'Application Details')
@section('page-title', 'Application Details')

@section('content')
@if($application)
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Case #{{ $application->id }}</strong>
                <span class="badge bg-secondary">{{ $application->status ?? 'new' }}</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-muted">Visa Type</div>
                    <div class="fw-semibold">{{ $application->visa_type ?? '—' }}</div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">Selected Package</div>
                    <div class="fw-semibold">{{ $application->selectedPackage->name ?? 'Not selected' }}</div>
                </div>
                <div class="mb-3">
                    <div class="small text-muted">Progress</div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ (int)($application->progress_pct ?? 0) }}%"></div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('applications.packages.index', $application->id) }}" class="btn btn-outline-success btn-sm">Choose Package</a>
                    <a href="{{ route('dashboard.applicant.documents.upload', $application->id) }}" class="btn btn-outline-secondary btn-sm">Manage Documents</a>
                    <a href="{{ route('applications.payments.list', $application->id) }}" class="btn btn-outline-dark btn-sm">Payments</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Team</strong></div>
            <div class="card-body">
                <div class="small">Case Manager: <strong>{{ $application->caseManager->name ?? 'Not assigned' }}</strong></div>
                <div class="small">Attorney: <strong>{{ $application->attorney->name ?? 'Not assigned' }}</strong></div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Feedback</strong></div>
            <div class="card-body">
                @forelse(($application->feedback ?? []) as $fb)
                    <div class="mb-2 p-2 bg-light rounded small">{{ $fb->content ?? $fb->notes ?? '' }}</div>
                @empty
                    <div class="text-muted small">No feedback yet.</div>
                @endforelse
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Shipment</strong></div>
            <div class="card-body">
                <div class="small">Tracking: <strong>{{ $application->shipment->tracking_number ?? '—' }}</strong></div>
            </div>
        </div>
    </div>
</div>
@else
    <div class="alert alert-warning">Application not found.</div>
@endif
@endsection
