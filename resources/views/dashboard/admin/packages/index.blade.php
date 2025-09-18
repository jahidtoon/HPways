@extends('layouts.dashboard')
@section('title','Packages')
@section('page-title','Packages')
@section('content')
<style>
    .admin-pricing-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;}
    .admin-pricing-card{border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;background:#fff;display:flex;flex-direction:column;position:relative;box-shadow:0 4px 10px rgba(0,0,0,0.04);}
    .admin-pricing-card.tier-basic .tier-head{background:#2a3b4d;}
    .admin-pricing-card.tier-advanced .tier-head{background:#4e73df;}
    .admin-pricing-card.tier-premium .tier-head{background:#d32f2f;}
    .admin-pricing-card .tier-head{color:#fff;padding:0.85rem 1rem;font-weight:600;font-size:1.05rem;display:flex;align-items:center;justify-content:space-between;}
    .admin-pricing-card .price{font-size:1.6rem;font-weight:700;color:#1e3c72;margin-bottom:.25rem;}
    .admin-pricing-card .body{padding:1rem 1.1rem 1.1rem;display:flex;flex-direction:column;flex-grow:1;}
    .admin-pricing-card ul{list-style:none;padding:0;margin:0 0 .75rem;flex-grow:1;font-size:.8rem;}
    .admin-pricing-card ul li{display:flex;align-items:flex-start;gap:.4rem;margin:.35rem 0;}
    .admin-pricing-card ul li:before{content:"✔";color:#4f8cff;font-size:.75rem;line-height:1.1rem;}
    .admin-pricing-card .actions{display:flex;gap:.4rem;flex-wrap:wrap;}
    .admin-pricing-card .badge-status{position:absolute;top:.6rem;right:.6rem;}
    .admin-section-header{display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between;margin-bottom:1.25rem;}
    .visa-type-chip{padding:.35rem .7rem;border:1px solid #cfd4dc;border-radius:20px;font-size:.75rem;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;background:#f8fafc;transition:.2s;}
    .visa-type-chip.active{background:#1e3c72;color:#fff;border-color:#1e3c72;}
    .category-block{margin-bottom:2.5rem;}
    .category-title{font-weight:600;font-size:1rem;margin:0 0 .75rem;color:#374151;display:flex;align-items:center;gap:.5rem;}
    .category-title span{font-size:.7rem;font-weight:500;color:#6b7280;}
    .missing-card{border:2px dashed #d1d5db;border-radius:16px;min-height:180px;padding:1rem;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:.85rem;color:#6b7280;background:#fafafa;}
    .missing-card a{margin-top:.5rem;}
    .filter-bar{background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:.75rem 1rem;display:flex;flex-wrap:wrap;gap:.5rem;}
    .search-box{min-width:240px;}
    @media (max-width: 600px){.admin-pricing-grid{grid-template-columns:repeat(auto-fill,minmax(200px,1fr));}}
</style>
<div class="container-fluid">
    <div class="admin-section-header">
        <h2 class="h4 mb-0">Packages</h2>
        <div class="d-flex gap-2 ms-auto">
            <!-- Package creation disabled -->
        </div>
    </div>
    <form method="GET" class="filter-bar mb-4" id="filterForm">
        <div class="d-flex gap-2 flex-wrap align-items-center" style="flex:1 1 auto;">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control search-box" placeholder="Search name / code / visa type">
            <input type="hidden" name="visa_type" value="{{ request('visa_type') }}" id="visaTypeInput">
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <strong style="font-size:.7rem;letter-spacing:.5px;color:#6b7280;">VISA TYPES:</strong>
                @foreach($visaTypes as $vt)
                    <div class="visa-type-chip {{ request('visa_type') == $vt ? 'active' : '' }}" data-vt="{{ $vt }}">{{ $vt }}</div>
                @endforeach
            </div>
        </div>
        <div class="ms-auto d-flex gap-2">
            <button class="btn btn-outline-secondary" type="submit">Apply</button>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-light border">Reset</a>
        </div>
    </form>
    @foreach($grouped as $visaType => $categories)
        <div class="mb-5">
            <div class="d-flex align-items-center mb-3" style="gap:.75rem;">
                <h5 class="mb-0" style="font-weight:700;color:#1e3c72;">{{ $visaType === 'GLOBAL' ? 'Global Packages (All Visa Types)' : 'Visa Type: ' . $visaType }}</h5>
                <span class="badge bg-light text-dark border">{{ count($categories) }} categories</span>
                @if($visaType !== 'GLOBAL')
                    <a href="{{ route('admin.packages.bulk-edit', ['visa_type' => $visaType]) }}" class="btn btn-outline-primary btn-sm ms-2">
                        <i class="fas fa-edit"></i> Bulk Edit All
                    </a>
                @endif
            </div>
            @foreach($categories as $catId => $block)
                @php($cat = $block['category'])
                @php($pkgs = $block['packages'])
                @php($missing = array_values(array_diff($tiersExpected, array_keys($pkgs))))
                <div class="category-block">
                    <div class="category-title">{{ $cat?->name ?? 'Uncategorized' }} <span>ID: {{ $catId ?: '—' }}</span>
                        @if(empty($missing))
                            <span class="badge bg-success ms-2">Complete</span>
                        @else
                            <span class="badge bg-warning text-dark ms-2">{{ count($missing) }} Missing</span>
                        @endif
                    </div>
                    <div class="admin-pricing-grid">
                        @foreach($tiersExpected as $tier)
                            @php($pkg = $pkgs[$tier] ?? null)
                            @if($pkg)
                                <div class="admin-pricing-card tier-{{ $tier }}">
                                    <div class="tier-head">
                                        <span class="text-capitalize">{{ $tier }}</span>
                                        <form action="{{ route('admin.packages.toggle',$pkg) }}" method="POST" class="m-0 p-0">
                                            @csrf @method('PATCH')
                                            <button class="badge border-0 {{ $pkg->active ? 'bg-success' : 'bg-secondary' }}">{{ $pkg->active ? 'On' : 'Off' }}</button>
                                        </form>
                                    </div>
                                    <div class="body">
                                        <div class="price">${{ number_format($pkg->price_cents/100,2) }}</div>
                                        <div class="small mb-2 fw-semibold text-truncate">{{ $pkg->name }}</div>
                                        <ul>
                                            @foreach(($pkg->features ?? []) as $f)
                                                <li>{{ $f }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="actions">
                                            <a href="{{ route('admin.packages.edit',$pkg) }}" class="btn btn-outline-primary btn-sm flex-fill">Edit</a>
                                            <form action="{{ route('admin.packages.destroy',$pkg) }}" method="POST" onsubmit="return confirm('Delete this package?')" class="flex-fill">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm w-100">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="missing-card">
                                    <div><strong class="text-capitalize">{{ $tier }}</strong> missing</div>
                                    <button class="btn btn-sm btn-outline-secondary mt-2" disabled>Cannot Add (Disabled)</button>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    <div class="mb-4">{{ $packages->links() }}</div>
</div>
@section('scripts')
<script>
document.querySelectorAll('.visa-type-chip').forEach(chip=>{
    chip.addEventListener('click',()=>{
        const vt = chip.getAttribute('data-vt');
        document.getElementById('visaTypeInput').value = (document.getElementById('visaTypeInput').value===vt)?'':vt;
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection
@endsection
