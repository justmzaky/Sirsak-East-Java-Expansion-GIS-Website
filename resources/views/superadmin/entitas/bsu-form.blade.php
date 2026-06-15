@extends('layouts.app')
@section('title', isset($wasteUnit) ? 'Edit BSU' : 'Tambah BSU')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><a href="{{ route('superadmin.entitas.bsu.index') }}">BSU</a>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">{{ isset($wasteUnit) ? 'Edit '.$wasteUnit->code : 'Tambah Baru' }}</span>
@endsection

@section('content')
<div style="max-width:700px">
    <div class="page-header">
        <div class="page-header-left">
            <h1>{{ isset($wasteUnit) ? 'Edit BSU: '.$wasteUnit->name : 'Tambah BSU Baru' }}</h1>
        </div>
        <a href="{{ route('superadmin.entitas.bsu.index') }}" class="btn btn-sm"><i class="ti ti-arrow-left"></i>Kembali</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($wasteUnit) ? route('superadmin.entitas.bsu.update',$wasteUnit->id) : route('superadmin.entitas.bsu.store') }}" method="POST">
                @csrf
                @if(isset($wasteUnit)) @method('PUT') @endif

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Kode BSU <span class="req">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code',$wasteUnit->code??'') }}" placeholder="BSU01" required maxlength="10" style="text-transform:uppercase">
                        <div class="form-hint">Format: BSU01, BSU02, dst.</div>
                        @error('code')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Agregator Default</label>
                        <select name="aggregator_id" class="form-control">
                            <option value="">— Tidak ada —</option>
                            @foreach($aggregators as $agg)
                            <option value="{{ $agg->id }}" {{ old('aggregator_id',$wasteUnit->aggregator_id??'')==$agg->id?'selected':'' }}>{{ $agg->code }} — {{ $agg->name }}</option>
                            @endforeach
                        </select>
                        @error('aggregator_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama BSU <span class="req">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$wasteUnit->name??'') }}" placeholder="Contoh: BSU Wonokromo" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Desa / Kelurahan</label>
                        <input type="text" name="village" class="form-control" value="{{ old('village',$wasteUnit->village??'') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="district" class="form-control" value="{{ old('district',$wasteUnit->district??'') }}">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Kabupaten / Kota <span class="req">*</span></label>
                        <input type="text" name="regency" class="form-control" value="{{ old('regency',$wasteUnit->regency??'') }}" required>
                        @error('regency')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone',$wasteUnit->phone??'') }}" placeholder="08xxxxxxxxxx">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="number" name="latitude" class="form-control" value="{{ old('latitude',$wasteUnit->latitude??'') }}" step="0.00000001" placeholder="-7.2830">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="number" name="longitude" class="form-control" value="{{ old('longitude',$wasteUnit->longitude??'') }}" step="0.00000001" placeholder="112.7460">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Tanggal Bergabung</label>
                        <input type="date" name="joined_at" class="form-control" value="{{ old('joined_at',isset($wasteUnit->joined_at)?$wasteUnit->joined_at->format('Y-m-d'):'') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" {{ old('is_active',isset($wasteUnit)?$wasteUnit->is_active:1)==1?'selected':'' }}>Aktif</option>
                            <option value="0" {{ old('is_active',isset($wasteUnit)?$wasteUnit->is_active:1)==0?'selected':'' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-green"><i class="ti ti-device-floppy"></i>{{ isset($wasteUnit) ? 'Update BSU' : 'Simpan BSU' }}</button>
                    <a href="{{ route('superadmin.entitas.bsu.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
