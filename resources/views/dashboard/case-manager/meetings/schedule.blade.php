@extends('layouts.dashboard')

@section('title', 'Schedule Meeting')
@section('page-title', 'Schedule Meeting')

@section('content')
<div class="card">
    <div class="card-header">Schedule Meeting for Case #{{ $meeting->application_id }}</div>
    <div class="card-body">
        <form method="POST" action="{{ route('dashboard.case-manager.meetings.schedule.update', $meeting->id) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Topic</label>
                <input type="text" class="form-control" value="{{ $meeting->topic }}" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea class="form-control" rows="3" disabled>{{ $meeting->notes }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Scheduled For</label>
                    <input type="datetime-local" name="scheduled_for" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="form-control" min="15" max="240" value="30" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Provider</label>
                    <select class="form-select" name="provider">
                        <option value="zoom" selected>Zoom</option>
                        <option value="teams">Microsoft Teams</option>
                        <option value="google_meet">Google Meet</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Join URL</label>
                    <input type="url" name="join_url" class="form-control" placeholder="https://...">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Start URL (host)</label>
                <input type="url" name="start_url" class="form-control" placeholder="https://...">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">Save & Mark Scheduled</button>
                <a href="{{ route('dashboard.case-manager.meetings.index') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
