@extends('layouts.dashboard')

@section('title', request('bulk') ? 'Create Multiple Packages' : 'Create Package')
@section('page-title', request('bulk') ? 'Create Multiple Packages' : 'Create Package')

@section('content')
@if(request('bulk'))
    <!-- Bulk Create Form -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus"></i> Create Multiple Packages
                        </h5>
                        <div>
                            <a href="{{ route('admin.packages.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Packages
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Create all 3 package tiers (Basic, Advanced, Premium) for a new visa type at once.
                        </div>

                        <form action="{{ route('admin.packages.bulk-create') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label">Visa Type <span class="text-danger">*</span></label>
                                <input type="text" name="visa_type" class="form-control"
                                       value="{{ old('visa_type', request('visa_type')) }}"
                                       placeholder="e.g., I485, K1, I130" required>
                                <small class="text-muted">Enter the visa type for which you want to create packages</small>
                            </div>

                            <div class="row">
                                @php
                                    $tiers = [
                                        'basic' => ['name' => 'Basic Package', 'price' => '46.99', 'color' => 'primary'],
                                        'advanced' => ['name' => 'Advanced Package', 'price' => '79.99', 'color' => 'info'],
                                        'premium' => ['name' => 'Premium Package', 'price' => '109.99', 'color' => 'warning']
                                    ];
                                @endphp
                                @foreach($tiers as $tier => $config)
                                    <div class="col-md-4 mb-4">
                                        <div class="card border-{{ $config['color'] }}">
                                            <div class="card-header bg-{{ $config['color'] }} text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-{{ $tier === 'basic' ? 'star' : ($tier === 'advanced' ? 'rocket' : 'crown') }}"></i>
                                                    {{ $config['name'] }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <input type="hidden" name="packages[{{ $tier }}][code]" value="{{ $tier }}">

                                                <div class="mb-3">
                                                    <label class="form-label">Package Name</label>
                                                    <input type="text" class="form-control" name="packages[{{ $tier }}][name]"
                                                           value="{{ old('packages.' . $tier . '.name', $config['name']) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Price (USD)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="packages[{{ $tier }}][price]"
                                                               value="{{ old('packages.' . $tier . '.price', $config['price']) }}"
                                                               step="0.01" min="0" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Features</label>
                                                    <div id="features-{{ $tier }}">
                                                        @php
                                                            $features = old('packages.' . $tier . '.features', [
                                                                'Professional consultation',
                                                                'Document review',
                                                                'Application preparation'
                                                            ]);
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
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Create All 3 Packages
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Single Create Form -->
    <div class="container" style="max-width:820px;">
        <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-link mb-3"><i class="fas fa-arrow-left"></i> Back</a>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.packages.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Visa Type (optional)</label>
                            <input type="text" name="visa_type" value="{{ old('visa_type', request('pref_visa_type')) }}" list="visaTypes" class="form-control">
                            <datalist id="visaTypes">
                                @foreach($visaTypes as $vt)
                                    <option value="{{ $vt }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" value="{{ old('code', request('pref_code')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price (USD)</label>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Features (one per line)</label>
                            <textarea name="features_raw" class="form-control" rows="5" placeholder="Fast processing\nAttorney review\nPriority support" oninput="syncFeatures(this)">{{ old('features_raw') }}</textarea>
                            <small class="text-muted">Converted to array below (editable JSON).</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Features JSON</label>
                            <textarea name="features_json" class="form-control" rows="4" oninput="syncRaw(this)">[]</textarea>
                        </div>
                        <div class="col-md-3 form-check ms-2">
                            <input class="form-check-input" type="checkbox" name="active" value="1" checked id="activeChk">
                            <label class="form-check-label" for="activeChk">Active</label>
                        </div>
                    </div>
                    <input type="hidden" name="features[]" id="featuresArrayHolder">
                    <input type="hidden" name="visa_category_id" value="{{ request('pref_category') }}">
                    <div class="mt-4">
                        <button class="btn btn-primary">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@if(request('bulk'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle adding features
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
@else
@push('scripts')
<script>
function syncFeatures(area){
    const lines = area.value.split(/\n+/).map(l=>l.trim()).filter(Boolean);
    document.querySelector('textarea[name="features_json"]').value = JSON.stringify(lines,null,2);
    updateHidden(lines);
}
function syncRaw(area){
    try { const arr = JSON.parse(area.value); updateHidden(arr); }
    catch(e) { /* ignore */ }
}
function updateHidden(arr){
    // Remove existing hidden feature inputs
    document.querySelectorAll('input[name="features[]"]').forEach(el=>el.remove());
    const form = document.querySelector('form');
    arr.forEach(v=>{
        const i=document.createElement('input');
        i.type='hidden'; i.name='features[]'; i.value=v; form.appendChild(i);
    });
}
</script>
@endpush
@endif
