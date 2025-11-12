@extends('layouts.app')
@section('title', 'Tambah Loket Baru')

@section('styles')
<style>
/* CSS ini diambil dari file index Anda untuk konsistensi */
.card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); padding: 24px;
    margin-bottom: 24px; max-width: 700px; /* Batasi lebar form */
}
.card-title {
    font-size: 20px; font-weight: 700; color: #0f172a;
    margin: 0 0 20px 0; padding-bottom: 15px; 
    border-bottom: 1px solid #e2e8f0;
}
.form-grid {
    display: grid; grid-template-columns: 1fr; gap: 16px;
}
.form-group { margin-bottom: 12px; }
.form-group label {
    display: block; font-weight: 600; font-size: 12px; 
    color: #475569; margin-bottom: 6px; 
}
.form-group input, .form-group select {
    width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; 
    border-radius: 10px; font-size: 14px; color: #1e293b; 
    background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.3s;
}
.form-group input:focus, .form-group select:focus {
    outline: none; border-color: #3b82f6; background: white;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.action-buttons {
    display: flex; gap: 8px; margin-top: 20px; justify-content: flex-end;
}
.btn { 
    padding: 10px 18px; border: none; border-radius: 12px; cursor: pointer; 
    font-weight: 600; font-size: 14px; display: inline-flex;
    align-items: center; gap: 5px; transition: all 0.2s;
    text-decoration: none;
}
.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.alert-danger {
    padding: 15px; margin-bottom: 24px; border-radius: 12px; 
    color: #991b1b; background-color: #fee2e2; border: 1px solid #fca5a5;
    display: flex; align-items: flex-start; gap: 10px; font-weight: 500;
}
</style>
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger" style="max-width: 700px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i> 
        <div>
            <strong>Gagal memproses data!</strong>
            <ul style="margin: 5px 0 0 20px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="card">
    <h3 class="card-title"><i class="fas fa-plus-circle"></i> Tambah Loket Baru</h3>
    
    <form action="{{ route('admin.loket.store') }}" method="POST">
        @csrf
        <div class="form-grid">
            <div class="form-group" style="grid-column: span 1;">
                <label for="nama_loket">Nama Loket</label>
                <input type="text" id="nama_loket" name="nama_loket" placeholder="Contoh: Ruang 1 / Konter A" required value="{{ old('nama_loket') }}">
            </div>
            
            <div class="form-group">
                <label for="layanan_id">Layanan yang Dilayani</label>
                <select id="layanan_id" name="layanan_id" required>
                    <option value="" disabled {{ old('layanan_id') ? '' : 'selected' }}>-- Pilih Layanan --</option>
                    @foreach($layanans as $layanan)
                        <option value="{{ $layanan->id }}" {{ old('layanan_id') == $layanan->id ? 'selected' : '' }}>{{ $layanan->nama_layanan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status Awal</label>
                <select id="status" name="status" required>
                    <option value="aktif" {{ old('status') == 'aktif' || !old('status') ? 'selected' : '' }}>Aktif</option>
                    <option value="tutup" {{ old('status') == 'tutup' ? 'selected' : '' }}>Tutup</option>
                </select>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.loket.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        </div>
    </form>
</div>

@endsection