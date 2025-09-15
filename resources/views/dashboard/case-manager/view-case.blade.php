@extends('layouts.dashboard')

@section('title', 'View Case')
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
        <h3 class="mb-0">{{ $case->applicant_name }} - {{ $case->visa_type }}</h3>
        <span class="badge bg-primary px-3 py-2">{{ $case->status }}</span>
    </div>
    <div class="mt-2">
        <p class="mb-0">Case ID: {{ $case->id }} | Submitted: {{ $case->submitted_at }}</p>
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
                                <div class="status-icon text-{{ $case->attorney_name ? 'success' : 'warning' }}">
                                    <i class="fas fa-gavel"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Attorney</h6>
                                    @if($case->attorney_name)
                                        <p class="mb-0">Assigned: <strong>{{ $case->attorney_name }}</strong></p>
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
                                    @if(count($case->missing_documents) > 0)
                                        <p class="mb-0"><span class="badge badge-lg bg-danger">{{ count($case->missing_documents) }} Missing</span></p>
                                    @else
                                        <p class="mb-0"><span class="badge badge-lg bg-success">Complete</span></p>
                                    @endif
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

        @if(count($case->missing_documents) > 0)
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Missing Documents</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($case->missing_documents as $document)
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent">
                        {{ $document }}
                        <button class="btn btn-sm btn-outline-primary">Request</button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

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
        @if(!$case->attorney_name)
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
