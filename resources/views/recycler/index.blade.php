@extends('layouts.app')
@section('title', 'Recycler')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Recycler</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Recycler (Daur Ulang)</h1>
        <p>Monitoring penerimaan material di fasilitas daur ulang</p>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:8px">
        <span class="filter-label"><i class="ti ti-filter" style="font-size:14px;margin-right:3px"></i>Filter:</span>
        <select name="year" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($years as $y)<option value="{{ $y }}" {{ $year==$y?'selected':'' }}>{{ $y }}</option>@endforeach
        </select>
        <select name="month" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i=>$m)
                <option value="{{ $i+1 }}" {{ $month==$i+1?'selected':'' }}>{{ $m }}</option>
            @endforeach
        </select>
        @if($year||$month)<a href="{{ route('recycler.index') }}" class="btn btn-sm"><i class="ti ti-x"></i>Reset</a>@endif
    </form>
</div>

<div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;justify-content:center">
    @foreach($summary as $s)
    @php $colors=['PET'=>'#16a34a','MLP'=>'#2563eb','Kardus'=>'#d97706','Metal'=>'#dc2626','HDPE'=>'#7c3aed','Campuran'=>'#0891b2']; @endphp
    <div style="flex:1;min-width:140px;max-width:200px;background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;border-bottom:4px solid {{ $colors[$s->material_type]??'#94a3b8' }};text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.02)">
        <div style="font-size:13px;font-weight:800;color:{{ $colors[$s->material_type]??'#94a3b8' }};text-transform:uppercase;letter-spacing:0.05em">{{ $s->material_type }}</div>
        <div style="font-size:22px;font-weight:800;color:var(--text);margin-top:6px">{{ number_format($s->total_kg,0,',','.') }} kg</div>
        <div style="font-size:11px;color:var(--text3);margin-top:4px">total diterima</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px">
    @forelse($recyclers as $rec)
    <div class="card">
        <div class="card-header" style="background:#faf5ff">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px">
                    <span class="badge badge-purple font-mono">{{ $rec->code }}</span>
                    <span style="font-size:15px;font-weight:700;color:var(--text)">{{ $rec->name }}</span>
                </div>
                <div style="font-size:12px;color:var(--text3)">{{ $rec->company_type }} &mdash; 📍 {{ $rec->regency }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:11px;color:var(--text3)">Pengiriman Masuk</div>
                <div style="font-size:18px;font-weight:700;color:var(--text)">{{ $rec->shipments_count }}</div>
            </div>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#faf5ff;border-radius:8px;margin-bottom:12px">
                <div>
                    <div style="font-size:11px;color:var(--text3)">Total Diterima (Periode)</div>
                    <div style="font-size:22px;font-weight:700;color:#7c3aed;margin-top:2px">{{ number_format($rec->total_received ?? 0,0,',','.') }} kg</div>
                </div>
                <i class="ti ti-building-factory-2" style="font-size:36px;color:#e9d5ff"></i>
            </div>
            @if($rec->pic_name || $rec->phone)
            <div style="display:flex;gap:12px;font-size:12px;color:var(--text3);margin-bottom:12px">
                @if($rec->pic_name)<span><i class="ti ti-user" style="margin-right:3px"></i>{{ $rec->pic_name }}</span>@endif
                @if($rec->phone)<span><i class="ti ti-phone" style="margin-right:3px"></i>{{ $rec->phone }}</span>@endif
            </div>
            @endif
            <div style="text-align:right">
                <a href="{{ route('recycler.show', $rec->id) }}" class="btn btn-sm"><i class="ti ti-eye"></i>Lihat Detail</a>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada recycler</p></div></div>
    @endforelse
</div>
@endsection
