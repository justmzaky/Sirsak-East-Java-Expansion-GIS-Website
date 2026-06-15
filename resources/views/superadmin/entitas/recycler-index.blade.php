@extends('layouts.app')
@section('title', 'Kelola Recycler')
@section('topbar-actions')
    <a href="{{ route('superadmin.entitas.aggregator.index') }}" class="btn btn-sm">Agregator</a>
    <a href="{{ route('superadmin.entitas.bsu.index') }}" class="btn btn-sm">BSU</a>
    <a href="{{ route('superadmin.entitas.recycler.index') }}" class="btn btn-sm btn-green">Recycler</a>
    <a href="{{ route('superadmin.entitas.recycler.create') }}" class="btn btn-green btn-sm"><i class="ti ti-plus"></i>Tambah Recycler</a>
@endsection
@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span><span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Kelola Recycler</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-left"><h1>Kelola Recycler</h1><p>Daftar fasilitas daur ulang terdaftar</p></div></div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>PIC</th><th>Lokasi</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($recyclers as $rec)
                <tr>
                    <td class="font-mono" style="font-weight:700;color:#7c3aed;font-size:12px">{{ $rec->code }}</td>
                    <td><div style="font-weight:600">{{ $rec->name }}</div><div style="font-size:11.5px;color:var(--text3)">{{ $rec->phone }}</div></td>
                    <td><span class="badge badge-purple">{{ $rec->company_type ?? 'N/A' }}</span></td>
                    <td style="font-size:12.5px">{{ $rec->pic_name ?? '—' }}</td>
                    <td style="font-size:12.5px">{{ $rec->regency }}</td>
                    <td><span class="badge {{ $rec->is_active ? 'badge-green' : 'badge-red' }}">{{ $rec->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td>
                        <div style="display:flex;gap:4px">
                            <a href="{{ route('superadmin.entitas.recycler.edit', $rec->id) }}" class="btn btn-sm btn-icon"><i class="ti ti-edit"></i></a>
                            <form action="{{ route('superadmin.entitas.recycler.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Hapus Recycler {{ $rec->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon" style="color:#dc2626;border-color:#fecaca"><i class="ti ti-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada recycler</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @if($recyclers->hasPages())<div class="pagination">{{ $recyclers->links() }}</div>@endif
</div>
@endsection
