@extends('layouts.dashboard')
@section('title','Edit Package')
@section('page-title','Edit Package')
@section('content')
<div class="container" style="max-width:820px;">
    <a href="{{ route('admin.packages.index') }}" class="btn btn-sm btn-link mb-3"><i class="fas fa-arrow-left"></i> Back</a>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.packages.update',$package) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Visa Type (optional)</label>
                        <input type="text" name="visa_type" value="{{ old('visa_type',$package->visa_type) }}" list="visaTypes" class="form-control">
                        <datalist id="visaTypes">
                            @foreach($visaTypes as $vt)
                                <option value="{{ $vt }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" value="{{ old('code',$package->code) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price (USD)</label>
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price',$package->price_cents/100) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name',$package->name) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Features (one per line)</label>
                        <textarea name="features_raw" class="form-control" rows="5" oninput="syncFeatures(this)">{{ implode("\n", old('features_raw', $package->features ?? [])) }}</textarea>
                        <small class="text-muted">Converted to array below (editable JSON).</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Features JSON</label>
                        <textarea name="features_json" class="form-control" rows="4" oninput="syncRaw(this)">{{ json_encode($package->features ?? [], JSON_PRETTY_PRINT) }}</textarea>
                    </div>
                    <div class="col-md-3 form-check ms-2">
                        <input class="form-check-input" type="checkbox" name="active" value="1" id="activeChk" {{ $package->active ? 'checked' : '' }}>
                        <label class="form-check-label" for="activeChk">Active</label>
                    </div>
                </div>
                <div id="hiddenFeatures"></div>
                <hr class="my-4"/>
                <h5 class="mb-2">Required Documents</h5>
                <p class="text-muted" style="margin-top:-.25rem">এই প্যাকেজের জন্য আবেদনকারীর কাছ থেকে কোন কোন ডকুমেন্ট লাগবে—এখানে সেট করুন।</p>
                
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:160px;">Code</th>
                                <th>Label</th>
                                <th style="width:90px;">Required</th>
                                <th style="width:120px;">Translation OK</th>
                                <th style="width:80px;">Active</th>
                                <th style="width:70px;"></th>
                            </tr>
                        </thead>
                        <tbody id="docsBody">
                            @foreach(($package->requiredDocuments ?? []) as $d)
                                <tr>
                                    <td>
                                        <input type="hidden" name="documents[{{ $loop->index }}][id]" value="{{ $d->id }}">
                                        <input type="text" class="form-control form-control-sm" name="documents[{{ $loop->index }}][code]" value="{{ $d->code }}" required>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm" name="documents[{{ $loop->index }}][label]" value="{{ $d->label }}" required></td>
                                    <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[{{ $loop->index }}][required]" {{ $d->required ? 'checked' : '' }}></td>
                                    <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[{{ $loop->index }}][translation_possible]" {{ $d->translation_possible ? 'checked' : '' }}></td>
                                    <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[{{ $loop->index }}][active]" {{ $d->active ? 'checked' : '' }}></td>
                                    <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Remove</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDocRow()">+ Add Document</button>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary">Update Package</button>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
let docIndex = {{ ($package->requiredDocuments?->count() ?? 0) }};
function init(){
    const raw = document.querySelector('textarea[name="features_json"]').value;
    try { const arr=JSON.parse(raw); updateHidden(arr); document.querySelector('textarea[name="features_raw"]').value = arr.join('\n'); }catch(e){}
}
function syncFeatures(area){
    const lines = area.value.split(/\n+/).map(l=>l.trim()).filter(Boolean);
    document.querySelector('textarea[name="features_json"]').value = JSON.stringify(lines,null,2);
    updateHidden(lines);
}
function syncRaw(area){
    try { const arr = JSON.parse(area.value); updateHidden(arr); document.querySelector('textarea[name="features_raw"]').value = arr.join('\n'); }
    catch(e) { /* ignore */ }
}
function updateHidden(arr){
    document.querySelectorAll('#hiddenFeatures input').forEach(el=>el.remove());
    const holder=document.getElementById('hiddenFeatures');
    arr.forEach(v=>{ const i=document.createElement('input'); i.type='hidden'; i.name='features[]'; i.value=v; holder.appendChild(i); });
}
function addDocRow(){
    const tbody = document.getElementById('docsBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" class="form-control form-control-sm" name="documents[${docIndex}][code]" placeholder="CODE" required></td>
        <td><input type="text" class="form-control form-control-sm" name="documents[${docIndex}][label]" placeholder="Label" required></td>
        <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[${docIndex}][required]" checked></td>
        <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[${docIndex}][translation_possible]"></td>
        <td class="text-center"><input type="checkbox" class="form-check-input" name="documents[${docIndex}][active]" checked></td>
        <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">Remove</button></td>
    `;
    tbody.appendChild(tr);
    docIndex++;
}
function removeRow(btn){
    const tr = btn.closest('tr');
    tr?.remove();
}
init();
</script>
@endsection
