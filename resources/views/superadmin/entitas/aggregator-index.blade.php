@extends('layouts.app')
@section('title', 'Kelola Agregator')
@section('topbar-actions')
    <a href="{{ route('superadmin.entitas.aggregator.index') }}" class="btn btn-sm btn-green">Agregator</a>
    <a href="{{ route('superadmin.entitas.bsu.index') }}" class="btn btn-sm">BSU</a>
    <a href="{{ route('superadmin.entitas.recycler.index') }}" class="btn btn-sm">Recycler</a>
    <a href="{{ route('superadmin.entitas.aggregator.create') }}" class="btn btn-green btn-sm"><i class="ti ti-plus"></i>Tambah Agregator</a>
@endsection
@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span><span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Kelola Agregator</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-left"><h1>Kelola Agregator</h1><p>Daftar seluruh pengepul sampah terdaftar</p></div></div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama</th><th>PIC</th><th>Lokasi</th><th>BSU</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($aggregators as $agg)
                <tr>
                    <td class="font-mono" style="font-weight:700;color:#d97706;font-size:12px">{{ $agg->code }}</td>
                    <td><div style="font-weight:600">{{ $agg->name }}</div><div style="font-size:11.5px;color:var(--text3)">{{ $agg->phone }}</div></td>
                    <td style="font-size:12.5px">{{ $agg->pic_name ?? '—' }}</td>
                    <td style="font-size:12.5px">{{ $agg->village }}, {{ $agg->district }}<br><span style="color:var(--text3)">{{ $agg->regency }}</span></td>
                    <td style="text-align:center;font-weight:700">{{ $agg->waste_units_count }}</td>
                    <td><span class="badge {{ $agg->is_active ? 'badge-green' : 'badge-red' }}">{{ $agg->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('superadmin.entitas.aggregator.edit', $agg->id) }}" class="btn btn-sm btn-icon"><i class="ti ti-edit"></i></a>
                            <form action="{{ route('superadmin.entitas.aggregator.destroy', $agg->id) }}" method="POST" onsubmit="return confirm('Hapus Agregator {{ $agg->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon" style="color:#dc2626;border-color:#fecaca"><i class="ti ti-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada agregator</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($aggregators->hasPages())<div class="pagination">{{ $aggregators->links() }}</div>@endif
</div>
@endsection
