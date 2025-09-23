@extends('layouts.dashboard')

@section('title', 'My Applications')
@section('page-title', 'My Applications')

@section('styles')
<style>
    .app-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
    .pill-status { display:inline-block; padding:.25rem .6rem; border-radius:999px; font-size:.72rem; font-weight:600; background:#ebf5ff; color:#0b5ed7; border:1px solid #b6dcff; text-transform:capitalize; }
    .progress-thin { height: 6px; border-radius: 999px; background:#eef2f7; }
    .progress-thin .bar { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); border-radius: 999px; }
    .table > :not(caption) > * > * { vertical-align: middle; }

    /* Mobile stacked table */
    @media (max-width: 576px) {
        .table thead { display: none; }
        .table tbody tr { display: block; background: #fff; margin-bottom: .75rem; border: 1px solid #e5e7eb; border-radius: 12px; padding: .5rem .75rem; }
        .table tbody td { display: flex; justify-content: space-between; align-items: center; border: 0 !important; padding: .4rem 0; }
        .table tbody td::before { content: attr(data-label); font-weight: 600; color: #374151; margin-right: 1rem; }
        .table tbody td.text-end { justify-content: flex-end; }
        .table-responsive { overflow: visible; }
    }
</style>
@endsection

@section('content')
<div class="app-card">
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
                    <td data-label="ID">#{{ $app->id }}</td>
                    <td data-label="Visa">{{ $app->visa_type ?? '—' }}</td>
                    <td data-label="Status"><span class="pill-status">{{ str_replace('_',' ', $app->status ?? 'new') }}</span></td>
                    <td data-label="Package">{{ $app->selectedPackage->name ?? '—' }}</td>
                    <td data-label="Progress">
                        <div class="progress-thin" style="width: 120px;">
                            <div class="bar" style="width: {{ (int)($app->progress_pct ?? 0) }}%"></div>
                        </div>
                    </td>
                    <td data-label="Created">{{ optional($app->created_at)->format('M d, Y') }}</td>
                    <td class="text-end" data-label=" ">
                        <a href="{{ route('dashboard.applicant.application.view', $app->id) }}" class="btn btn-sm btn-primary w-100 w-sm-auto">Open</a>
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
