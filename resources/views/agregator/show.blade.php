@extends('layouts.app')
@section('title', $aggregator->name)

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <a href="{{ route('agregator.index') }}">Agregator</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">{{ $aggregator->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
            <span class="badge badge-amber font-mono">{{ $aggregator->code }}</span>
            <span class="badge {{ $aggregator->is_active ? 'badge-green' : 'badge-red' }}">{{ $aggregator->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <h1>{{ $aggregator->name }}</h1>
        <p>{{ $aggregator->village }}, {{ $aggregator->district }}, {{ $aggregator->regency }} &mdash; {{ $aggregator->phone }}</p>
    </div>
    <a href="{{ route('agregator.index') }}" class="btn btn-sm"><i class="ti ti-arrow-left"></i>Kembali</a>
</div>

{{-- Stocks --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
    @foreach($aggregator->stocks as $s)
    <div class="card" style="padding:16px;border-top:3px solid var(--green);min-width:140px">
        <div style="font-size:11px;font-weight:600;color:var(--green);text-transform:uppercase">{{ $s->material_type }}</div>
        <div style="font-size:24px;font-weight:700;color:var(--text);margin-top:4px">{{ number_format($s->stock_kg,0,',','.') }}</div>
        <div style="font-size:11.5px;color:var(--text3)">kg stok tersedia</div>
    </div>
    @endforeach
</div>

{{-- Collections Table --}}
<div class="card" style="margin-bottom:16px">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-scale"></i>Riwayat Penerimaan dari BSU</div>
        <div style="font-size:12px;color:var(--text3)">{{ $collections->total() }} record</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Tanggal</th><th>BSU Pengirim</th><th>Material</th><th style="text-align:right">Berat Bersih</th><th style="text-align:right">Nilai</th></tr></thead>
            <tbody>
                @forelse($collections as $col)
                <tr>
                    <td class="font-mono" style="font-size:12px;color:var(--green)">{{ $col->transaction_code }}</td>
                    <td>{{ $col->collected_at->format('d M Y') }}</td>
                    <td><div style="font-weight:600">{{ $col->wasteUnit?->name }}</div><div style="font-size:11px;color:var(--text3)">{{ $col->wasteUnit?->regency }}</div></td>
                    <td><span class="badge badge-green" style="font-size:11px">{{ $col->material_type }}</span></td>
                    <td style="text-align:right;font-weight:700">{{ number_format($col->net_weight_kg,1,',','.') }} kg</td>
                    <td style="text-align:right;color:var(--text2)">Rp {{ number_format($col->total_value,0,',','.') }}</td>
                </tr>
                @empty<tr><td colspan="6"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada data</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($collections->hasPages())<div class="pagination">{{ $collections->links() }}</div>@endif
</div>

{{-- Shipments Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-truck-delivery"></i>Riwayat Pengiriman ke Recycler</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode Pengiriman</th><th>Tanggal</th><th>Recycler</th><th>Material</th><th style="text-align:right">Berat Kirim</th><th>Status</th></tr></thead>
            <tbody>
                @php $statusClass=['dispatched'=>'badge-blue','in_transit'=>'badge-amber','received'=>'badge-green','cancelled'=>'badge-red']; @endphp
                @forelse($shipments as $shp)
                <tr>
                    <td class="font-mono" style="font-size:12px;color:#7c3aed">{{ $shp->shipment_code }}</td>
                    <td>{{ $shp->dispatched_at->format('d M Y') }}</td>
                    <td><div style="font-weight:600">{{ $shp->recycler?->name }}</div><div style="font-size:11px;color:var(--text3)">{{ $shp->recycler?->code }}</div></td>
                    <td><span class="badge badge-purple" style="font-size:11px">{{ $shp->material_type }}</span></td>
                    <td style="text-align:right;font-weight:700">{{ number_format($shp->shipped_weight_kg,1,',','.') }} kg</td>
                    <td><span class="badge {{ $statusClass[$shp->status] ?? 'badge-gray' }}">{{ $shp->status_label }}</span></td>
                </tr>
                @empty<tr><td colspan="6"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada pengiriman</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($shipments->hasPages())<div class="pagination">{{ $shipments->links() }}</div>@endif
</div>
@endsection
