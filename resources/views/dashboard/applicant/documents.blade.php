@extends('layouts.dashboard')

@section('title', 'Documents')
@section('page-title', 'Documents')

@section('styles')
<style>
  .app-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
  .app-card-header { background: #fff; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 14px; border-top-right-radius: 14px; }
  .chip { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: .15rem .45rem; border-radius: 8px; font-size: .68rem; font-weight: 700; }
  .pill { display: inline-block; padding: .25rem .55rem; border-radius: 999px; font-size: .7rem; font-weight: 600; }
  .pill-count { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
  .pill-status { background: #ebf5ff; color: #0b5ed7; border: 1px solid #b6dcff; text-transform: capitalize; }
  .doc-item { border-bottom: 1px dashed #e5e7eb; padding: .65rem 0; }
  .doc-item:last-child { border-bottom: 0; }
  .help { font-size: .8rem; color: #6b7280; }

    /* Mobile tweaks */
    @media (max-width: 576px) {
        .app-card-header { padding: .75rem 1rem !important; }
        .p-3.p-md-4 { padding: 1rem !important; }
        .doc-item { flex-wrap: wrap; gap: .4rem; }
        .doc-item > div:last-child { width: 100%; display: flex; justify-content: flex-start; }
        .btn.btn-sm { width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="app-card h-100">
            <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
                <strong>Pending Documents</strong>
                <span class="pill pill-count">{{ is_array($pendingDocuments) ? count($pendingDocuments) : 0 }}</span>
            </div>
            <div class="p-3 p-md-4">
                @if(!empty($pendingDocuments))
                    <div>
                        @foreach($pendingDocuments as $doc)
                            <div class="doc-item d-flex justify-content-between align-items-start">
                                <div>
                                    @php
                                        $label = is_array($doc) ? ($doc['label'] ?? ($doc['code'] ?? 'Document')) : (string) $doc;
                                        $code  = is_array($doc) ? ($doc['code'] ?? null) : null;
                                    @endphp
                                    <div>{{ $label }}</div>
                                    @if($code)
                                        <div class="mt-1"><span class="chip">{{ $code }}</span></div>
                                    @endif
                                </div>
                                @if($currentApplication)
                                    <a href="{{ route('dashboard.applicant.documents.upload', $currentApplication->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-upload me-1"></i>Upload</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">No pending documents.</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="app-card h-100">
            <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
                <strong>My Applications</strong>
            </div>
            <div class="p-3 p-md-4">
                    @foreach($applications as $app)
                    <div class="doc-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Case #{{ $app->id }} · {{ $app->visa_type ?? '—' }}</div>
                            <div class="help"><span class="pill pill-status">{{ str_replace('_',' ', $app->status ?? 'new') }}</span></div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard.applicant.documents.upload', $app->id) }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                            <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                    @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
