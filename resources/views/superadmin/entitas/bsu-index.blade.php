@extends('layouts.app')
@section('title', 'Kelola BSU')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Kelola Entitas</span>
@endsection

@section('topbar-actions')
    <a href="{{ route('superadmin.entitas.aggregator.index') }}" class="btn btn-sm {{ request()->routeIs('superadmin.entitas.aggregator*')?'btn-green':'' }}">Agregator</a>
    <a href="{{ route('superadmin.entitas.bsu.index') }}" class="btn btn-sm {{ request()->routeIs('superadmin.entitas.bsu*')?'btn-green':'' }}">BSU</a>
    <a href="{{ route('superadmin.entitas.recycler.index') }}" class="btn btn-sm {{ request()->routeIs('superadmin.entitas.recycler*')?'btn-green':'' }}">Recycler</a>
    <a href="{{ route('superadmin.entitas.bsu.create') }}" class="btn btn-green btn-sm"><i class="ti ti-plus"></i>Tambah BSU</a>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left"><h1>Kelola Waste Collection (BSU)</h1><p>Daftar seluruh Bank Sampah Unit terdaftar</p></div>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama BSU</th><th>Lokasi</th><th>Agregator Default</th><th>Status</th><th>Bergabung</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($wasteUnits as $bsu)
                <tr>
                    <td class="font-mono" style="font-weight:700;color:var(--green);font-size:12px">{{ $bsu->code }}</td>
                    <td><div style="font-weight:600">{{ $bsu->name }}</div><div style="font-size:11.5px;color:var(--text3)">{{ $bsu->phone }}</div></td>
                    <td style="font-size:12.5px">{{ $bsu->village }}, {{ $bsu->district }}<br><span style="color:var(--text3)">{{ $bsu->regency }}</span></td>
                    <td>
                        @if($bsu->aggregator)
                        <span class="badge badge-amber font-mono" style="font-size:10.5px">{{ $bsu->aggregator->code }}</span>
                        <span style="font-size:12px;margin-left:4px">{{ $bsu->aggregator->name }}</span>
                        @else<span style="color:var(--text3);font-size:12px">Belum ditautkan</span>@endif
                    </td>
                    <td><span class="badge {{ $bsu->is_active ? 'badge-green' : 'badge-red' }}">{{ $bsu->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="font-size:12px;color:var(--text3)">{{ $bsu->joined_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('superadmin.entitas.bsu.edit', $bsu->id) }}" class="btn btn-sm btn-icon"><i class="ti ti-edit"></i></a>
                            <form action="{{ route('superadmin.entitas.bsu.destroy', $bsu->id) }}" method="POST" onsubmit="return confirm('Hapus BSU {{ $bsu->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon" style="color:#dc2626;border-color:#fecaca"><i class="ti ti-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada BSU terdaftar</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($wasteUnits->hasPages())<div class="pagination">{{ $wasteUnits->links() }}</div>@endif
</div>
@endsection
