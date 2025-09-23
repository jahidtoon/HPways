@extends('layouts.dashboard')

@section('title', 'My Meetings')
@section('page-title', 'My Meetings')

@section('content')
<div class="card">
    <div class="card-header">Upcoming and Past Meetings</div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Case</th>
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
                        <td>{{ $meeting->attorney->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span></td>
                        <td>{{ $meeting->scheduled_for ? $meeting->scheduled_for->format('M d, Y H:i') : '—' }}</td>
                        <td>
                            @if($meeting->join_url && in_array($meeting->status, ['scheduled','approved']))
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ $meeting->join_url }}"><i class="fas fa-video me-1"></i>Join</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No meetings found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $meetings->links() }}
    </div>
    <div class="card-footer small text-muted">Contact your case manager for schedule changes.</div>
    </div>
@endsection
