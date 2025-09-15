@extends('layouts.dashboard')

@section('title', 'Support')
@section('page-title', 'Support')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="mb-3">Your Team</h6>
        <div class="row g-3">
            @foreach($userApplications as $app)
                <div class="col-md-6">
                    <div class="p-3 rounded border bg-light">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold">Case #{{ $app->id }}</div>
                                <div class="small text-muted">{{ $app->visa_type ?? '—' }}</div>
                            </div>
                            <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                        </div>
                        <hr>
                        <div class="small">Case Manager: <strong>{{ $app->caseManager->name ?? 'Not assigned' }}</strong></div>
                        <div class="small">Attorney: <strong>{{ $app->attorney->name ?? 'Not assigned' }}</strong></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
