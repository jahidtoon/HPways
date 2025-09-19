@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell me-2 text-primary"></i>Notifications
        </h1>
        <div>
            <button class="btn btn-outline-primary me-2">
                <i class="fas fa-check-double me-1"></i> Mark All Read
            </button>
            <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Notifications -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
        </div>
        <div class="card-body">
            @if(count($notifications) > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                <div class="list-group-item border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if($notification['type'] == 'new_application')
                                <div class="bg-primary rounded-circle p-2">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                            @elseif($notification['type'] == 'document_uploaded')
                                <div class="bg-success rounded-circle p-2">
                                    <i class="fas fa-file-upload text-white"></i>
                                </div>
                            @elseif($notification['type'] == 'attorney_assigned')
                                <div class="bg-info rounded-circle p-2">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $notification['message'] }}</h6>
                            <small class="text-muted">{{ $notification['time'] }}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No New Notifications</h5>
                <p class="text-muted">You're all caught up! New notifications will appear here.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection