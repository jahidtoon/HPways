@extends('layouts.dashboard')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-analytics me-2 text-primary"></i>Analytics
        </h1>
        <a href="{{ route('dashboard.case-manager.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        <!-- Status Distribution -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Application Status Distribution</h6>
                </div>
                <div class="card-body">
                    @foreach($statusDistribution as $status)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ ucfirst(str_replace('_', ' ', $status->status)) }}</span>
                            <span class="font-weight-bold">{{ $status->count }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($status->count / $statusDistribution->sum('count')) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Applications by Manager -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Applications by Case Manager</h6>
                </div>
                <div class="card-body">
                    @foreach($applicationsByManager as $manager)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ $manager->name }}</span>
                            <span class="font-weight-bold">{{ $manager->count }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ ($manager->count / $applicationsByManager->sum('count')) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection