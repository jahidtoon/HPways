@extends('layouts.dashboard')

@section('title', 'Case Review')
@section('page-title', 'Case Review')

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
    .feedback-form {
        background: rgba(255,255,255,0.95);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 24px rgba(78,115,223,0.10);
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
        <p class="mb-0">Case ID: {{ $case->id }} | Application ID: {{ $case->application_id }} | Submitted: {{ $case->submitted_at }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Document Review</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Document</th>
                                <th width="150">Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($case->documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>
                                    @if($document->status == 'Provided')
                                        <span class="document-status provided"><i class="fas fa-check-circle me-1"></i>{{ $document->status }}</span>
                                    @else
                                        <span class="document-status missing"><i class="fas fa-times-circle me-1"></i>{{ $document->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">View</button>
                                    @if($document->status == 'Provided')
                                        <button class="btn btn-sm btn-outline-warning">Flag Issue</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-comments text-primary me-2"></i>Case Manager Notes</h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded">
                    <p class="mb-0">{{ $case->notes }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="feedback-form">
            <h5 class="mb-3">Attorney Feedback</h5>
            <form action="{{ route('attorney.provide-feedback', $case->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="feedback" class="form-label">Feedback to Applicant</label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="5" placeholder="Enter your feedback for the applicant. Be specific about document requirements or concerns."></textarea>
                    <small class="text-muted">This feedback will be visible to the applicant on their dashboard.</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Decision</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="approve" value="approve">
                        <label class="form-check-label" for="approve">
                            <span class="text-success fw-bold">Approve</span> - Documents are complete and case can proceed
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="reject" value="reject">
                        <label class="form-check-label" for="reject">
                            <span class="text-danger fw-bold">Reject</span> - Application has serious issues that cannot be remediated
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="decision" id="more_info" value="more_info" checked>
                        <label class="form-check-label" for="more_info">
                            <span class="text-warning fw-bold">Request More Information</span> - Needs additional documents or clarification
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Document Requests</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requested_documents[]" id="passport" value="Passport">
                        <label class="form-check-label" for="passport">Passport</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requested_documents[]" id="birth_certificate" value="Birth Certificate">
                        <label class="form-check-label" for="birth_certificate">Birth Certificate</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requested_documents[]" id="financial_docs" value="Financial Documents">
                        <label class="form-check-label" for="financial_docs">Financial Documents</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requested_documents[]" id="medical_records" value="Medical Records">
                        <label class="form-check-label" for="medical_records">Medical Records</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requested_documents[]" id="police_clearance" value="Police Clearance">
                        <label class="form-check-label" for="police_clearance">Police Clearance</label>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Send Feedback to Applicant</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
