@extends('layouts.dashboard')

@section('title', 'Meetings')
@section('page-title', 'All Meetings')

@section('content')
<div class="card">
    <div class="card-header">Meetings Overview</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Case</th>
                    <th>Applicant</th>
                    <th>Attorney</th>
                    <th>Case Manager</th>
                    <th>Status</th>
                    <th>Scheduled For</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $meeting)
                    <tr>
                        <td>{{ $meeting->id }}</td>
                        <td>#{{ $meeting->application_id }}</td>
                        <td>{{ $meeting->applicant->name ?? '—' }}</td>
                        <td>{{ $meeting->attorney->name ?? '—' }}</td>
                        <td>{{ $meeting->caseManager->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span></td>
                        <td>{{ $meeting->scheduled_for ? $meeting->scheduled_for->format('M d, Y H:i') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No meetings found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $meetings->links() }}
    </div>
</div>
@endsection
