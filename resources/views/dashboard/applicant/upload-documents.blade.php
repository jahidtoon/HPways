@extends('layouts.dashboard')

@section('title', 'Upload Documents')
@section('page-title', 'Upload Documents')

@section('styles')
<style>
  .app-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
  .app-card-header { background: #fff; border-bottom: 1px solid #e5e7eb; border-top-left-radius: 14px; border-top-right-radius: 14px; }
  .meta { font-size: .85rem; color: #6b7280; }
  .form-select { border-radius: 10px; }
  .form-control { border-radius: 10px; }
  .btn { border-radius: 10px; }
  .dropzone { background: #fafafa; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 18px; transition: background .2s, border-color .2s; }
  .dropzone.dragover { background: #f0f9ff; border-color: #38bdf8; }
  .pill { display: inline-block; padding: .25rem .55rem; border-radius: 999px; font-size: .7rem; font-weight: 600; }
  .pill-uploaded { background: #e6fffa; color: #0f766e; border: 1px solid #99f6e4; }
  .pill-pending { background: #fff7ed; color: #b45309; border: 1px solid #fed7aa; }
  .chip { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: .15rem .45rem; border-radius: 8px; font-size: .68rem; font-weight: 700; }
  .doc-item { border-bottom: 1px dashed #e5e7eb; padding: .65rem 0; }
  .doc-item:last-child { border-bottom: 0; }
  .help { font-size: .75rem; color: #6b7280; }

  /* Mobile tweaks */
  @media (max-width: 576px) {
    .app-card-header { padding: .75rem 1rem !important; }
    .p-3.p-md-4 { padding: 1rem !important; }
    .d-flex.gap-2 > * { flex: 1 1 auto; }
    .dropzone { padding: 16px; }
    .doc-item { padding: .6rem 0; }
  }
</style>
@endsection

@section('content')
@if(!$currentApplication)
  <div class="alert alert-warning">
    <strong>No Application Found</strong>
    <p>Please <a href="{{ route('dashboard.applicant.index') }}">return to dashboard</a> and try again.</p>
  </div>
@else
<div class="row g-3">
  <div class="col-lg-7">
    <div class="app-card h-100">
      <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold">Upload to Case #{{ $currentApplication->id }}</div>
          <div class="meta">Visa: {{ $currentApplication->visa_type ?? '—' }}</div>
        </div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.applicant.documents') }}">Back</a>
      </div>
      <div class="p-3 p-md-4">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <form id="uploadForm" method="post" action="{{ route('applications.documents.store', $currentApplication->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">Document Type</label>
            <select name="type" class="form-select" required id="docTypeSelect">
              <option value="">Select type…</option>
              @forelse(($required ?? []) as $r)
                <option value="{{ $r['code'] }}" data-translation-possible="{{ !empty($r['translation_possible']) ? '1' : '0' }}">{{ $r['label'] }} (Required)</option>
              @empty
                <!-- No required docs available -->
              @endforelse
              @forelse(($optional ?? []) as $r)
                <option value="{{ $r['code'] }}" data-translation-possible="{{ !empty($r['translation_possible']) ? '1' : '0' }}">{{ $r['label'] }} (Optional)</option>
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
            <div id="fileDropzone" class="dropzone text-center">
              <div class="mb-2"><i class="fas fa-cloud-upload-alt me-1"></i> Drag & drop file here, or click to browse</div>
              <input id="fileInput" type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="display:none" required>
              <div id="fileMeta" class="help">Accepted: PDF, JPG, JPEG, PNG (max 5MB)</div>
            </div>
          </div>
          <div id="translationWrap" class="mb-3" style="display:none">
            <div class="form-check mb-1">
              <input class="form-check-input" type="checkbox" name="needs_translation" value="1" id="needs_translation">
              <label class="form-check-label" for="needs_translation">Needs translation</label>
            </div>
            <div class="help">If selected, upload must include a translation by a competent translator with certification per USCIS standards (see note below).</div>
          </div>
          <div class="d-flex gap-2">
            <button id="uploadBtn" class="btn btn-primary"><i class="fas fa-upload me-1"></i>Upload</button>
            <a href="{{ route('dashboard.applicant.documents') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="app-card h-100">
      <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
        <div><strong>Required & Optional</strong>
        @if($currentApplication->selectedPackage)
          <small class="text-muted">(Package: {{ $currentApplication->selectedPackage->name }})</small></div>
        @endif
      </div>
      <div class="p-3 p-md-4">
        <h6 class="text-muted mb-2">Required Documents</h6>
        <div class="mb-3">
          @forelse(($required ?? []) as $r)
            <div class="doc-item d-flex justify-content-between align-items-start">
              <div>
                <div>{{ $r['label'] }}</div>
                <div class="mt-1"><span class="chip">{{ $r['code'] }}</span></div>
              </div>
              <div>
                @if($r['uploaded'] ?? false)
                  <span class="pill pill-uploaded"><i class="fas fa-check me-1"></i>Uploaded</span>
                @else
                  <span class="pill pill-pending"><i class="fas fa-clock me-1"></i>Pending</span>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">No required documents defined.</div>
          @endforelse
        </div>
        <h6 class="text-muted mb-2">Optional Documents</h6>
        <div>
          @forelse(($optional ?? []) as $r)
            <div class="doc-item d-flex justify-content-between align-items-start">
              <div>
                <div>{{ $r['label'] }}</div>
                @if(!empty($r['code']))<div class="mt-1"><span class="chip">{{ $r['code'] }}</span></div>@endif
              </div>
              <div>
                @if($r['uploaded'] ?? false)
                  <span class="pill pill-uploaded"><i class="fas fa-check me-1"></i>Uploaded</span>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">No optional documents defined.</div>
          @endforelse
        </div>
        
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

@push('scripts')
<script>
  (function(){
    // AJAX submit to avoid navigating to JSON and prevent 419 on refresh
    const form = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    if (form && uploadBtn) {
      form.addEventListener('submit', async function(e){
        e.preventDefault();
        uploadBtn.disabled = true;
        try {
          const fd = new FormData(form);
          const res = await fetch(form.action, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin',
            body: fd
          });
          if (!res.ok) {
            const err = await res.json().catch(()=>({message:'Upload failed'}));
            alert(err.message || 'Upload failed');
            uploadBtn.disabled = false;
            return;
          }
          // Refresh to reflect updated required/optional states
          window.location.href = `{{ route('applications.documents.index', $currentApplication->id) }}`;
        } catch (err) {
          alert('Network error while uploading');
          uploadBtn.disabled = false;
        }
      });
    }

    const dz = document.getElementById('fileDropzone');
    const input = document.getElementById('fileInput');
    const meta = document.getElementById('fileMeta');
    if (!dz || !input || !meta) return;

    function human(bytes){
      const i = bytes === 0 ? 0 : Math.floor(Math.log(bytes) / Math.log(1024));
      const sizes = ['B','KB','MB','GB'];
      return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
    }

    dz.addEventListener('click', () => input.click());
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
    dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
    dz.addEventListener('drop', e => {
      e.preventDefault(); dz.classList.remove('dragover');
      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });
    input.addEventListener('change', () => {
      const f = input.files && input.files[0];
      if (!f) { meta.textContent = 'Accepted: PDF, JPG, JPEG, PNG (max 5MB)'; return; }
      if (f.size > 5 * 1024 * 1024) {
        alert('File is larger than 5MB. Please choose a smaller file.');
        input.value = ''; meta.textContent = 'Accepted: PDF, JPG, JPEG, PNG (max 5MB)';
        return;
      }
      meta.textContent = f.name + ' • ' + human(f.size);
    });

    // Toggle translation option based on selected doc type's translation_possible flag
    const typeSelect = document.getElementById('docTypeSelect');
    const translationWrap = document.getElementById('translationWrap');
    if (typeSelect && translationWrap) {
      const toggle = () => {
        const opt = typeSelect.options[typeSelect.selectedIndex];
        const can = opt && opt.dataset && opt.dataset.translationPossible === '1';
        translationWrap.style.display = can ? '' : 'none';
        if (!can) {
          const cb = document.getElementById('needs_translation');
          if (cb) cb.checked = false;
        }
      };
      typeSelect.addEventListener('change', toggle);
      toggle();
    }
  })();
  </script>
@endpush
