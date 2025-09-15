@extends('layouts.dashboard')

@section('title', 'Applicant Dashboard')
@section('page-title', 'Applicant Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome / Status Row -->
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="badge rounded-pill bg-primary-subtle text-primary">Welcome</span>
                        </div>
                        <h5 class="mb-0">Hi, {{ $user->name ?? 'Applicant' }}!</h5>
                    </div>
                    <p class="text-muted mt-2 mb-0">Track your progress, upload documents, and manage your application here.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Current Visa Type</strong>
                        @if($currentApplication?->visa_type)
                            <span class="badge bg-info">{{ $currentApplication->visa_type }}</span>
                        @else
                            <a href="{{ route('eligibility.quiz') }}" class="btn btn-sm btn-outline-primary">Take Quiz</a>
                        @endif
                    </div>
                    <div>
                        <small class="text-muted">Use the eligibility quiz to refine your package options.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Applications</div>
                            <div class="fs-4 fw-bold">{{ $applications->count() }}</div>
                        </div>
                        <i class="fa-solid fa-clipboard-list text-primary fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Pending Docs</div>
                            <div class="fs-4 fw-bold">{{ is_array($pendingDocuments) ? count($pendingDocuments) : 0 }}</div>
                        </div>
                        <i class="fa-solid fa-file-circle-question text-warning fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Payments</div>
                            <div class="fs-4 fw-bold">{{ $hasPayments ? 'Up to date' : 'Due' }}</div>
                        </div>
                        <i class="fa-solid fa-credit-card text-success fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Tracking</div>
                            <div class="fs-6 fw-semibold">{{ $trackingInfo ? $trackingInfo : 'N/A' }}</div>
                        </div>
                        <i class="fa-solid fa-truck-fast text-info fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Application Summary + Actions -->
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>Current Application</strong>
                </div>
                <div class="card-body">
                    @if($currentApplication)
                        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                            <span class="badge bg-secondary">ID #{{ $currentApplication->id }}</span>
                            <span class="badge bg-primary">{{ strtoupper($currentApplication->status ?? 'new') }}</span>
                            @if($currentApplication->selectedPackage)
                                <span class="badge bg-success">Package: {{ $currentApplication->selectedPackage->name }}</span>
                            @endif
                            @if($currentApplication->progress_pct !== null)
                                <span class="badge bg-info">Progress: {{ $currentApplication->progress_pct }}%</span>
                            @endif
                        </div>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ (int)($currentApplication->progress_pct ?? 0) }}%"></div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard.applicant.application.view', $currentApplication->id) }}" class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                            <a href="{{ route('dashboard.applicant.documents.upload', $currentApplication->id) }}" class="btn btn-outline-secondary btn-sm">
                                Upload Documents
                            </a>
                            <a href="{{ route('applications.packages.index', $currentApplication->id) }}" class="btn btn-outline-success btn-sm">
                                Choose Package
                            </a>
                            <a href="{{ route('applications.payments.list', $currentApplication->id) }}" class="btn btn-outline-dark btn-sm">
                                Payments
                            </a>
                        </div>
                    @else
                        <p class="text-muted mb-3">You don’t have an application yet.</p>
                        <a href="{{ route('eligibility.quiz') }}" class="btn btn-primary">Start with Eligibility Quiz</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Recent Attorney Feedback</strong>
                    <span class="badge bg-light text-muted">Last 5</span>
                </div>
                <div class="card-body">
            @forelse($recentFeedback as $fb)
                        <div class="mb-3 p-2 rounded bg-light">
                            <div class="small text-muted">Case #{{ $fb->application_id }} · {{ optional($fb->created_at)->diffForHumans() }}</div>
                <div>{{ $fb->content ?? \Illuminate\Support\Str::limit($fb->notes ?? '', 120) }}</div>
                        </div>
                    @empty
                        <div class="text-muted">No feedback yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <strong>My Applications</strong>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Visa</th>
                        <th>Status</th>
                        <th>Package</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>#{{ $app->id }}</td>
                        <td>{{ $app->visa_type ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $app->status ?? 'new' }}</span></td>
                        <td>{{ $app->selectedPackage->name ?? '—' }}</td>
                        <td>{{ optional($app->created_at)->format('M d, Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-primary">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted p-4">No applications yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
