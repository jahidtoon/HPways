@extends('layouts.dashboard')

@section('title', 'Case #' . $case->id . ' - ' . ($case->user->name ?? 'N/A'))
@section('page-title', 'Case Details')

@section('styles')
<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
    }
    .case-header {
        background: rgba(78, 115, 223, 0.92);
        color: #fff;
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.18);
    }
    .card-glass {
        background: rgba(255,255,255,0.92);
        border-radius: 1.25rem;
        box-shadow: 0 4px 24px rgba(78,115,223,0.10);
        backdrop-filter: blur(4px);
        border: 1px solid #e0e7ff;
        margin-bottom: 1.5rem;
    }
    .document-status {
        font-weight: 600;
    }
    .document-status.provided {
        color: #1cc88a;
    }
    .document-status.missing {
        color: #e74a3b;
    }
    .action-box {
        background: rgba(255,255,255,0.95);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(78,115,223,0.10);
    }
    .status-card {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .status-card .card-body {
        padding: 1.25rem;
    }
    .status-icon {
        font-size: 2rem;
        margin-right: 1rem;
    }
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.35rem 0.7rem;
    }
</style>
@endsection

@section('content')
<div class="case-header">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0">{{ $case->user->name ?? 'N/A' }} - {{ $case->visa_type ?? 'N/A' }}</h3>
        <span class="badge bg-primary px-3 py-2">{{ $case->status }}</span>
    </div>
    <div class="mt-2">
        <p class="mb-0">Case ID: {{ $case->id }} | Submitted: {{ $case->submitted_at ? $case->submitted_at->format('M d, Y') : 'Not submitted' }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Application Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card status-card bg-light">
                            <div class="card-body d-flex align-items-center">
                                <div class="status-icon text-primary">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Case Manager</h6>
                                    <p class="mb-0">Assigned: <strong>You</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card status-card bg-light">
                            <div class="card-body d-flex align-items-center">
                                <div class="status-icon text-{{ $case->attorney ? 'success' : 'warning' }}">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Attorney</h6>
                                    @if($case->attorney)
                                        <p class="mb-0">Assigned: <strong>{{ $case->attorney->name }}</strong></p>
                                    @else
                                        <p class="mb-0"><span class="badge badge-lg bg-warning">Not Assigned</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card status-card bg-light">
                            <div class="card-body d-flex align-items-center">
                                <div class="status-icon text-info">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Documents</h6>
                                    @php
                                        $uploadedDocs = $case->documents ?? collect();
                                        $uploadedCount = $uploadedDocs->count();
                                        $missingDocs = $case->missing_documents ?? [];
                                        $missingCount = is_array($missingDocs) ? count($missingDocs) : 0;
                                    @endphp
                                    <p class="mb-0">
                                        <span class="badge badge-lg bg-success">{{ $uploadedCount }} Uploaded</span>
                                        @if($missingCount > 0)
                                            <span class="badge badge-lg bg-danger">{{ $missingCount }} Missing</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card status-card bg-light">
                            <div class="card-body d-flex align-items-center">
                                <div class="status-icon text-warning">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Feedback</h6>
                                    @if(isset($case->feedback) && $case->feedback)
                                        <p class="mb-0"><span class="badge badge-lg bg-info">Provided</span></p>
                                    @else
                                        <p class="mb-0"><span class="badge badge-lg bg-secondary">None</span></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Status Details -->
        <div class="card card-glass">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Document Status</h5>
                @if($case->selectedPackage)
                    <small class="text-muted">Package: {{ $case->selectedPackage->name }}</small>
                @endif
            </div>
            <div class="card-body">
                @if(count($required) > 0 || count($optional) > 0)
                    <div class="row">
                        @if(count($required) > 0)
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="fas fa-exclamation-circle text-danger me-1"></i>Required Documents</h6>
                            <div class="list-group list-group-flush">
                                @foreach($required as $doc)
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                        <div>
                                            <strong>{{ $doc['label'] }}</strong>
                                            <small class="text-muted d-block">{{ $doc['code'] }}</small>
                                        </div>
                                        @if($doc['uploaded'])
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Uploaded
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Missing
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if(count($optional) > 0)
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="fas fa-info-circle text-info me-1"></i>Optional Documents</h6>
                            <div class="list-group list-group-flush">
                                @foreach($optional as $doc)
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                        <div>
                                            <strong>{{ $doc['label'] }}</strong>
                                            <small class="text-muted d-block">{{ $doc['code'] }}</small>
                                        </div>
                                        @if($doc['uploaded'])
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Uploaded
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-minus"></i> Not Uploaded
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>No document requirements defined</strong><br>
                        Visa Type: {{ $case->visa_type ?? 'None' }}<br>
                        Package: {{ $case->selectedPackage->name ?? 'None' }}
                    </div>
                @endif
                
                @if($case->documents->count() > 0)
                    <hr class="my-4">
                    <h6 class="mb-3"><i class="fas fa-paperclip text-primary me-2"></i>All Uploaded Documents ({{ $case->documents->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Document Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Translation</th>
                                    <th>Upload Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($case->documents as $doc)
                                    <tr>
                                        <td>
                                            <strong>{{ $doc->original_name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $doc->type }}</span>
                                        </td>
                                        <td>
                                            {{ number_format($doc->size_bytes / 1024, 1) }} KB
                                        </td>
                                        <td>
                                            @switch($doc->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending Review</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge bg-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $doc->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($doc->needs_translation)
                                                @switch($doc->translation_status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Translation Needed</span>
                                                        @break
                                                    @case('in_progress')
                                                        <span class="badge bg-info">Translating</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Translated</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Translation Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $doc->translation_status }}</span>
                                                @endswitch
                                            @else
                                                <span class="badge bg-light text-muted">No Translation</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $doc->created_at->format('M d, Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>No documents uploaded yet</strong><br>
                        The applicant hasn't uploaded any documents for this case.
                    </div>
                @endif
            </div>
        </div>



        @if(isset($case->feedback) && $case->feedback)
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-comments text-info me-2"></i>Attorney Feedback</h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded">
                    <p class="mb-0">{{ $case->feedback }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        @if(!$case->attorney)
        <div class="action-box mb-4">
            <h5 class="mb-3">Assign Attorney</h5>
            <form action="{{ route('case-manager.assign-attorney', $case->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="attorney_id" class="form-label">Select Attorney</label>
                    <select class="form-select" id="attorney_id" name="attorney_id" required>
                        <option value="" selected disabled>Choose an attorney</option>
                        @foreach($availableAttorneys as $attorney)
                            <option value="{{ $attorney->id }}">{{ $attorney->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Assign Attorney</button>
                </div>
            </form>
        </div>
        @endif
        
        <div class="action-box">
            <h5 class="mb-3">Request Documents</h5>
            <form action="{{ route('case-manager.request-documents', $case->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="documents" class="form-label">Documents Needed</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="birth_certificate" id="doc1" name="documents[]">
                        <label class="form-check-label" for="doc1">
                            Birth Certificate
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="bank_statements" id="doc2" name="documents[]">
                        <label class="form-check-label" for="doc2">
                            Bank Statements
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="passport" id="doc3" name="documents[]">
                        <label class="form-check-label" for="doc3">
                            Passport Copy
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="photos" id="doc4" name="documents[]">
                        <label class="form-check-label" for="doc4">
                            Passport Photos
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="message" class="form-label">Message to Applicant</label>
                    <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-warning">Request Documents</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
