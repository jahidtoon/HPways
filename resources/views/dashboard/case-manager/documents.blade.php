@extends('layouts.dashboard')

@section('title', 'Document Manager')
@section('page-title', 'Document Manager')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-folder-open me-2 text-primary"></i>Document Manager
        </h1>
        <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Documents Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Documents from My Cases</h6>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Document</th>
                            <th>Application</th>
                            <th>Client</th>
                            <th>Uploaded</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr>
                            <td>
                                <i class="fas fa-file-alt me-2"></i>
                                {{ $document->original_name ?? 'Document' }}
                            </td>
                            <td>
                                <span class="badge bg-primary">#{{ $document->application->id }}</span>
                            </td>
                            <td>{{ $document->application->user->name ?? 'N/A' }}</td>
                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                            <td>{{ number_format($document->size / 1024, 2) }} KB</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="#" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $documents->links() }}
            @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Documents Found</h5>
                <p class="text-muted">Documents from your assigned cases will appear here.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection