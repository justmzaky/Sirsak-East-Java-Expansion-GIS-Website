@extends('layouts.app')
@section('title', $recycler->name)

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><a href="{{ route('recycler.index') }}">Recycler</a>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">{{ $recycler->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
            <span class="badge badge-purple font-mono">{{ $recycler->code }}</span>
            <span class="badge {{ $recycler->is_active ? 'badge-green' : 'badge-red' }}">{{ $recycler->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
        <h1>{{ $recycler->name }}</h1>
        <p>{{ $recycler->address }}, {{ $recycler->regency }} &mdash; PIC: {{ $recycler->pic_name }} &mdash; {{ $recycler->phone }}</p>
    </div>
    <a href="{{ route('recycler.index') }}" class="btn btn-sm"><i class="ti ti-arrow-left"></i>Kembali</a>
</div>

<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px">
    @foreach($materialBreakdown as $mb)
    <div class="card" style="padding:16px;border-top:3px solid #7c3aed;min-width:140px">
        <div style="font-size:11px;font-weight:600;color:#7c3aed;text-transform:uppercase">{{ $mb->material_type }}</div>
        <div style="font-size:24px;font-weight:700;color:var(--text);margin-top:4px">{{ number_format($mb->total_kg ?? 0,0,',','.') }}</div>
        <div style="font-size:11.5px;color:var(--text3)">kg diterima</div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-truck-delivery"></i>Riwayat Pengiriman Masuk</div>
        <div style="font-size:12px;color:var(--text3)">{{ $shipments->total() }} record &mdash; View Only</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode Pengiriman</th><th>Tanggal</th><th>Dari Agregator</th><th>Material</th><th style="text-align:right">Berat Kirim</th><th style="text-align:right">Berat Diterima</th><th>Status</th></tr></thead>
            <tbody>
                @php $sc=['dispatched'=>'badge-blue','in_transit'=>'badge-amber','received'=>'badge-green','cancelled'=>'badge-red']; @endphp
                @forelse($shipments as $shp)
                <tr>
                    <td class="font-mono" style="font-size:12px;color:#7c3aed">{{ $shp->shipment_code }}</td>
                    <td>{{ $shp->dispatched_at->format('d M Y') }}</td>
                    <td><div style="font-weight:600">{{ $shp->aggregator?->name }}</div><div style="font-size:11px;color:var(--text3)">{{ $shp->aggregator?->code }}</div></td>
                    <td><span class="badge badge-purple" style="font-size:11px">{{ $shp->material_type }}</span></td>
                    <td style="text-align:right;font-weight:600">{{ number_format($shp->shipped_weight_kg,1,',','.') }} kg</td>
                    <td style="text-align:right;color:var(--green);font-weight:600">{{ $shp->received_weight_kg ? number_format($shp->received_weight_kg,1,',','.').' kg' : '—' }}</td>
                    <td><span class="badge {{ $sc[$shp->status]??'badge-gray' }}">{{ $shp->status_label }}</span></td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada pengiriman</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($shipments->hasPages())<div class="pagination">{{ $shipments->links() }}</div>@endif
</div>
@endsection
