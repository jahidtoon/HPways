@extends('layouts.dashboard')

@section('title', 'Attorneys')
@section('page-title', 'Attorneys')

@section('content')
<div class="container-fluid">
    <!-- Attorneys List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Available Attorneys</h6>
            <small class="text-muted">{{ $attorneys->count() }} attorneys available</small>
        </div>
        <div class="card-body">
            @if($attorneys->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Cases Assigned</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attorneys as $attorney)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <div class="avatar-initial bg-primary rounded-circle">
                                                    {{ strtoupper(substr($attorney->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $attorney->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attorney->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $attorney->applications->count() }} cases</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $attorney->email }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-envelope"></i> Contact
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <h5>No Attorneys Available</h5>
                    <p class="text-muted">No attorneys are currently registered in the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        font-weight: 600;
        color: white;
    }
</style>
@endsection