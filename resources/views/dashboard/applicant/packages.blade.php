@extends('layouts.dashboard')

@section('title', 'Choose a Package')
@section('page-title', 'Choose a Package')

@section('styles')
<style>
  .app-card { background:#fff; border:1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 6px 20px rgba(17,24,39,0.06); }
  .app-card-header { background:#fff; border-bottom:1px solid #e5e7eb; border-top-left-radius:14px; border-top-right-radius:14px; }
  .price { font-weight: 800; font-size: 1.25rem; }
  .feature { font-size:.9rem; color:#374151; }
  .selected { border-color:#16a34a !important; box-shadow: 0 0 0 3px rgba(22,163,74,0.15); }
  .pill { display:inline-block; padding:.25rem .55rem; border-radius:999px; font-size:.7rem; font-weight:600; background:#ebf5ff; color:#0b5ed7; border:1px solid #b6dcff; }
  @media (max-width:576px){ .col-md-4{ width:100%; } }
</style>
@endsection

@section('content')
<div id="pkgRoot" class="row g-3"></div>
@endsection

@push('scripts')
<script>
(function(){
  const root = document.getElementById('pkgRoot');
  const appId = {{ $application->id }};

  function money(cents){ return new Intl.NumberFormat(undefined,{style:'currency',currency:'USD'}).format((cents||0)/100); }

  function render(data){
    root.innerHTML = '';
    if(!data.packages || data.packages.length===0){
      root.innerHTML = '<div class="col-12"><div class="alert alert-info">No packages available for this visa type yet.</div></div>';
      return;
    }
    data.packages.forEach(p=>{
      const col = document.createElement('div');
      col.className = 'col-md-4';
      col.innerHTML = `
        <div class="app-card h-100 ${p.selected ? 'selected':''}">
          <div class="app-card-header px-3 py-3 d-flex justify-content-between align-items-center">
            <strong>${p.name}</strong>
            ${p.selected ? '<span class="pill">Selected</span>':''}
          </div>
          <div class="p-3">
            <div class="price mb-2">${money(p.price_cents)}</div>
            <ul class="mb-3">
              ${(p.features||[]).map(f=>`<li class="feature">${f}</li>`).join('')}
            </ul>
            <button class="btn btn-primary w-100" data-id="${p.id}" ${p.selected?'disabled':''}>${p.selected?'Current Package':'Choose Package'}</button>
          </div>
        </div>`;
      root.appendChild(col);
    });
    root.querySelectorAll('button[data-id]').forEach(btn=>{
      btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.getAttribute('data-id');
        e.currentTarget.disabled = true;
        try {
          const res = await fetch(`{{ route('applications.packages.select', $application->id) }}`, {
            method: 'POST',
            headers: {
              'Content-Type':'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ package_id: id })
          });
          const json = await res.json();
          if(!res.ok){
            alert(json.error || 'Failed to select package');
            e.currentTarget.disabled = false; return;
          }
          load();
        } catch(err){
          alert('Network error');
          e.currentTarget.disabled = false;
        }
      });
    });
  }

  async function load(){
    root.innerHTML = '<div class="col-12"><div class="app-card"><div class="p-3">Loading packages…</div></div></div>';
    try {
      const res = await fetch(`{{ route('applications.packages.index', $application->id) }}` , {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      });
      const json = await res.json();
      render(json);
    } catch(err){
      root.innerHTML = '<div class="col-12"><div class="alert alert-danger">Failed to load packages.</div></div>';
    }
  }

  load();
})();
</script>
@endpush
