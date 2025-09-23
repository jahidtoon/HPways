@extends('layouts.dashboard')

@section('title', 'Meetings')
@section('page-title', 'My Meetings')

@section('content')
<div class="card">
    <div class="card-header">Requested and Scheduled Meetings</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Case</th>
                    <th>Applicant</th>
                    <th>Status</th>
                    <th>Scheduled For</th>
                    <th>Links</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($meetings as $meeting)
                    <tr>
                        <td>#{{ $meeting->application_id }}</td>
                        <td>{{ $meeting->applicant->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span></td>
                        <td>{{ $meeting->scheduled_for ? $meeting->scheduled_for->format('M d, Y H:i') : '—' }}</td>
                        <td>
                            @if($meeting->start_url && in_array($meeting->status, ['scheduled','approved']))
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ $meeting->start_url }}">Start</a>
                            @endif
                            @if($meeting->join_url && in_array($meeting->status, ['scheduled','approved']))
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ $meeting->join_url }}">Join</a>
                            @endif
                        </td>
                        <td>
                            @if(in_array($meeting->status, ['requested','scheduled','approved']))
                                <form action="{{ route('dashboard.attorney.meetings.cancel', $meeting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this meeting?')">Cancel</button>
                                </form>
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
