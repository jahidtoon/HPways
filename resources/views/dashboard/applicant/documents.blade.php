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
    <div class="col-lg-7">
        <div class="app-card h-100">
            <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
                <strong>Uploaded Documents</strong>
                <span class="pill pill-count">{{ is_array($uploadedDocuments ?? null) ? count($uploadedDocuments) : ($currentApplication?->documents?->count() ?? 0) }}</span>
            </div>
            <div class="p-3 p-md-4">
                @if(!empty($uploadedDocuments))
                    @foreach($uploadedDocuments as $doc)
                        <div class="doc-item d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $doc['label'] }}</div>
                                <div class="mt-1 d-flex align-items-center gap-2">
                                    <span class="chip">{{ $doc['code'] }}</span>
                                    @php
                                        $status = strtolower($doc['status'] ?? 'pending');
                                        $badgeClass = match($status){
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'pending' => 'bg-warning text-dark',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                                    @if(($doc['count'] ?? 1) > 1)
                                        <span class="pill pill-count" title="Multiple uploads detected">x{{ $doc['count'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($doc['latest_name']))
                                    <div class="help mt-1">Latest: {{ $doc['latest_name'] }} • {{ $doc['created_at'] }}</div>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/view-document.php?id={{ $doc['latest_id'] }}&action=download" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-eye me-1"></i>Open</a>
                                <a href="/view-document.php?id={{ $doc['latest_id'] }}&action=download&download=1" class="btn btn-sm btn-outline-secondary"><i class="fas fa-download me-1"></i>Download</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-muted">No documents uploaded yet.</div>
                @endif
                <div class="alert alert-info mt-3">
                    Note: If you re-upload the same document type, the latest file will be shown here. Attorneys will review and mark each document as <strong>approved</strong> or <strong>rejected</strong>.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
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
                            <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
