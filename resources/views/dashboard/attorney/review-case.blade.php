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
    .timeline-marker {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .feedback-history {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }
    .feedback-history::-webkit-scrollbar {
        width: 6px;
    }
    .feedback-history::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 3px;
    }
    .feedback-history::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
@if(isset($message) && !empty($message))
    <div class="alert alert-{{ $messageType == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
        <strong>{{ $messageType == 'success' ? 'Success!' : 'Error!' }}</strong> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(isset($_GET['success']))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ $_GET['success'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(isset($_GET['error']))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ $_GET['error'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="case-header">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0">{{ $case->applicant_name }} - {{ $case->visa_type }}</h3>
        <span class="badge bg-primary px-3 py-2">{{ $case->status }}</span>
    </div>
    <div class="mt-2">
        <p class="mb-0">Case ID: {{ $case->id }} | Applicant: {{ $case->user->name ?? 'Unknown' }} | Submitted: {{ $case->submitted_at }}</p>
    </div>
</div>
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0">{{ $case->applicant_name }} - {{ $case->visa_type }}</h3>
        <span class="badge bg-primary px-3 py-2">{{ $case->status }}</span>
    </div>

<div class="row">
    <div class="col-md-8">
        @php($application = $case)
        @include('components.application.progress', ['application' => $application])
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
                            @if(isset($documentStatus) && count($documentStatus) > 0)
                                @foreach($documentStatus as $docStatus)
                                <tr class="{{ $docStatus['uploaded'] ? '' : 'table-warning' }}">
                                    <td>
                                        <div>
                                            <strong>{{ $docStatus['label'] }}</strong>
                                            @if($docStatus['required'])
                                                <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">Required</span>
                                            @else
                                                <span class="badge bg-secondary ms-2" style="font-size: 0.6rem;">Optional</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($docStatus['uploaded'])
                                            @if($docStatus['status'] == 'approved')
                                                <span class="document-status provided"><i class="fas fa-check-circle me-1"></i>Approved</span>
                                            @elseif($docStatus['status'] == 'pending')
                                                <span class="document-status" style="color: #f59e0b;"><i class="fas fa-clock me-1"></i>Pending Review</span>
                                            @elseif($docStatus['status'] == 'rejected')
                                                <span class="document-status missing"><i class="fas fa-times-circle me-1"></i>Rejected</span>
                                            @else
                                                <span class="document-status" style="color: #6b7280;"><i class="fas fa-upload me-1"></i>Uploaded</span>
                                            @endif
                                        @else
                                            <span class="document-status missing"><i class="fas fa-exclamation-triangle me-1"></i>Missing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($docStatus['uploaded'])
                                            <a href="/view-document.php?id={{ $docStatus['uploaded_document']->id }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            @if($docStatus['status'] == 'pending')
                                                <button class="btn btn-sm btn-outline-success ms-1" onclick="approveDocument({{ $docStatus['uploaded_document']->id }})">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger ms-1" onclick="rejectDocument({{ $docStatus['uploaded_document']->id }})">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            @endif
                                        @else
                                            <span class="text-muted small">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                                        <p class="mb-0">No document requirements found for this visa type</p>
                                    </td>
                                </tr>
                            @endif
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
            <h5 class="mb-3"><i class="fas fa-gavel me-2"></i>Attorney Actions</h5>
            
            <!-- Quick Action Buttons -->
            <div class="row g-2 mb-4">
                <div class="col-12">
                    <button type="button" class="btn btn-success w-100" onclick="showApprovalForm()">
                        <i class="fas fa-check me-2"></i>Approve Application
                    </button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-danger w-100" onclick="showRejectionForm()">
                        <i class="fas fa-times me-2"></i>Reject Application
                    </button>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-warning w-100" onclick="showFeedbackForm()">
                        <i class="fas fa-comment me-2"></i>Provide Feedback
                    </button>
                </div>
            </div>

            <!-- Approval Form -->
            <div id="approvalForm" style="display: none;">
                <form action="/attorney-review-case.php?id={{ $case->id }}" method="POST">
                    <input type="hidden" name="action" value="approve">
                    <div class="alert alert-success">
                        <strong><i class="fas fa-check-circle me-2"></i>Approve Application</strong>
                        <p class="mb-0 mt-2">This will approve the application and notify the applicant.</p>
                    </div>
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" placeholder="Add any final notes for the applicant..."></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Confirm Approval
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="hideAllForms()">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Rejection Form -->
            <div id="rejectionForm" style="display: none;">
                <form action="/attorney-review-case.php?id={{ $case->id }}" method="POST">
                    <input type="hidden" name="action" value="reject">
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-times-circle me-2"></i>Reject Application</strong>
                        <p class="mb-0 mt-2">This will reject the application. Please provide a clear reason.</p>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Provide detailed reason for rejection..." required></textarea>
                        <small class="text-muted">This reason will be visible to the applicant.</small>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Confirm Rejection
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="hideAllForms()">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Feedback Form -->
            <div id="feedbackForm" style="display: none;">
                <form action="/attorney-review-case.php?id={{ $case->id }}" method="POST" onsubmit="console.log('Form submitting to:', this.action);">
                    <input type="hidden" name="action" value="feedback">
                    <div class="alert alert-info">
                        <strong><i class="fas fa-comment me-2"></i>Provide Feedback</strong>
                        <p class="mb-0 mt-2">Request additional information or documents from the applicant.</p>
                    </div>
                    <div class="mb-3">
                        <label for="feedback_message" class="form-label">Feedback Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedback_message" name="feedback_message" rows="4" placeholder="Specify what information or documents are needed..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback Type</label>
                        <select class="form-select" name="feedback_type" required>
                            <option value="">Select feedback type</option>
                            <option value="general">General Feedback</option>
                            <option value="document_issue">Document Issue</option>
                            <option value="legal_advice">Legal Advice</option>
                            <option value="rfe">Request for Evidence (RFE)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Specific Issues to Address</label>
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="issues[]" id="document_quality" value="Document Quality Issues">
                                <label class="form-check-label" for="document_quality">Document Quality Issues (unclear, incomplete)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="issues[]" id="missing_signatures" value="Missing Signatures">
                                <label class="form-check-label" for="missing_signatures">Missing Required Signatures</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="issues[]" id="translation_needed" value="Translation Required">
                                <label class="form-check-label" for="translation_needed">Documents Need Translation</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="issues[]" id="additional_evidence" value="Additional Evidence Needed">
                                <label class="form-check-label" for="additional_evidence">Additional Evidence of Joint Life Required</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Additional Document Requests for {{ $case->visa_type ?? 'this case' }}</label>
                        <div class="row">
                            @if($case->visa_type == 'I751')
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="joint_bank" value="Joint Bank Statements">
                                    <label class="form-check-label" for="joint_bank">Additional Joint Bank Statements</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="joint_lease" value="Joint Lease/Mortgage">
                                    <label class="form-check-label" for="joint_lease">Joint Lease or Mortgage Documents</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="joint_insurance" value="Joint Insurance">
                                    <label class="form-check-label" for="joint_insurance">Joint Insurance Policies</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="joint_utilities" value="Joint Utility Bills">
                                    <label class="form-check-label" for="joint_utilities">Joint Utility Bills</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="tax_returns" value="Joint Tax Returns">
                                    <label class="form-check-label" for="tax_returns">Joint Tax Returns</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="photos" value="Joint Photos">
                                    <label class="form-check-label" for="photos">Photos Together (with dates)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="affidavits" value="Affidavits from Friends/Family">
                                    <label class="form-check-label" for="affidavits">Affidavits from Friends/Family</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="other_evidence" value="Other Joint Evidence">
                                    <label class="form-check-label" for="other_evidence">Other Joint Evidence</label>
                                </div>
                            </div>
                            @else
                            <div class="col-6">
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
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="medical_records" value="Medical Records">
                                    <label class="form-check-label" for="medical_records">Medical Records</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="police_clearance" value="Police Clearance">
                                    <label class="form-check-label" for="police_clearance">Police Clearance</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="requested_documents[]" id="other_docs" value="Other Documents">
                                    <label class="form-check-label" for="other_docs">Other Documents</label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deadline for Response</label>
                        <select class="form-select" name="response_deadline">
                            <option value="7_days">7 days</option>
                            <option value="14_days" selected>14 days</option>
                            <option value="30_days">30 days</option>
                            <option value="60_days">60 days</option>
                            <option value="custom">Custom deadline</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-paper-plane me-2"></i>Send Feedback
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="hideAllForms()">Cancel</button>
                    </div>
                </form>
            </div>

            <!-- Request Meeting Form -->
            <div class="mt-4">
                <div class="card card-glass">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="fas fa-video text-primary me-2"></i>Request Zoom Meeting</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard.attorney.meetings.request', $case->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Topic<span class="text-danger">*</span></label>
                                <input type="text" name="topic" class="form-control" placeholder="e.g. Case discussion about {{ $case->visa_type }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Add any context or agenda items"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane me-1"></i>Send Request to Case Manager</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Previous Feedback & Communication History -->
            <div class="mt-4">
                <h6 class="mb-3"><i class="fas fa-history me-2"></i>Case Communication History</h6>
                
                @if($case->feedback && $case->feedback->count() > 0)
                <div class="feedback-history" style="max-height: 300px; overflow-y: auto;">
                    @foreach($case->feedback->sortByDesc('created_at') as $feedback)
                    <div class="feedback-item p-3 mb-3 rounded shadow-sm" style="background: {{ $feedback->type == 'approval' ? 'rgba(16, 185, 129, 0.1)' : ($feedback->type == 'rejection' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(78, 115, 223, 0.1)') }}; border-left: 4px solid {{ $feedback->type == 'approval' ? '#10b981' : ($feedback->type == 'rejection' ? '#ef4444' : '#4e73df') }};">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                @if($feedback->type == 'approval')
                                    <span class="badge bg-success me-2"><i class="fas fa-check me-1"></i>Approved</span>
                                @elseif($feedback->type == 'rejection')
                                    <span class="badge bg-danger me-2"><i class="fas fa-times me-1"></i>Rejected</span>
                                @elseif($feedback->type == 'rfe')
                                    <span class="badge bg-warning me-2"><i class="fas fa-exclamation-triangle me-1"></i>RFE</span>
                                @elseif($feedback->type == 'document_issue')
                                    <span class="badge bg-info me-2"><i class="fas fa-file-alt me-1"></i>Document Issue</span>
                                @else
                                    <span class="badge bg-primary me-2"><i class="fas fa-comment me-1"></i>{{ ucfirst($feedback->type) }}</span>
                                @endif
                                <small class="text-muted">by {{ $feedback->user->name ?? 'Attorney' }}</small>
                            </div>
                            <small class="text-muted">{{ $feedback->created_at->format('M d, Y \a\t H:i') }}</small>
                        </div>
                        <p class="mb-0" style="font-size: 0.9rem;">{{ $feedback->content }}</p>
                        
                        @if($feedback->type == 'rfe' || $feedback->type == 'document_issue')
                        <div class="mt-2 p-2 rounded" style="background: rgba(255, 255, 255, 0.7);">
                            <small class="text-muted d-block mb-1"><i class="fas fa-clock me-1"></i>Response Expected</small>
                            <small class="fw-semibold">Within 14 days</small>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No feedback history available</p>
                    <small class="text-muted">Attorney feedback and communications will appear here</small>
                </div>
                @endif
                
                <!-- Case Timeline -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="mb-3"><i class="fas fa-timeline me-2"></i>Case Timeline</h6>
                    <div class="timeline">
                        <div class="timeline-item d-flex align-items-center mb-2">
                            <div class="timeline-marker bg-success me-3"></div>
                            <div>
                                <strong>Case Assigned to Attorney</strong>
                                <br><small class="text-muted">{{ $case->created_at->format('M d, Y \a\t H:i') }}</small>
                            </div>
                        </div>
                        @if($case->status == 'approved')
                        <div class="timeline-item d-flex align-items-center mb-2">
                            <div class="timeline-marker bg-success me-3"></div>
                            <div>
                                <strong>Application Approved</strong>
                                <br><small class="text-muted">{{ $case->updated_at->format('M d, Y \a\t H:i') }}</small>
                            </div>
                        </div>
                        @elseif($case->status == 'rejected')
                        <div class="timeline-item d-flex align-items-center mb-2">
                            <div class="timeline-marker bg-danger me-3"></div>
                            <div>
                                <strong>Application Rejected</strong>
                                <br><small class="text-muted">{{ $case->updated_at->format('M d, Y \a\t H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showApprovalForm() {
    hideAllForms();
    document.getElementById('approvalForm').style.display = 'block';
}

function showRejectionForm() {
    hideAllForms();
    document.getElementById('rejectionForm').style.display = 'block';
}

function showFeedbackForm() {
    hideAllForms();
    document.getElementById('feedbackForm').style.display = 'block';
}

function hideAllForms() {
    document.getElementById('approvalForm').style.display = 'none';
    document.getElementById('rejectionForm').style.display = 'none';
    document.getElementById('feedbackForm').style.display = 'none';
}

function approveDocument(docId) {
    if (confirm('Approve this document?')) {
        // For demo purposes, just reload with success message
        window.location.href = window.location.href + '&doc_approved=' + docId;
    }
}

function rejectDocument(docId) {
    const reason = prompt('Please provide a reason for rejecting this document:');
    if (reason && reason.trim()) {
        // For demo purposes, just reload with success message
        window.location.href = window.location.href + '&doc_rejected=' + docId + '&reason=' + encodeURIComponent(reason);
    }
}

// Auto-hide forms on page load and show feedback form by default for new cases
document.addEventListener('DOMContentLoaded', function() {
    hideAllForms();
    @if(!isset($case->feedback) || count($case->feedback) == 0)
        showFeedbackForm(); // Show feedback form for new cases
    @endif
});
</script>
@endsection
