@extends('layouts.dashboard')

@section('title', 'Documents')
@section('page-title', 'Documents')

@section('content')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Pending Documents</strong>
                <span class="badge bg-light text-muted">{{ is_array($pendingDocuments) ? count($pendingDocuments) : 0 }}</span>
            </div>
            <div class="card-body">
                @if(!empty($pendingDocuments))
                    <ul class="list-group list-group-flush">
                        @foreach($pendingDocuments as $doc)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    @php
                                        $label = is_array($doc) ? ($doc['label'] ?? ($doc['code'] ?? 'Document')) : (string) $doc;
                                        $code  = is_array($doc) ? ($doc['code'] ?? null) : null;
                                    @endphp
                                    {{ $label }}
                                    @if($code)
                                        <span class="badge bg-light text-muted ms-2">{{ $code }}</span>
                                    @endif
                                </span>
                                @if($currentApplication)
                                    <a href="{{ route('dashboard.applicant.documents.upload', $currentApplication->id) }}" class="btn btn-sm btn-outline-primary">Upload</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted">No pending documents.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>My Applications</strong></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($applications as $app)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Case #{{ $app->id }} · {{ $app->visa_type ?? '—' }}</div>
                                <div class="small text-muted">{{ $app->status ?? 'new' }}</div>
                            </div>
                            <a href="{{ route('dashboard.applicant.documents.upload', $app->id) }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
