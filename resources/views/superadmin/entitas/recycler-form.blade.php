@extends('layouts.app')
@section('title', isset($recycler) ? 'Edit Recycler' : 'Tambah Recycler')
@section('breadcrumb')
    <span class="breadcrumb-sep">/</span><a href="{{ route('superadmin.entitas.recycler.index') }}">Recycler</a>
    <span class="breadcrumb-sep">/</span><span class="breadcrumb-current">{{ isset($recycler) ? 'Edit '.$recycler->code : 'Tambah Baru' }}</span>
@endsection
@section('content')
<div style="max-width:700px">
    <div class="page-header">
        <div class="page-header-left"><h1>{{ isset($recycler) ? 'Edit: '.$recycler->name : 'Tambah Recycler Baru' }}</h1></div>
        <a href="{{ route('superadmin.entitas.recycler.index') }}" class="btn btn-sm"><i class="ti ti-arrow-left"></i>Kembali</a>
    </div>
    <div class="card"><div class="card-body">
        <form action="{{ isset($recycler) ? route('superadmin.entitas.recycler.update',$recycler->id) : route('superadmin.entitas.recycler.store') }}" method="POST">
            @csrf @if(isset($recycler)) @method('PUT') @endif
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Kode <span class="req">*</span></label><input type="text" name="code" class="form-control" value="{{ old('code',$recycler->code??'') }}" placeholder="REC01" required maxlength="10">@error('code')<div class="form-error">{{ $message }}</div>@enderror</div>
                <div class="form-group"><label class="form-label">Tipe Perusahaan</label><input type="text" name="company_type" class="form-control" value="{{ old('company_type',$recycler->company_type??'') }}" placeholder="PT, CV, UD"></div>
            </div>
            <div class="form-group"><label class="form-label">Nama Recycler <span class="req">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name',$recycler->name??'') }}" required>@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Nama PIC</label><input type="text" name="pic_name" class="form-control" value="{{ old('pic_name',$recycler->pic_name??'') }}"></div>
                <div class="form-group"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone',$recycler->phone??'') }}"></div>
            </div>
            <div class="form-group"><label class="form-label">Alamat</label><textarea name="address" class="form-control" rows="2">{{ old('address',$recycler->address??'') }}</textarea></div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Kabupaten/Kota <span class="req">*</span></label><input type="text" name="regency" class="form-control" value="{{ old('regency',$recycler->regency??'') }}" required></div>
                <div class="form-group"><label class="form-label">Status</label><select name="is_active" class="form-control"><option value="1" {{ old('is_active',isset($recycler)?$recycler->is_active:1)==1?'selected':'' }}>Aktif</option><option value="0" {{ old('is_active',isset($recycler)?$recycler->is_active:1)==0?'selected':'' }}>Nonaktif</option></select></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label class="form-label">Latitude</label><input type="number" name="latitude" class="form-control" value="{{ old('latitude',$recycler->latitude??'') }}" step="0.00000001"></div>
                <div class="form-group"><label class="form-label">Longitude</label><input type="number" name="longitude" class="form-control" value="{{ old('longitude',$recycler->longitude??'') }}" step="0.00000001"></div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-green"><i class="ti ti-device-floppy"></i>{{ isset($recycler) ? 'Update' : 'Simpan' }}</button>
                <a href="{{ route('superadmin.entitas.recycler.index') }}" class="btn">Batal</a>
            </div>
        </form>
    </div></div>
</div>
@endsection
