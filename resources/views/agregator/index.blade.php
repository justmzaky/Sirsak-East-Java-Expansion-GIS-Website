@extends('layouts.app')
@section('title', 'Agregator')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Agregator</span>
@endsection

@section('topbar-actions')
    @role('superadmin')
    <a href="{{ route('superadmin.pengiriman.index') }}" class="btn btn-green btn-sm"><i class="ti ti-truck-delivery"></i>Input Pengiriman</a>
    @endrole
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Agregator (Pengepul)</h1>
        <p>Monitoring inventori dan alur material di setiap agregator</p>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:8px">
        <span class="filter-label"><i class="ti ti-filter" style="font-size:14px;margin-right:3px"></i>Filter:</span>
        <select name="year" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($years as $y)<option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>@endforeach
        </select>
        <select name="month" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i=>$m)
                <option value="{{ $i+1 }}" {{ $month == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        @if($year || $month)<a href="{{ route('agregator.index') }}" class="btn btn-sm"><i class="ti ti-x"></i>Reset</a>@endif
    </form>
</div>

{{-- Material Summary --}}
<div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;justify-content:center">
    @foreach($materialSummary as $ms)
    @php $colors=['PET'=>'#16a34a','MLP'=>'#2563eb','Kardus'=>'#d97706','Metal'=>'#dc2626','HDPE'=>'#7c3aed','Campuran'=>'#0891b2']; @endphp
    <div style="flex:1;min-width:140px;max-width:200px;background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;border-bottom:4px solid {{ $colors[$ms->material_type] ?? '#94a3b8' }};text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.02)">
        <div style="font-size:13px;font-weight:800;color:{{ $colors[$ms->material_type] ?? '#94a3b8' }};text-transform:uppercase;letter-spacing:0.05em">{{ $ms->material_type }}</div>
        <div style="font-size:22px;font-weight:800;color:var(--text);margin-top:6px">{{ number_format($ms->total_kg,0,',','.') }} kg</div>
    </div>
    @endforeach
</div>

{{-- Aggregator Cards Grid --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px">
    @forelse($aggregators as $agg)
    <div class="card">
        <div class="card-header" style="background:#f8fafc">
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px">
                    <span class="badge badge-amber font-mono" style="font-size:11px">{{ $agg->code }}</span>
                    <span style="font-size:15px;font-weight:700;color:var(--text)">{{ $agg->name }}</span>
                </div>
                <div style="font-size:12px;color:var(--text3);margin-top:3px">📍 {{ $agg->village }}, {{ $agg->district }}, {{ $agg->regency }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:11px;color:var(--text3)">BSU Terhubung</div>
                <div style="font-size:18px;font-weight:700;color:var(--text)">{{ $agg->waste_units_count }}</div>
            </div>
        </div>
        <div class="card-body">
            {{-- Stock Inventory --}}
            <div style="margin-bottom:12px">
                <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">Stok Inventori Saat Ini</div>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    @forelse($agg->stocks->where('stock_kg','>',0) as $stock)
                    <div style="background:var(--green-light);border:1px solid var(--green-mid);border-radius:8px;padding:6px 12px;text-align:center">
                        <div style="font-size:10px;font-weight:700;color:var(--green-dark)">{{ $stock->material_type }}</div>
                        <div style="font-size:14px;font-weight:700;color:var(--text)">{{ number_format($stock->stock_kg,0,',','.') }}</div>
                        <div style="font-size:10px;color:var(--text3)">kg</div>
                    </div>
                    @empty
                    <span style="font-size:12.5px;color:var(--text3);font-style:italic">Stok kosong</span>
                    @endforelse
                </div>
            </div>

            {{-- Total received period --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fafc;border-radius:8px">
                <span style="font-size:12.5px;color:var(--text2)">Total diterima periode ini</span>
                <span style="font-size:15px;font-weight:700;color:var(--green)">{{ number_format($agg->total_kg ?? 0,0,',','.') }} kg</span>
            </div>

            <div style="margin-top:10px;text-align:right">
                <a href="{{ route('agregator.show', $agg->id) }}" class="btn btn-sm"><i class="ti ti-eye"></i>Detail & Riwayat</a>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1">
        <div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada data agregator</p></div>
    </div>
    @endforelse
</div>
@endsection
