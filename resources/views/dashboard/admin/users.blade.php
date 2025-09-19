@extends('layouts.dashboard')
@section('title', 'Applicant Management')
@section('page-title', 'Applicant Management')
@section('content')
<style>
    .dataTables_info,
    .dataTables_paginate { display: none !important; }
    .table + div:not(:has(.pagination)) { display: none; }
    /* Reduce extra spacing at bottom if any */
    .card .table { margin-bottom: 0; }
    .card .mt-3 { margin-top: 0.75rem !important; }
    .pagination { margin-bottom: 0; }
    .container { padding-bottom: 1rem; }
    .card-body { padding-bottom: 1rem; }
    .p-3.border-top ~ * { display: none; }
</style>
<div class="container">
    <h2 class="mb-4">Manage Applicants</h2>
    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">Back to Dashboard</a>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registration Date</th>
                        <th>Applications</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'Not provided' }}</td>
                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Unknown' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $user->applications ? $user->applications->count() : 0 }} applications</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-eye"></i> View Profile
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No applicants found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3 d-flex justify-content-center">
                {{ $users->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
