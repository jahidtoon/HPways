@extends('layouts.dashboard')

@section('title', 'My Applications')
@section('page-title', 'My Applications')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Visa</th>
                    <th>Status</th>
                    <th>Package</th>
                    <th>Progress</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
                <tr>
                    <td>#{{ $app->id }}</td>
                    <td>{{ $app->visa_type ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ $app->status ?? 'new' }}</span></td>
                    <td>{{ $app->selectedPackage->name ?? '—' }}</td>
                    <td>
                        <div class="progress" style="height: 6px; width: 120px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ (int)($app->progress_pct ?? 0) }}%"></div>
                        </div>
                    </td>
                    <td>{{ optional($app->created_at)->format('M d, Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-primary">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted p-4">No applications yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
