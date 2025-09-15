@extends('layouts.dashboard')

@section('title', 'Upload Documents')
@section('page-title', 'Upload Documents')

@section('content')
@if(!$currentApplication)
  <div class="alert alert-warning">
    <strong>No Application Found</strong>
    <p>Please <a href="{{ route('dashboard.applicant.index') }}">return to dashboard</a> and try again.</p>
  </div>
@else
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Upload to Case #{{ $currentApplication->id }} ({{ $currentApplication->visa_type ?? 'No visa type' }})</strong>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.applicant.documents') }}">Back</a>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <form method="post" action="{{ route('applications.documents.store', $currentApplication->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Document Type</label>
            <select name="type" class="form-select" required>
              <option value="">Select type…</option>
              @forelse(($required ?? []) as $r)
                <option value="{{ $r['code'] }}">{{ $r['label'] }} (Required)</option>
              @empty
                <!-- No required docs available -->
              @endforelse
              @forelse(($optional ?? []) as $r)
                <option value="{{ $r['code'] }}">{{ $r['label'] }} (Optional)</option>
              @empty
                <!-- No optional docs available -->
              @endforelse
              @if(empty($required) && empty($optional))
                <option value="GENERAL">General Document</option>
              @endif
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">File</label>
            <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
            <div class="form-text">Accepted: PDF, JPG, JPEG, PNG (max 5MB)</div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="needs_translation" value="1" id="needs_translation">
            <label class="form-check-label" for="needs_translation">Needs translation</label>
          </div>
          <button class="btn btn-primary">Upload</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white">
        <strong>Required & Optional</strong>
        @if($currentApplication->selectedPackage)
          <small class="text-muted">(Package: {{ $currentApplication->selectedPackage->name }})</small>
        @endif
      </div>
      <div class="card-body">
        <h6 class="text-muted mb-2">Required Documents</h6>
        <ul class="list-group list-group-flush mb-3">
          @forelse(($required ?? []) as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>{{ $r['label'] }} <span class="badge bg-light text-muted ms-2">{{ $r['code'] }}</span></span>
              @if($r['uploaded'] ?? false)
                <span class="badge bg-success">✓ Uploaded</span>
              @else
                <span class="badge bg-warning">Pending</span>
              @endif
            </li>
          @empty
            <li class="list-group-item text-muted">No required documents defined.</li>
          @endforelse
        </ul>
        <h6 class="text-muted mb-2">Optional Documents</h6>
        <ul class="list-group list-group-flush">
          @forelse(($optional ?? []) as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>{{ $r['label'] }} @if(!empty($r['code']))<span class="badge bg-light text-muted ms-2">{{ $r['code'] }}</span>@endif</span>
              @if($r['uploaded'] ?? false)
                <span class="badge bg-success">✓ Uploaded</span>
              @endif
            </li>
          @empty
            <li class="list-group-item text-muted">No optional documents defined.</li>
          @endforelse
        </ul>
        
        @if(empty($required) && empty($optional))
          <div class="alert alert-info">
            <strong>Debug Info:</strong><br>
            Visa Type: {{ $currentApplication->visa_type ?? 'None' }}<br>
            Package: {{ $currentApplication->selectedPackage->name ?? 'None' }}<br>
            Package ID: {{ $currentApplication->selectedPackage->id ?? 'None' }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
@endsection
