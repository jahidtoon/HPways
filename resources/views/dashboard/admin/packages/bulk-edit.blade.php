@extends('layouts.dashboard')

@section('title', 'Bulk Edit Packages - ' . $visaType)
@section('page-title', 'Bulk Edit Packages')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Bulk Edit Packages for {{ $visaType }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.packages.index', ['visa_type' => $visaType]) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Packages
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.packages.bulk-update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="visa_type" value="{{ $visaType }}">

                        <div class="row">
                            @php
                                $tiers = ['basic', 'advanced', 'premium'];
                            @endphp
                            @foreach($tiers as $tier)
                                @php
                                    $package = $packages[$tier] ?? null;
                                @endphp
                                <div class="col-md-4 mb-4">
                                    <div class="card border {{ $package ? 'border-primary' : 'border-warning' }}">
                                        <div class="card-header {{ $package ? 'bg-primary text-white' : 'bg-warning' }}">
                                            <h6 class="mb-0 text-capitalize">
                                                <i class="fas fa-{{ $tier === 'basic' ? 'star' : ($tier === 'advanced' ? 'rocket' : 'crown') }}"></i>
                                                {{ $tier }} Package
                                                @if(!$package)
                                                    <span class="badge bg-danger ms-2">Missing</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if($package)
                                                <input type="hidden" name="packages[{{ $tier }}][id]" value="{{ $package->id }}">

                                                <div class="mb-3">
                                                    <label class="form-label">Package Name</label>
                                                    <input type="text" class="form-control" name="packages[{{ $tier }}][name]"
                                                           value="{{ old('packages.' . $tier . '.name', $package->name) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Price (USD)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="packages[{{ $tier }}][price_cents]"
                                                               value="{{ old('packages.' . $tier . '.price_cents', $package->price_cents / 100) }}"
                                                               step="0.01" min="0" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Features</label>
                                                    <div id="features-{{ $tier }}">
                                                        @php
                                                            $features = old('packages.' . $tier . '.features', $package->features ?? []);
                                                        @endphp
                                                        @foreach($features as $index => $feature)
                                                            <div class="input-group mb-2">
                                                                <input type="text" class="form-control" name="packages[{{ $tier }}][features][{{ $index }}]"
                                                                       value="{{ $feature }}" placeholder="Feature description">
                                                                <button type="button" class="btn btn-outline-danger remove-feature" type="button">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm add-feature" data-tier="{{ $tier }}">
                                                        <i class="fas fa-plus"></i> Add Feature
                                                    </button>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="packages[{{ $tier }}][active]"
                                                               value="1" {{ old('packages.' . $tier . '.active', $package->active) ? 'checked' : '' }}>
                                                        <label class="form-check-label">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    This {{ $tier }} package does not exist yet.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.packages.index', ['visa_type' => $visaType]) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update All Packages
                            </button>
                        </div>
                    </form>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle adding features to existing packages
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-feature') || e.target.closest('.add-feature')) {
            const button = e.target.closest('.add-feature');
            const tier = button.getAttribute('data-tier');
            const container = document.getElementById('features-' + tier);

            const featureDiv = document.createElement('div');
            featureDiv.className = 'input-group mb-2';
            featureDiv.innerHTML = `
                <input type="text" class="form-control" name="packages[${tier}][features][]" placeholder="Feature description">
                <button type="button" class="btn btn-outline-danger remove-feature">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(featureDiv);
        }

        // Handle removing features
        if (e.target.classList.contains('remove-feature') || e.target.closest('.remove-feature')) {
            e.target.closest('.input-group').remove();
        }
    });
});
</script>
@endpush