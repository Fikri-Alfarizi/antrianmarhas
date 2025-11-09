@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('styles')
<style>
.preview-logo { 
    max-width: 200px; 
    margin: 15px 0; 
    border: 2px solid #e0e0e0; 
    border-radius: 8px;
    padding: 10px;
    background: #f8f9fa;
}
.preview-container { 
    background: #f8f9fa; 
    padding: 15px; 
    border-radius: 8px; 
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.preview-info { flex: 1; }
.preview-info p { margin: 5px 0; font-size: 13px; color: #666; }
.preview-info strong { color: #2c3e50; }
@media (max-width: 768px) {
    .preview-container { flex-direction: column; align-items: flex-start; }
}
</style>
@endsection

@section('content')
<div class="card">
    <h2><i class="fas fa-cog"></i> Pengaturan Sistem</h2>
    <p style="color: #666; margin-bottom: 20px;">Kelola informasi dasar instansi Anda di sini</p>
    
    <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label><i class="fas fa-image"></i> Logo Instansi</label>
            @if($pengaturan && $pengaturan->logo)
            <div class="preview-container">
                <img src="{{ asset('storage/' . $pengaturan->logo) }}" class="preview-logo" alt="Logo">
                <div class="preview-info">
                    <p><strong>Logo Saat Ini:</strong></p>
                    <p>{{ $pengaturan->logo }}</p>
                </div>
            </div>
            @else
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; color: #999; margin-bottom: 15px;">
                <i class="fas fa-image" style="font-size: 32px; margin-bottom: 10px;"></i>
                <p>Belum ada logo</p>
            </div>
            @endif
            <input type="file" name="logo" accept="image/*" style="padding: 8px;">
            <small style="color: #999;">Format: JPG, PNG, GIF. Maksimal 2MB. Ukuran rekomendasi: 200x200px</small>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-hospital"></i> Nama Instansi</label>
            <input type="text" name="nama_instansi" value="{{ $pengaturan->nama_instansi ?? '' }}" required 
                   placeholder="Contoh: RSUD Sehat Sejahtera">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
            <textarea name="alamat" rows="3" required 
                      placeholder="Alamat lengkap instansi">{{ $pengaturan->alamat ?? '' }}</textarea>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Nomor Telepon</label>
            <input type="text" name="telepon" value="{{ $pengaturan->telepon ?? '' }}" required 
                   placeholder="Contoh: (021) 12345678">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-info-circle"></i> Deskripsi (Opsional)</label>
            <textarea name="deskripsi" rows="3" 
                      placeholder="Deskripsi singkat tentang instansi Anda">{{ $pengaturan->deskripsi ?? '' }}</textarea>
            <small style="color: #999;">Deskripsi ini akan ditampilkan di beberapa bagian aplikasi</small>
        </div>
        
        <div style="background: #e8f5e9; border-left: 4px solid #27ae60; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; color: #1b5e20;">
                <i class="fas fa-info-circle"></i> Perubahan akan diterapkan langsung ke seluruh aplikasi
            </p>
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@endsection
