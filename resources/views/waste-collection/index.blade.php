@extends('layouts.app')
@section('title', 'Waste Collection')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Waste Collection</span>
@endsection

@section('topbar-actions')
    @role('superadmin')
    <a href="{{ route('superadmin.penimbangan.index') }}" class="btn btn-green btn-sm"><i class="ti ti-plus"></i>Input Penimbangan</a>
    @endrole
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Waste Collection (BSU)</h1>
        <p>Data seluruh penimbangan dari Bank Sampah Unit</p>
    </div>
</div>

{{-- Stat Chips --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
    <div class="card" style="padding:16px">
        <div style="font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Total Transaksi</div>
        <div style="font-size:26px;font-weight:700;color:var(--text)">{{ number_format($totalTrx,0,',','.') }}</div>
        <div style="font-size:11.5px;color:var(--text3);margin-top:3px">penimbangan tercatat</div>
    </div>
    <div class="card" style="padding:16px;border-top:3px solid var(--green)">
        <div style="font-size:11px;font-weight:600;color:var(--green);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Total Berat Bersih</div>
        <div style="font-size:26px;font-weight:700;color:var(--text)">{{ number_format($totalKg,0,',','.') }}</div>
        <div style="font-size:11.5px;color:var(--text3);margin-top:3px">kg terkumpul</div>
    </div>
    <div class="card" style="padding:16px">
        <div style="font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Total Nilai</div>
        <div style="font-size:22px;font-weight:700;color:var(--text)">Rp {{ number_format($totalValue,0,',','.') }}</div>
        <div style="font-size:11.5px;color:var(--text3);margin-top:3px">estimasi nilai sampah</div>
    </div>
    <div class="card" style="padding:16px">
        <div style="font-size:11px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px">Material Terbanyak</div>
        @php $top = $statsByMaterial->sortByDesc('total_kg')->first(); @endphp
        <div style="font-size:22px;font-weight:700;color:var(--green)">{{ $top?->material_type ?? '-' }}</div>
        <div style="font-size:11.5px;color:var(--text3);margin-top:3px">{{ $top ? number_format($top->total_kg,0,',','.') . ' kg' : '–' }}</div>
    </div>
</div>

{{-- Filter --}}
<div class="filter-bar">
    <form method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;width:100%">
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
        <select name="bsu_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua BSU</option>
            @foreach($bsuList as $bsu)<option value="{{ $bsu->id }}" {{ $bsuId == $bsu->id ? 'selected' : '' }}>{{ $bsu->code }} – {{ $bsu->name }}</option>@endforeach
        </select>
        @if($year || $month || $bsuId)
            <a href="{{ route('waste-collection.index') }}" class="btn btn-sm"><i class="ti ti-x"></i>Reset</a>
        @endif
    </form>
</div>

{{-- Material breakdown --}}
<div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;justify-content:center;">
    @foreach($statsByMaterial->sortByDesc('total_kg') as $stat)
    @php $colors = ['PET'=>'#16a34a','MLP'=>'#2563eb','Kardus'=>'#d97706','Metal'=>'#dc2626','HDPE'=>'#7c3aed','Campuran'=>'#0891b2']; @endphp
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;min-width:150px;text-align:center;border-top:4px solid {{ $colors[$stat->material_type] ?? '#94a3b8' }};box-shadow:0 4px 6px -1px rgba(0,0,0,0.05),0 2px 4px -2px rgba(0,0,0,0.05);transition:transform 0.2s" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div style="font-size:12px;font-weight:700;color:{{ $colors[$stat->material_type] ?? '#94a3b8' }};text-transform:uppercase;letter-spacing:0.05em">{{ $stat->material_type }}</div>
        <div style="font-size:22px;font-weight:700;color:var(--text);margin-top:6px;line-height:1">{{ number_format($stat->total_kg,0,',','.') }} <span style="font-size:13px;color:var(--text3);font-weight:500">kg</span></div>
        <div style="font-size:11.5px;color:var(--text3);margin-top:6px">{{ number_format($stat->total_trx,0,',','.') }} transaksi</div>
    </div>
    @endforeach
</div>

{{-- Data Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-table"></i>Riwayat Penimbangan</div>
        <div style="font-size:12px;color:var(--text3)">{{ $collections->total() }} data ditemukan</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Tanggal</th>
                    <th>BSU</th>
                    <th>Agregator Tujuan</th>
                    <th>Material</th>
                    <th>Kondisi</th>
                    <th style="text-align:right">Berat Bersih</th>
                    <th style="text-align:right">Nilai</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $col)
                <tr>
                    <td><span class="font-mono" style="font-size:12px;color:var(--green);font-weight:600">{{ $col->transaction_code }}</span></td>
                    <td style="white-space:nowrap">{{ $col->collected_at->format('d M Y') }}</td>
                    <td>
                        <div style="font-weight:600;color:var(--text)">{{ $col->wasteUnit?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--text3)">{{ $col->wasteUnit?->code }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500">{{ $col->aggregator?->name ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--text3)">{{ $col->aggregator?->code }}</div>
                    </td>
                    <td>
                        @php $colors = ['PET'=>'badge-green','MLP'=>'badge-blue','Kardus'=>'badge-amber','Metal'=>'badge-red','HDPE'=>'badge-purple','Campuran'=>'badge-gray']; @endphp
                        <span class="badge {{ $colors[$col->material_type] ?? 'badge-gray' }}">{{ $col->material_type }}</span>
                    </td>
                    <td><span style="font-size:12px;color:var(--text2)">{{ $col->material_condition }}</span></td>
                    <td style="text-align:right;font-weight:700;color:var(--text)">{{ number_format($col->net_weight_kg,1,',','.') }} kg</td>
                    <td style="text-align:right;font-size:12px;color:var(--text2)">Rp {{ number_format($col->total_value,0,',','.') }}</td>
                    <td style="font-size:12px;color:var(--text3)">{{ $col->recorder?->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="empty-state"><i class="ti ti-inbox" style="font-size:28px;color:var(--text3);display:block;margin-bottom:8px"></i>Belum ada data penimbangan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($collections->hasPages())
    <div class="pagination">
        {{ $collections->links('vendor.pagination.simple') }}
    </div>
    @endif
</div>
@endsection
