@extends('layouts.dashboard')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="mb-3">Application Summary</h6>
        <ul class="list-group list-group-flush">
            @foreach($applications as $app)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Case #{{ $app->id }} · {{ $app->visa_type ?? '—' }}</div>
                        <div class="small text-muted">Docs: {{ $app->documents->count() }} · Payments: {{ $app->payments->count() }}</div>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('dashboard.applicant.application.view', $app->id) }}">Open</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
