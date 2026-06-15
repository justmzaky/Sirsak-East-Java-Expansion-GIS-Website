@extends('layouts.app')
@section('title', isset($aggregator) ? 'Edit Agregator' : 'Tambah Agregator')
@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><a href="{{ route('superadmin.entitas.aggregator.index') }}">Agregator</a>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">{{ isset($aggregator) ? 'Edit '.$aggregator->code : 'Tambah Baru' }}</span>
@endsection
@section('content')
<div style="max-width:700px">
    <div class="page-header">
        <div class="page-header-left"><h1>{{ isset($aggregator) ? 'Edit Agregator: '.$aggregator->name : 'Tambah Agregator Baru' }}</h1></div>
        <a href="{{ route('superadmin.entitas.aggregator.index') }}" class="btn btn-sm"><i class="ti ti-arrow-left"></i>Kembali</a>
    </div>
    <div class="card"><div class="card-body">
        <form action="{{ isset($aggregator) ? route('superadmin.entitas.aggregator.update',$aggregator->id) : route('superadmin.entitas.aggregator.store') }}" method="POST">
            @csrf @if(isset($aggregator)) @method('PUT') @endif
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Kode Agregator <span class="req">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code',$aggregator->code??'') }}" placeholder="AGG01" required maxlength="10">
                    @error('code')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama PIC</label>
                    <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name',$aggregator->pic_name??'') }}" placeholder="Nama penanggung jawab">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Agregator <span class="req">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name',$aggregator->name??'') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Desa</label><input type="text" name="village" class="form-control" value="{{ old('village',$aggregator->village??'') }}"></div>
                <div class="form-group"><label class="form-label">Kecamatan</label><input type="text" name="district" class="form-control" value="{{ old('district',$aggregator->district??'') }}"></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Kabupaten/Kota <span class="req">*</span></label><input type="text" name="regency" class="form-control" value="{{ old('regency',$aggregator->regency??'') }}" required></div>
                <div class="form-group"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone',$aggregator->phone??'') }}"></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Latitude</label><input type="number" name="latitude" class="form-control" value="{{ old('latitude',$aggregator->latitude??'') }}" step="0.00000001"></div>
                <div class="form-group"><label class="form-label">Longitude</label><input type="number" name="longitude" class="form-control" value="{{ old('longitude',$aggregator->longitude??'') }}" step="0.00000001"></div>
            </div>
            <div class="form-group"><label class="form-label">Status</label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ old('is_active',isset($aggregator)?$aggregator->is_active:1)==1?'selected':'' }}>Aktif</option>
                    <option value="0" {{ old('is_active',isset($aggregator)?$aggregator->is_active:1)==0?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-green"><i class="ti ti-device-floppy"></i>{{ isset($aggregator) ? 'Update' : 'Simpan' }}</button>
                <a href="{{ route('superadmin.entitas.aggregator.index') }}" class="btn">Batal</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
