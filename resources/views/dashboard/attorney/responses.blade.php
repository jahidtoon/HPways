@extends('layouts.dashboard')

@section('title', 'Applicant Responses')
@section('page-title', 'Applicant Responses')

@section('styles')
<style>
    body {
        background: linear-gradient(120deg, #f8fafc 0%, #e0e7ff 100%);
    }
    .response-header {
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
    .response-item {
        position: relative;
        border-left: 4px solid #4e73df;
        padding-left: 1.5rem;
        margin-left: 1rem;
        margin-bottom: 2rem;
    }
    .response-item::before {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #4e73df;
        left: -10px;
        top: 0;
    }
    .response-item:last-child {
        margin-bottom: 0;
    }
    .timeline-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    .document-status {
        font-weight: 600;
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 50rem;
        font-size: 0.85rem;
    }
    .document-status.new {
        background-color: rgba(78, 115, 223, 0.15);
        color: #4e73df;
    }
    .document-status.reviewed {
        background-color: rgba(28, 200, 138, 0.15);
        color: #1cc88a;
    }
    .document-preview {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .document-preview img {
        width: 100%;
        height: auto;
    }
    .conversation-container {
        max-height: 400px;
        overflow-y: auto;
    }
    .feedback-item {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: #f8f9fc;
    }
    .response-item {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: #e8f4fd;
        border-left: 4px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div class="response-header">
    <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Applicant Responses</h3>
        <div>
            <span class="badge bg-primary me-2">3 New Responses</span>
            <span class="badge bg-success">5 Total</span>
        </div>
    </div>
    <div class="mt-2">
        <p class="mb-0">Review document uploads and responses from applicants based on your feedback</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card card-glass mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-bell text-primary me-2"></i>New Responses</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <!-- Response Item 1 -->
                    <div class="list-group-item border-0 mb-3 p-0">
                        <div class="d-flex p-3 bg-light rounded-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-primary text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Jane Doe - APP-201</h5>
                                    <span class="document-status new">
                                        <i class="fas fa-circle me-1 fa-xs"></i>New Response
                                    </span>
                                </div>
                                <div class="timeline-date"><i class="fas fa-clock me-1"></i>August 26, 2025 - 10:23 AM</div>
                                <p class="mb-2">Uploaded <strong>Birth Certificate</strong> in response to your feedback from August 23.</p>
                                <div class="d-flex mt-3">
                                    <a href="#" class="btn btn-sm btn-primary me-2">Review Document</a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#conversation1">View Conversation</a>
                                </div>
                                
                                <div class="collapse mt-3" id="conversation1">
                                    <div class="conversation-container p-3 bg-white rounded">
                                        <h6 class="border-bottom pb-2 mb-3">Feedback History</h6>
                                        <!-- Your feedback -->
                                        <div class="feedback-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Your Feedback</span>
                                                <small class="text-muted">August 23, 2025</small>
                                            </div>
                                            <p class="mb-0 mt-2">Please provide your birth certificate as it's required for your visa application. Make sure it's a certified copy.</p>
                                        </div>
                                        
                                        <!-- Applicant response -->
                                        <div class="response-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Jane Doe</span>
                                                <small class="text-muted">August 26, 2025</small>
                                            </div>
                                            <p class="mb-2 mt-2">I've uploaded my birth certificate as requested. This is a certified copy from the county records office.</p>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Birth_Certificate.pdf (2.3 MB)</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Reply form -->
                                        <div class="mt-3">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="reply1" class="form-label">Your Reply</label>
                                                    <textarea class="form-control" id="reply1" rows="3" placeholder="Type your response..."></textarea>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="docStatus1" id="approve1" value="approve" checked>
                                                            <label class="form-check-label text-success" for="approve1">Approve</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="docStatus1" id="reject1" value="reject">
                                                            <label class="form-check-label text-danger" for="reject1">Reject</label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Send Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Response Item 2 -->
                    <div class="list-group-item border-0 mb-3 p-0">
                        <div class="d-flex p-3 bg-light rounded-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-primary text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Mark Evans - APP-202</h5>
                                    <span class="document-status new">
                                        <i class="fas fa-circle me-1 fa-xs"></i>New Response
                                    </span>
                                </div>
                                <div class="timeline-date"><i class="fas fa-clock me-1"></i>August 25, 2025 - 3:45 PM</div>
                                <p class="mb-2">Uploaded <strong>Financial Documents</strong> in response to your feedback from August 22.</p>
                                <div class="d-flex mt-3">
                                    <a href="#" class="btn btn-sm btn-primary me-2">Review Document</a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#conversation2">View Conversation</a>
                                </div>
                                
                                <div class="collapse mt-3" id="conversation2">
                                    <div class="conversation-container p-3 bg-white rounded">
                                        <h6 class="border-bottom pb-2 mb-3">Feedback History</h6>
                                        <!-- Your feedback -->
                                        <div class="feedback-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Your Feedback</span>
                                                <small class="text-muted">August 22, 2025</small>
                                            </div>
                                            <p class="mb-0 mt-2">Please provide bank statements for the last 3 months to show financial stability for your student visa application.</p>
                                        </div>
                                        
                                        <!-- Applicant response -->
                                        <div class="response-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Mark Evans</span>
                                                <small class="text-muted">August 25, 2025</small>
                                            </div>
                                            <p class="mb-2 mt-2">I've uploaded my bank statements for the last 3 months as requested. I've also included a letter from my bank confirming my account standing.</p>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Bank_Statements_June.pdf (1.8 MB)</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Bank_Statements_July.pdf (1.9 MB)</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Bank_Statements_August.pdf (2.0 MB)</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                                <span>Bank_Letter.pdf (0.5 MB)</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Reply form -->
                                        <div class="mt-3">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="reply2" class="form-label">Your Reply</label>
                                                    <textarea class="form-control" id="reply2" rows="3" placeholder="Type your response..."></textarea>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="docStatus2" id="approve2" value="approve" checked>
                                                            <label class="form-check-label text-success" for="approve2">Approve</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="docStatus2" id="reject2" value="reject">
                                                            <label class="form-check-label text-danger" for="reject2">Reject</label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Send Reply</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Response Item 3 -->
                    <div class="list-group-item border-0 p-0">
                        <div class="d-flex p-3 bg-light rounded-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-primary text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Linda Green - APP-203</h5>
                                    <span class="document-status new">
                                        <i class="fas fa-circle me-1 fa-xs"></i>New Response
                                    </span>
                                </div>
                                <div class="timeline-date"><i class="fas fa-clock me-1"></i>August 24, 2025 - 11:15 AM</div>
                                <p class="mb-2">Uploaded <strong>Police Clearance</strong> in response to your feedback from August 20.</p>
                                <div class="d-flex mt-3">
                                    <a href="#" class="btn btn-sm btn-primary me-2">Review Document</a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#conversation3">View Conversation</a>
                                </div>
                                
                                <div class="collapse mt-3" id="conversation3">
                                    <div class="conversation-container p-3 bg-white rounded">
                                        <h6 class="border-bottom pb-2 mb-3">Feedback History</h6>
                                        <!-- Conversation history would go here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-glass">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i>Previously Reviewed Responses</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <!-- Previously Reviewed Item 1 -->
                    <div class="list-group-item border-0 mb-3 p-0">
                        <div class="d-flex p-3 bg-light rounded-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-success text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Jane Doe - APP-201</h5>
                                    <span class="document-status reviewed">
                                        <i class="fas fa-check-circle me-1 fa-xs"></i>Reviewed
                                    </span>
                                </div>
                                <div class="timeline-date"><i class="fas fa-clock me-1"></i>August 15, 2025 - 9:30 AM</div>
                                <p class="mb-2">Uploaded <strong>Passport</strong> in response to your feedback from August 12.</p>
                                <p class="mb-0"><strong>Your response:</strong> <span class="text-success">Approved</span> - Document meets all requirements.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Previously Reviewed Item 2 -->
                    <div class="list-group-item border-0 p-0">
                        <div class="d-flex p-3 bg-light rounded-3">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-success text-white">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0">Mark Evans - APP-202</h5>
                                    <span class="document-status reviewed">
                                        <i class="fas fa-check-circle me-1 fa-xs"></i>Reviewed
                                    </span>
                                </div>
                                <div class="timeline-date"><i class="fas fa-clock me-1"></i>August 14, 2025 - 2:15 PM</div>
                                <p class="mb-2">Uploaded <strong>University Acceptance Letter</strong> in response to your feedback from August 10.</p>
                                <p class="mb-0"><strong>Your response:</strong> <span class="text-success">Approved</span> - Document verified with the university.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
