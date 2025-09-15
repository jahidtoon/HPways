@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" value="{{ $user->name ?? '' }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="{{ $user->email ?? '' }}" disabled>
        </div>
        <p class="text-muted small mb-0">Profile editing coming soon.</p>
    </div>
</div>
@endsection
