@extends('layouts.app')
@section('title', 'Pengiriman Agregator')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Pengiriman</span>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1fr 1.3fr;gap:20px;align-items:start">

    {{-- LEFT: Form --}}
    <div class="card" style="position:sticky;top:24px">
        <div class="card-header">
            <div>
                <div class="card-title"><i class="ti ti-truck-delivery"></i>Input Pengiriman ke Recycler</div>
                <div class="card-subtitle">Pilih agregator → lihat stok → kirim ke recycler</div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.pengiriman.store') }}" method="POST" x-data="pengirimanForm()">
                @csrf

                <div class="form-group">
                    <label class="form-label">Agregator Pengirim <span class="req">*</span></label>
                    <select name="aggregator_id" class="form-control @error('aggregator_id') is-invalid @enderror" required
                        x-model="aggId" @change="loadStock()">
                        <option value="">— Pilih Agregator —</option>
                        @foreach($aggregators as $agg)
                        <option value="{{ $agg->id }}" {{ old('aggregator_id')==$agg->id?'selected':'' }}>{{ $agg->code }} — {{ $agg->name }}</option>
                        @endforeach
                    </select>
                    @error('aggregator_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Stock display --}}
                <div x-show="stocks.length > 0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px;margin-bottom:16px">
                    <div style="font-size:11px;font-weight:700;color:var(--green-dark);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">Stok Tersedia</div>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <template x-for="s in stocks" :key="s.material_type">
                            <div style="background:#fff;border:1px solid #bbf7d0;border-radius:7px;padding:6px 12px;text-align:center;cursor:pointer" @click="selectMaterial(s)">
                                <div style="font-size:10px;font-weight:700;color:var(--green-dark)" x-text="s.material_type"></div>
                                <div style="font-size:14px;font-weight:700;color:var(--text)" x-text="Number(s.stock_kg).toLocaleString('id-ID')"></div>
                                <div style="font-size:10px;color:var(--text3)">kg</div>
                            </div>
                        </template>
                    </div>
                </div>
                <div x-show="aggId && stocks.length === 0 && !loading" class="alert alert-warning" style="margin-bottom:16px">
                    <i class="ti ti-alert-triangle"></i><span>Agregator ini tidak memiliki stok tersedia.</span>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Jenis Material <span class="req">*</span></label>
                        <select name="material_type" class="form-control @error('material_type') is-invalid @enderror" required x-model="selectedMat">
                            <option value="">— Pilih Material —</option>
                            @foreach(['PET','MLP','Kardus','Metal','HDPE','Campuran'] as $mat)
                            <option value="{{ $mat }}" {{ old('material_type')==$mat?'selected':'' }}>{{ $mat }}</option>
                            @endforeach
                        </select>
                        @error('material_type')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Recycler Tujuan <span class="req">*</span></label>
                        <select name="recycler_id" class="form-control @error('recycler_id') is-invalid @enderror" required>
                            <option value="">— Pilih Recycler —</option>
                            @foreach($recyclers as $rec)
                            <option value="{{ $rec->id }}" {{ old('recycler_id')==$rec->id?'selected':'' }}>{{ $rec->code }} — {{ $rec->name }}</option>
                            @endforeach
                        </select>
                        @error('recycler_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Berat Dikirim (kg) <span class="req">*</span></label>
                    <input type="number" name="shipped_weight_kg" class="form-control @error('shipped_weight_kg') is-invalid @enderror"
                        step="0.01" min="0.01" placeholder="0.00" required>
                    <div class="form-hint" x-show="selectedMat && availableStock > 0">
                        Stok tersedia: <strong x-text="availableStock.toLocaleString('id-ID') + ' kg'"></strong>
                    </div>
                    @error('shipped_weight_kg')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Info Kendaraan</label>
                    <input type="text" name="vehicle_info" class="form-control" placeholder="Contoh: B 1234 ABC" value="{{ old('vehicle_info') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-green" style="width:100%;height:42px;font-size:14px;justify-content:center">
                    <i class="ti ti-send"></i>Catat Pengiriman &amp; Kurangi Stok
                </button>
            </form>
        </div>
    </div>

    {{-- RIGHT: Shipment Table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-list-details"></i>Riwayat Pengiriman</div>
        </div>
        {{-- Filter --}}
        <div style="padding:12px 18px;border-bottom:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap">
            <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <select name="aggregator_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Agregator</option>
                    @foreach($aggregators as $agg)<option value="{{ $agg->id }}" {{ $aggregatorId==$agg->id?'selected':'' }}>{{ $agg->code }}</option>@endforeach
                </select>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach(['dispatched'=>'Dikirim','in_transit'=>'Dalam Perjalanan','received'=>'Diterima','cancelled'=>'Dibatalkan'] as $v=>$l)
                    <option value="{{ $v }}" {{ $status==$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
                @if($aggregatorId||$status)<a href="{{ route('superadmin.pengiriman.index') }}" class="btn btn-sm"><i class="ti ti-x"></i></a>@endif
            </form>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th><th>Tgl Kirim</th><th>Dari → Ke</th><th>Material</th>
                        <th style="text-align:right">Berat</th><th>Status</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sc=['dispatched'=>'badge-blue','in_transit'=>'badge-amber','received'=>'badge-green','cancelled'=>'badge-red']; @endphp
                    @forelse($shipments as $shp)
                    <tr>
                        <td class="font-mono" style="font-size:11.5px;color:#7c3aed">{{ $shp->shipment_code }}</td>
                        <td style="white-space:nowrap;font-size:12px">{{ $shp->dispatched_at->format('d M Y') }}</td>
                        <td>
                            <div style="font-size:12px;font-weight:600">{{ $shp->aggregator?->code }}</div>
                            <div style="font-size:11px;color:var(--text3);display:flex;align-items:center;gap:3px">
                                <i class="ti ti-arrow-right" style="font-size:10px"></i>{{ $shp->recycler?->name }}
                            </div>
                        </td>
                        <td><span class="badge badge-purple" style="font-size:10.5px">{{ $shp->material_type }}</span></td>
                        <td style="text-align:right;font-weight:700;font-size:12.5px">{{ number_format($shp->shipped_weight_kg,1,',','.') }} kg</td>
                        <td><span class="badge {{ $sc[$shp->status]??'badge-gray' }}" style="font-size:10.5px">{{ $shp->status_label }}</span></td>
                        <td>
                            @if(!in_array($shp->status,['received','cancelled']))
                            <div x-data="{open:false}" style="position:relative">
                                <button class="btn btn-sm" @click="open=!open"><i class="ti ti-dots-vertical"></i></button>
                                <div x-show="open" @click.away="open=false"
                                    style="position:absolute;right:0;top:32px;z-index:50;background:#fff;border:1px solid var(--border);border-radius:9px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:180px;overflow:hidden">
                                    @foreach(['in_transit'=>['Tandai Dalam Perjalanan','badge-amber'],'received'=>['Tandai Diterima','badge-green'],'cancelled'=>['Batalkan','badge-red']] as $sv=>[$sl,$sc2])
                                    @if($shp->status !== $sv)
                                    <form action="{{ route('superadmin.pengiriman.updateStatus', $shp->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $sv }}">
                                        @if($sv==='received')<input type="number" name="received_weight_kg" placeholder="Berat diterima (kg)" class="form-control" style="margin:6px 10px;width:calc(100% - 20px);height:30px;font-size:12px">@endif
                                        <button type="submit" class="btn" style="width:100%;border-radius:0;border:none;border-bottom:1px solid var(--border);justify-content:flex-start;padding-left:14px">
                                            <span class="badge {{ $sc2 }}" style="font-size:10px">{{ $sl }}</span>
                                        </button>
                                    </form>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <span style="font-size:11px;color:var(--text3)">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada pengiriman</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shipments->hasPages())<div class="pagination">{{ $shipments->links() }}</div>@endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function pengirimanForm() {
    return {
        aggId: '{{ old("aggregator_id","") }}',
        stocks: [],
        loading: false,
        selectedMat: '{{ old("material_type","") }}',
        availableStock: 0,

        async loadStock() {
            if (!this.aggId) { this.stocks = []; return; }
            this.loading = true;
            const r = await fetch(`/superadmin/pengiriman/stock/${this.aggId}`);
            this.stocks = await r.json();
            this.loading = false;
        },
        selectMaterial(s) {
            this.selectedMat = s.material_type;
            this.availableStock = s.stock_kg;
        },
        init() { if (this.aggId) this.loadStock(); }
    };
}
</script>
@endpush
