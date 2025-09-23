@extends('layouts.dashboard')
@section('title', 'Application Detail')
@section('page-title', 'Application Detail')

@section('styles')
<style>
    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
    }
    
    .progress-custom {
        height: 8px;
        border-radius: 4px;
    }
    
    .document-item {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: white;
    }
    
    .document-item.uploaded {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    
    .document-item.missing {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    
    .assignment-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.5rem;
        background: white;
        transition: all 0.3s ease;
    }
    
    .assignment-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .timeline-item {
        border-left: 3px solid #e2e8f0;
        padding-left: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0.5rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3b82f6;
    }
    
    .action-btn {
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Application #APP-{{ $application->id }}</h2>
                    <p class="text-muted mb-0">Submitted {{ $application->created_at ? $application->created_at->format('M d, Y \a\t H:i') : '-' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <!-- Status Update Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Update Status
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateStatus('pending')">Pending</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateStatus('under_review')">Under Review</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateStatus('approved')">Approved</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateStatus('rejected')">Rejected</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateStatus('completed')">Completed</a></li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.applications') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Applications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            @include('components.application.progress', ['application' => $application])
            <!-- Basic Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Application Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-5">Applicant:</dt>
                                <dd class="col-7">{{ $application->user->name ?? 'Unknown' }}</dd>
                                <dt class="col-5">Email:</dt>
                                <dd class="col-7">{{ $application->user->email ?? '-' }}</dd>
                                <dt class="col-5">Visa Type:</dt>
                                <dd class="col-7">
                                    <span class="badge bg-info">{{ $application->visa_type ?? 'Not Specified' }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-5">Status:</dt>
                                <dd class="col-7">
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'under_review' => 'info', 
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'completed' => 'success'
                                        ];
                                        $color = $statusColors[$application->status] ?? 'secondary';
                                    @endphp
                                    <span class="status-badge bg-{{ $color }} text-white">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                                </dd>
                                <dt class="col-5">Priority:</dt>
                                <dd class="col-7">
                                    <span class="badge bg-warning">Normal</span>
                                </dd>
                                <dt class="col-5">Progress:</dt>
                                <dd class="col-7">
                                    <div class="progress progress-custom">
                                        <div class="progress-bar bg-primary" style="width: {{ $completionPercentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $completionPercentage }}% Complete</small>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
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
                                            <div class="mb-2">
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Uploaded</span>
                                                <br><small class="text-muted">{{ $uploaded->created_at ? $uploaded->created_at->format('M d, Y') : '' }}</small>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument('{{ $uploaded->id }}', '{{ $reqDoc->label }}')">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="printDocument('{{ $uploaded->id }}')">
                                                    <i class="fas fa-print me-1"></i>Print
                                                </button>
                                            </div>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Missing</span>
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

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                </div>
                <div class="card-body">
                    @if(count($application->payments ?? []) > 0)
                        @foreach($application->payments as $payment)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                <div>
                                    <h6 class="mb-1">${{ number_format(($payment->amount_cents ?? 0) / 100, 2) }}</h6>
                                    <small class="text-muted">{{ ucfirst($payment->provider ?? 'Unknown') }} • {{ $payment->created_at ? $payment->created_at->format('M d, Y') : '' }}</small>
                                </div>
                                <span class="badge bg-{{ ($payment->status ?? 'pending') === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status ?? 'Pending') }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle text-warning fs-1 mb-3"></i>
                            <h6>No Payments Made</h6>
                            <p class="text-muted">This application has no payment records yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attorney Feedback Section -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Attorney Feedback</h5>
                </div>
                <div class="card-body">
                    @if(count($application->feedback ?? []) > 0)
                        @foreach($application->feedback->sortByDesc('created_at') as $feedback)
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
                                        <small class="text-muted">by {{ $feedback->attorney->name ?? 'Attorney' }}</small>
                                    </div>
                                    <small class="text-muted">{{ $feedback->created_at ? $feedback->created_at->format('M d, Y \a\t H:i') : '' }}</small>
                                </div>
                                <p class="mb-0" style="font-size: 0.9rem;">{{ $feedback->content }}</p>
                                
                                @if($feedback->type == 'rfe' || $feedback->type == 'document_issue')
                                    @if($feedback->requested_documents && count($feedback->requested_documents) > 0)
                                        <div class="mt-2 p-2 rounded" style="background: rgba(255, 255, 255, 0.7);">
                                            <small class="text-muted d-block mb-1"><i class="fas fa-file-alt me-1"></i>Requested Documents:</small>
                                            <ul class="mb-0 ps-3">
                                                @foreach($feedback->requested_documents as $doc)
                                                    <li><small class="fw-semibold">{{ $doc }}</small></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                            <h6>No Attorney Feedback</h6>
                            <p class="text-muted">No feedback has been provided by the assigned attorney yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Staff Assignment -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Staff Assignment</h5>
                </div>
                <div class="card-body">
                    <!-- Case Manager Assignment -->
                    <div class="assignment-card mb-3">
                        <h6><i class="fas fa-briefcase me-2 text-primary"></i>Case Manager</h6>
                        @if($application->caseManager ?? null)
                            <p class="mb-2"><strong>{{ $application->caseManager->name }}</strong></p>
                            <small class="text-muted">{{ $application->caseManager->email }}</small>
                        @else
                            <p class="text-muted mb-3">Not assigned</p>
                        @endif
                        
                        <form action="{{ route('admin.assign-case-manager', $application->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <select name="case_manager_id" class="form-select form-select-sm" required>
                                    <option value="">Select Case Manager</option>
                                    @foreach($availableCaseManagers as $cm)
                                        <option value="{{ $cm->id }}" {{ ($application->case_manager_id ?? null) == $cm->id ? 'selected' : '' }}>
                                            {{ $cm->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                            </div>
                        </form>
                    </div>

                    <!-- Attorney Assignment -->
                    <div class="assignment-card mb-3">
                        <h6><i class="fas fa-gavel me-2 text-warning"></i>Attorney</h6>
                        @if($application->attorney ?? null)
                            <p class="mb-2"><strong>{{ $application->attorney->name }}</strong></p>
                            <small class="text-muted">{{ $application->attorney->email }}</small>
                        @else
                            <p class="text-muted mb-3">Not assigned</p>
                        @endif
                        
                        <form action="{{ route('admin.assign-attorney', $application->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <select name="attorney_id" class="form-select form-select-sm" required>
                                    <option value="">Select Attorney</option>
                                    @foreach($availableAttorneys as $attorney)
                                        <option value="{{ $attorney->id }}" {{ ($application->attorney_id ?? null) == $attorney->id ? 'selected' : '' }}>
                                            {{ $attorney->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-warning">Assign</button>
                            </div>
                        </form>
                    </div>

                    <!-- Printer Assignment -->
                    <div class="assignment-card">
                        <h6><i class="fas fa-print me-2 text-info"></i>Printing Staff</h6>
                        @if($application->assignedPrinter ?? null)
                            <p class="mb-2"><strong>{{ $application->assignedPrinter->name }}</strong></p>
                            <small class="text-muted">{{ $application->assignedPrinter->email }}</small>
                        @else
                            <p class="text-muted mb-3">Not assigned</p>
                        @endif
                        
                        <form action="{{ route('admin.assign-printer', $application->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <select name="printer_id" class="form-select form-select-sm" required>
                                    <option value="">Select Printer</option>
                                    @foreach($availablePrinters as $printer)
                                        <option value="{{ $printer->id }}" {{ ($application->assigned_printer_id ?? null) == $printer->id ? 'selected' : '' }}>
                                            {{ $printer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-info">Assign</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Activity Timeline -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-item">
                        <h6 class="mb-1">Application Submitted</h6>
                        <small class="text-muted">{{ $application->created_at ? $application->created_at->format('M d, Y H:i') : '-' }}</small>
                    </div>
                    
                    @if($application->caseManager ?? null)
                    <div class="timeline-item">
                        <h6 class="mb-1">Case Manager Assigned</h6>
                        <small class="text-muted">{{ $application->caseManager->name }}</small>
                    </div>
                    @endif
                    
                    @if($application->attorney ?? null)
                    <div class="timeline-item">
                        <h6 class="mb-1">Attorney Assigned</h6>
                        <small class="text-muted">{{ $application->attorney->name }}</small>
                    </div>
                    @endif
                    
                    @foreach($application->documents ?? [] as $doc)
                    <div class="timeline-item">
                        <h6 class="mb-1">Document Uploaded</h6>
                        <small class="text-muted">{{ $doc->type }} - {{ $doc->created_at ? $doc->created_at->format('M d, Y') : '' }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentViewerModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Document Viewer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="documentContent" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading document...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="printDocumentBtn">
                    <i class="fas fa-print me-1"></i>Print Document
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Form -->
<form id="statusUpdateForm" action="{{ route('admin.update-application-status', $application->id) }}" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
</form>

@endsection

@section('scripts')
<script>
function updateStatus(status) {
    if(confirm('Are you sure you want to update the application status to ' + status.replace('_', ' ') + '?')) {
        document.getElementById('statusInput').value = status;
        document.getElementById('statusUpdateForm').submit();
    }
}

function sendNotification() {
    alert('Notification feature will be implemented.');
}

function generateReport() {
    alert('Report generation feature will be implemented.');
}

function addNote() {
    alert('Add note feature will be implemented.');
}

function exportData() {
    alert('Export feature will be implemented.');
}

// Document viewing and printing functions
let currentDocumentId = null;

function viewDocument(documentId, documentName) {
    currentDocumentId = documentId;
    const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
    const modalTitle = document.getElementById('documentViewerModalLabel');
    const documentContent = document.getElementById('documentContent');
    
    // Update modal title
    modalTitle.innerHTML = '<i class="fas fa-file-alt me-2"></i>' + documentName;
    
    // Show loading spinner
    documentContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading document...</p>
        </div>
    `;
    
    // Show modal
    modal.show();
    
    // Load document content
    fetch('/view-document.php?id=' + documentId)
        .then(response => response.text())
        .then(html => {
            documentContent.innerHTML = html;
        })
        .catch(error => {
            documentContent.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Error Loading Document</h5>
                    <p>Unable to load the document. Please try again later.</p>
                    <small class="text-muted">Error: ${error.message}</small>
                </div>
            `;
        });
}

function printDocument(documentId) {
    if (documentId) {
        // Open document in new window for printing
        const printWindow = window.open('/view-document.php?id=' + documentId + '&print=1', '_blank', 'width=800,height=600');
        
        // Wait for content to load, then trigger print
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
                // Close window after printing (optional)
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            }, 1000); // Increased delay to ensure content loads
        };
        
        // Fallback if onload doesn't work
        setTimeout(() => {
            if (printWindow && !printWindow.closed) {
                try {
                    printWindow.print();
                } catch (e) {
                    console.log('Print fallback triggered');
                }
            }
        }, 1500);
    } else {
        alert('No document selected for printing.');
    }
}

// Print button handler in modal
document.addEventListener('DOMContentLoaded', function() {
    const printBtn = document.getElementById('printDocumentBtn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            if (currentDocumentId) {
                printDocument(currentDocumentId);
            }
        });
    }
});
</script>
@endsection
