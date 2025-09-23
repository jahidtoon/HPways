@extends('layouts.dashboard')

@section('title', 'Meetings')
@section('page-title', 'Meetings')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Requested and Scheduled Meetings</span>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Case</th>
                    <th>Applicant</th>
                    <th>Attorney</th>
                    <th>Status</th>
                    <th>Scheduled For</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $meeting)
                    <tr>
                        <td>#{{ $meeting->application_id }}</td>
                        <td>{{ $meeting->applicant->name ?? '—' }}</td>
                        <td>{{ $meeting->attorney->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span></td>
                        <td>{{ $meeting->scheduled_for ? $meeting->scheduled_for->format('M d, Y H:i') : '—' }}</td>
                        <td>
                            @if($meeting->status === 'requested')
                                <a class="btn btn-sm btn-primary" href="{{ route('dashboard.case-manager.meetings.schedule', $meeting->id) }}">Schedule</a>
                                <form action="{{ route('dashboard.case-manager.meetings.decline', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Decline this request?')">Decline</button>
                                </form>
                            @elseif(in_array($meeting->status, ['scheduled']))
                                <form action="{{ route('dashboard.case-manager.meetings.approve', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form action="{{ route('dashboard.case-manager.meetings.cancel', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this meeting?')">Cancel</button>
                                </form>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No meetings found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $meetings->links() }}
    </div>
</div>
@endsection
