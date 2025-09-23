@extends('layouts.dashboard')
@section('title', 'Application Documents')
@section('page-title', 'Application Documents')

@section('styles')
<style>
    .document-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fff; }
    .document-item.uploaded { border-color: #10b981; background: #f0fdf4; }
    .document-item.missing { border-color: #ef4444; background: #fef2f2; }
    .status-badge { border-radius: 10px; padding: 2px 8px; font-size: 12px; }

    /* Hide global sidebar for this view and expand content */
    aside.sidebar { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; padding-left: 1rem !important; padding-right: 1rem !important; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Application #APP-{{ $application->id }}</h2>
                <small class="text-muted">Applicant: {{ $application->user->name ?? 'Unknown' }} · Visa: {{ strtoupper($application->visa_type ?? '-') }} · Status: {{ str_replace('_',' ', $application->status) }}</small>
            </div>
            <a href="{{ route('dashboard.printing-department.index') }}" class="btn btn-secondary">Back to Printing Dashboard</a>
        </div>
    </div>
    @include('components.application.progress', ['application' => $application])

    <div class="card mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Document Status</h5>
            @php
                $requiredCodes = collect($requiredDocuments ?? [])->pluck('code')->filter()->values();
                $uploadedCodes = collect($application->documents ?? [])->pluck('type')->filter()->unique();
                $submittedCount = $uploadedCodes->intersect($requiredCodes)->count();
            @endphp
            <span class="badge bg-light text-dark">{{ $submittedCount }}/{{ count($requiredDocuments ?? []) }} Submitted</span>
        </div>
        <div class="card-body">
            @if(count($requiredDocuments) > 0)
                @foreach($requiredDocuments as $reqDoc)
                    @php
                        $uploaded = collect($application->documents ?? [])->firstWhere('type', $reqDoc->code);
                    @endphp
                    <div class="document-item {{ $uploaded ? 'uploaded' : 'missing' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $reqDoc->label }}</h6>
                                <small class="text-muted">{{ $reqDoc->required ? 'Required' : 'Optional' }} • {{ $reqDoc->translation_possible ? 'Translation available' : 'No translation needed' }}</small>
                                @if($uploaded)
                                    <br><small class="text-info"><i class="fas fa-file me-1"></i>{{ $uploaded->original_name ?? 'Document' }}</small>
                                    @if(!$uploaded->stored_path)
                                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>No file path - view may not work</small>
                                    @endif
                                @endif
                            </div>
                            <div class="text-end">
                                @if($uploaded)
                                    <span class="status-badge bg-success text-white"><i class="fas fa-check me-1"></i>Uploaded</span>
                                    <div class="mt-2">
                                        <a href="/view-document.php?id={{ $uploaded->id }}&action=download" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="/view-document.php?id={{ $uploaded->id }}&print=1" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-print me-1"></i>Print
                                        </a>
                                        <a href="/view-document.php?id={{ $uploaded->id }}&action=download&download=1" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                @else
                                    <span class="status-badge bg-danger text-white"><i class="fas fa-times me-1"></i>Missing</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">No required documents specified for this visa type.</p>
            @endif
        </div>
    </div>
</div>
@endsection
