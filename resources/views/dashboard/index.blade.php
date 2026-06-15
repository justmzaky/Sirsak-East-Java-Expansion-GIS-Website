@extends('layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Dashboard</span>
@endsection

@section('topbar-actions')
    <form method="GET" action="{{ route('dashboard') }}" style="display:flex;gap:8px;align-items:center">
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
        @if($year || $month)
        <a href="{{ route('dashboard') }}" class="btn btn-sm"><i class="ti ti-x"></i>Reset</a>
        @endif
    </form>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Dashboard Traceability</h1>
        <p>Ringkasan alur material sampah Jawa Timur &mdash; diperbarui {{ now()->format('d M Y, H:i') }}</p>
    </div>
</div>

{{-- KPI Cards --}}
<div class="kpi-grid">
    @php
    $materialColors = ['PET'=>'#16a34a','MLP'=>'#2563eb','Kardus'=>'#d97706','Metal'=>'#dc2626','HDPE'=>'#7c3aed','Campuran'=>'#0891b2'];
    $materialIcons  = ['PET'=>'ti-bottle','MLP'=>'ti-stack-2','Kardus'=>'ti-box','Metal'=>'ti-tools','HDPE'=>'ti-droplet','Campuran'=>'ti-recycle'];
    @endphp
    @foreach(['PET','MLP','Kardus','Metal','HDPE'] as $mat)
    @php $m = $materials->get($mat); @endphp
    <div class="kpi-card" style="border-top-color:{{ $materialColors[$mat] }}">
        <div class="kpi-label" style="color:{{ $materialColors[$mat] }}">
            <i class="ti {{ $materialIcons[$mat] }}" style="margin-right:3px"></i>{{ $mat }}
        </div>
        <div class="kpi-val">{{ $m ? number_format($m->total_kg,0,',','.') : '0' }}<span class="kpi-unit">kg</span></div>
        <div class="kpi-trend up"><i class="ti ti-trending-up"></i>Rp {{ $m ? number_format($m->total_value,0,',','.') : '0' }}</div>
    </div>
    @endforeach
</div>

{{-- Charts & Activity --}}
<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:16px;margin-bottom:16px">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ti ti-chart-bar"></i>Distribusi Material</div>
                <div class="card-subtitle">Total berat per jenis material (kg)</div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="materialChart" height="180"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-activity"></i>Aktivitas Terbaru</div>
        </div>
        <div style="padding:0">
            @forelse($recentCollections as $col)
            <div style="display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid var(--border)">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--green-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti ti-scale" style="font-size:15px;color:var(--green-dark)"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $col->wasteUnit?->name ?? '-' }}</div>
                    <div style="font-size:11.5px;color:var(--text3)">{{ $col->material_type }} · {{ number_format($col->net_weight_kg,0,',','.') }} kg → {{ $col->aggregator?->name }}</div>
                </div>
                <span class="badge badge-green" style="font-size:10px">{{ $col->collected_at->format('d/m') }}</span>
            </div>
            @empty
            <div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada data</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Entity counts + Top lists --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px">
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-buildings"></i>Entitas Aktif</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
            @foreach([['Waste Collection (BSU)','bsu','ti-recycle','#16a34a'],['Agregator','agregator','ti-truck-delivery','#d97706'],['Recycler','recycler','ti-building-factory-2','#7c3aed']] as [$label,$key,$icon,$color])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fafc;border-radius:8px">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="ti {{ $icon }}" style="font-size:17px;color:{{ $color }}"></i>
                    <span style="font-size:13px;font-weight:500;color:var(--text2)">{{ $label }}</span>
                </div>
                <span style="font-size:18px;font-weight:700;color:var(--text)">{{ $counts[$key] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-trophy"></i>Top BSU</div>
            <a href="{{ route('waste-collection.index') }}" class="btn btn-sm">Lihat semua</a>
        </div>
        <div style="padding:0">
            @foreach($topBsu as $i=>$bsu)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid var(--border)">
                <span style="font-size:11px;font-weight:700;color:var(--text3);width:16px">{{ $i+1 }}</span>
                <div style="flex:1;min-width:0">
                    <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $bsu->name }}</div>
                    <div style="font-size:11px;color:var(--text3)">{{ $bsu->regency }}</div>
                </div>
                <span style="font-size:12.5px;font-weight:700;color:var(--green)">{{ number_format($bsu->total_kg ?? 0,0,',','.') }} kg</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-truck"></i>Top Agregator</div>
            <a href="{{ route('agregator.index') }}" class="btn btn-sm">Lihat semua</a>
        </div>
        <div style="padding:0">
            @foreach($topAgg as $i=>$agg)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid var(--border)">
                <span style="font-size:11px;font-weight:700;color:var(--text3);width:16px">{{ $i+1 }}</span>
                <div style="flex:1;min-width:0">
                    <div style="font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $agg->name }}</div>
                    <div style="font-size:11px;color:var(--text3)">{{ $agg->regency }}</div>
                </div>
                <span style="font-size:12.5px;font-weight:700;color:var(--green)">{{ number_format($agg->total_kg ?? 0,0,',','.') }} kg</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const colors = {
    PET:'#16a34a', MLP:'#2563eb', Kardus:'#d97706', Metal:'#dc2626', HDPE:'#7c3aed'
};
const materials = @json($materials);
const labels = Object.keys(colors);
const data   = labels.map(l => materials[l]?.total_kg || 0);

new Chart(document.getElementById('materialChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            data,
            backgroundColor: labels.map(l => colors[l] + '22'),
            borderColor:     labels.map(l => colors[l]),
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { color: '#475569', font: { size: 12, weight: 600 } } }
        }
    }
});
</script>
@endpush
