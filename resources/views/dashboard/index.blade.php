@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $hasAdminRole = $user->hasRole(['admin', 'super-admin']);
    $hasApplicantRole = $user->hasRole('applicant');
@endphp

@if($hasAdminRole)
    {{-- Redirect admin users to admin dashboard --}}
    <script>
        window.location.href = "{{ route('admin.dashboard') }}";
    </script>
@else
    {{-- Redirect all other users to applicant dashboard --}}
    <script>
        window.location.href = "{{ route('dashboard.applicant.index') }}";
    </script>
@endif

{{-- Fallback content while redirecting --}}
<div class="container mx-auto px-4 py-8">
    <div class="text-center">
        <div class="animate-spin inline-block w-8 h-8 border-4 border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="loading">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-4 text-gray-600">Redirecting to your dashboard...</p>
    </div>
</div>
@endsection
