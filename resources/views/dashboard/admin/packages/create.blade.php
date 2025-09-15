@extends('layouts.dashboard')
@section('title','Create Package')
@section('page-title','Create Package')
@section('content')
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
@endsection
@section('scripts')
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
@endsection
