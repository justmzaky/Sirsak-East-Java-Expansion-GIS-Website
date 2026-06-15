@extends('layouts.app')
@section('title', 'Laporan')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Laporan</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Laporan & Ekspor Data</h1>
        <p>Unduh laporan sistem dalam format Excel atau PDF</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
    @foreach([
        ['ti-scale','Laporan Penimbangan','Export seluruh data penimbangan BSU per periode','badge-green','Tersedia', route('superadmin.laporan.export.penimbangan')],
        ['ti-truck-delivery','Laporan Pengiriman','Export data pengiriman dari agregator ke recycler','badge-blue','Tersedia', route('superadmin.laporan.export.pengiriman')],
        ['ti-chart-bar','Laporan Material Flow','Rekapitulasi alur material per jenis dan periode','badge-amber','Tersedia', route('superadmin.laporan.export.material-flow')],
        ['ti-building-factory-2','Laporan Recycler','Data penerimaan tiap recycler per periode','badge-purple','Tersedia', route('superadmin.laporan.export.recycler')],
        ['ti-users','Laporan Entitas','Daftar BSU, Agregator, dan Recycler aktif','badge-gray','Tersedia', route('superadmin.laporan.export.entitas')],
        ['ti-activity','Laporan Aktivitas','Log semua aktivitas admin dalam sistem','badge-red','Tersedia', route('superadmin.laporan.export.aktivitas')],
    ] as [$icon,$title,$desc,$badge,$status,$url])
    <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
            <div style="width:44px;height:44px;background:var(--green-light);border-radius:10px;display:flex;align-items:center;justify-content:center">
                <i class="ti {{ $icon }}" style="font-size:22px;color:var(--green-dark)"></i>
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px">{{ $title }}</div>
                <div style="font-size:12.5px;color:var(--text3);line-height:1.5">{{ $desc }}</div>
            </div>
            <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between">
                <span class="badge {{ $badge }}" style="font-size:10.5px">{{ $status }}</span>
                <a href="{{ $url }}" class="btn btn-sm" style="text-decoration:none">
                    <i class="ti ti-download"></i>Export Excel
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card" style="margin-top:16px">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-history"></i>Log Aktivitas Terbaru</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th><th>IP Address</th></tr></thead>
            <tbody>
                @forelse($logs ?? [] as $log)
                <tr>
                    <td style="white-space:nowrap;font-size:12px">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td style="font-size:12.5px;font-weight:500">{{ $log->user?->name ?? 'System' }}</td>
                    <td>
                        @php $ac=['created'=>'badge-green','updated'=>'badge-amber','deleted'=>'badge-red','login'=>'badge-blue','logout'=>'badge-gray']; @endphp
                        <span class="badge {{ $ac[$log->action] ?? 'badge-gray' }}" style="font-size:10.5px">{{ $log->action }}</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--text2)">{{ $log->description }}</td>
                    <td style="font-size:11.5px;color:var(--text3);font-family:monospace">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada log aktivitas</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
