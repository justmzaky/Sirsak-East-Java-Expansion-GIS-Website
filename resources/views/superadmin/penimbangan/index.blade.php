@extends('layouts.app')
@section('title', 'Input Penimbangan')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><span>Manajemen</span>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">Penimbangan</span>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:1.1fr 1fr;gap:20px;align-items:start">

    {{-- LEFT: Form --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title"><i class="ti ti-scale"></i>Input Penimbangan Sampah</div>
                    <div class="card-subtitle">Data otomatis masuk ke inventori agregator tujuan</div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info" style="margin-bottom:20px">
                    <i class="ti ti-info-circle"></i>
                    <span>Berat bersih = berat kotor − berat tara. Stok agregator tujuan akan bertambah secara otomatis setelah data disimpan.</span>
                </div>

                <form action="{{ route('superadmin.penimbangan.store') }}" method="POST" x-data="penimbanganForm()">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Kode Transaksi</label>
                            <input type="text" class="form-control" value="{{ $nextCode }}" readonly style="color:var(--text3);background:#f8fafc">
                            <div class="form-hint">Generate otomatis</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal <span class="req">*</span></label>
                            <input type="date" name="collected_at" class="form-control @error('collected_at') is-invalid @enderror" value="{{ old('collected_at', date('Y-m-d')) }}" required>
                            @error('collected_at')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">BSU (Waste Collection) <span class="req">*</span></label>
                            <select name="waste_unit_id" class="form-control @error('waste_unit_id') is-invalid @enderror" required
                                x-model="bsuId" @change="setDefaultAgg()">
                                <option value="">— Pilih BSU —</option>
                                @foreach($bsuList as $bsu)
                                <option value="{{ $bsu->id }}" data-agg="{{ $bsu->aggregator_id }}" {{ old('waste_unit_id')==$bsu->id?'selected':'' }}>
                                    {{ $bsu->code }} — {{ $bsu->name }} ({{ $bsu->regency }})
                                </option>
                                @endforeach
                            </select>
                            @error('waste_unit_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Agregator Tujuan <span class="req">*</span></label>
                            <select name="aggregator_id" class="form-control @error('aggregator_id') is-invalid @enderror" required x-model="aggId">
                                <option value="">— Pilih Agregator —</option>
                                @foreach($aggregators as $agg)
                                <option value="{{ $agg->id }}" {{ old('aggregator_id')==$agg->id?'selected':'' }}>
                                    {{ $agg->code }} — {{ $agg->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-hint">Default sesuai BSU yang dipilih</div>
                            @error('aggregator_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Jenis Material <span class="req">*</span></label>
                            <select name="material_type" class="form-control @error('material_type') is-invalid @enderror" required>
                                <option value="">— Pilih Material —</option>
                                @foreach(['PET','MLP','Kardus','Metal','HDPE','Campuran'] as $mat)
                                <option value="{{ $mat }}" {{ old('material_type')==$mat?'selected':'' }}>{{ $mat }}</option>
                                @endforeach
                            </select>
                            @error('material_type')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kondisi Material <span class="req">*</span></label>
                            <select name="material_condition" class="form-control" required>
                                @foreach(['Bersih & Kering','Kotor / Campuran','Basah'] as $c)
                                <option value="{{ $c }}" {{ old('material_condition')==$c?'selected':'' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Berat Kotor (kg) <span class="req">*</span></label>
                            <input type="number" name="gross_weight_kg" class="form-control @error('gross_weight_kg') is-invalid @enderror"
                                step="0.01" min="0.01" placeholder="0.00" required
                                x-model="gross" @input="calcNet()">
                            @error('gross_weight_kg')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Berat Tara / Kemasan (kg)</label>
                            <input type="number" name="tare_weight_kg" class="form-control"
                                step="0.01" min="0" placeholder="0.00" value="{{ old('tare_weight_kg',0) }}"
                                x-model="tare" @input="calcNet()">
                            <div class="form-hint" x-show="net > 0">Berat bersih: <strong x-text="net.toFixed(2) + ' kg'"></strong></div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Harga / kg (Rp)</label>
                            <input type="number" name="price_per_kg" class="form-control"
                                step="1" min="0" placeholder="0"
                                x-model="price" @input="calcTotal()">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estimasi Nilai (Rp)</label>
                            <input type="text" class="form-control" readonly
                                :value="'Rp ' + (net * price).toLocaleString('id-ID')"
                                style="color:var(--green);font-weight:600;background:#f0fdf4">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Opsional...">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Confirmation preview --}}
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-bottom:16px" x-show="net > 0">
                        <div style="font-size:11px;font-weight:700;color:var(--green-dark);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">Ringkasan</div>
                        <div style="display:flex;gap:16px;flex-wrap:wrap">
                            <div><div style="font-size:11px;color:var(--text3)">Berat Bersih</div><div style="font-size:16px;font-weight:700;color:var(--text)" x-text="net.toFixed(2) + ' kg'"></div></div>
                            <div><div style="font-size:11px;color:var(--text3)">Estimasi Nilai</div><div style="font-size:16px;font-weight:700;color:var(--green)" x-text="'Rp ' + (net * price).toLocaleString('id-ID')"></div></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-green" style="width:100%;height:42px;font-size:14px;justify-content:center">
                        <i class="ti ti-device-floppy"></i>Simpan &amp; Kirim ke Inventori Agregator
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: History --}}
    <div class="card" style="position:sticky;top:24px">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-history"></i>Riwayat Terbaru</div>
            <div style="font-size:12px;color:var(--text3)">{{ $collections->total() }} total</div>
        </div>
        <div style="max-height:560px;overflow-y:auto">
            @forelse($collections as $col)
            <div style="padding:12px 18px;border-bottom:1px solid var(--border)">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
                    <span class="font-mono" style="font-size:11px;font-weight:700;color:var(--green)">{{ $col->transaction_code }}</span>
                    <span style="font-size:11px;color:var(--text3);white-space:nowrap">{{ $col->collected_at->format('d/m/Y') }}</span>
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--text)">{{ $col->wasteUnit?->name }}</div>
                <div style="font-size:11.5px;color:var(--text2);margin-top:2px">{{ $col->material_type }} · {{ $col->material_condition }}</div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px">
                    <span style="font-size:11.5px;color:var(--text3);display:flex;align-items:center;gap:3px">
                        <i class="ti ti-arrow-right" style="font-size:11px"></i>{{ $col->aggregator?->name }}
                    </span>
                    <span style="font-size:13px;font-weight:700;color:var(--green)">{{ number_format($col->net_weight_kg,1,',','.') }} kg</span>
                </div>
                @role('superadmin')
                <div style="margin-top:6px;text-align:right">
                    <form action="{{ route('superadmin.penimbangan.destroy', $col->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="color:#dc2626;border-color:#fecaca;font-size:11px">
                            <i class="ti ti-trash" style="font-size:12px"></i>Hapus
                        </button>
                    </form>
                </div>
                @endrole
            </div>
            @empty
            <div class="empty-state"><i class="ti ti-inbox"></i><p>Belum ada data</p></div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const bsuAggMap = {
    @foreach($bsuList as $bsu)
    '{{ $bsu->id }}': '{{ $bsu->aggregator_id }}',
    @endforeach
};

function penimbanganForm() {
    return {
        bsuId: '{{ old("waste_unit_id","") }}',
        aggId: '{{ old("aggregator_id","") }}',
        gross: {{ old("gross_weight_kg",0) }},
        tare:  {{ old("tare_weight_kg",0) }},
        price: {{ old("price_per_kg",0) }},
        net:   0,
        init() { this.calcNet(); },
        setDefaultAgg() {
            const aggId = bsuAggMap[this.bsuId];
            if (aggId) this.aggId = aggId;
        },
        calcNet()   { this.net = Math.max(0, this.gross - this.tare); },
        calcTotal() {},
    };
}
</script>
@endpush
