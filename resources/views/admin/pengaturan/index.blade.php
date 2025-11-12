// File: resources/views/admin/pengaturan/index.blade.php
// (Asumsi route: admin.pengaturan.index)

@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('styles')
<style>
/* CSS Anda sudah bagus, saya hanya merapikannya sedikit */
.form-group {
    margin-bottom: 1.25rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.form-group input[type="file"] {
    padding: 0.5rem;
}
.form-group small {
    color: #999;
    font-size: 0.8rem;
    margin-top: 5px;
    display: block;
}

/* Penampung preview */
.preview-container {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    border: 1px dashed #ddd;
}
.preview-logo { 
    max-width: 200px; 
    max-height: 150px;
    margin: 0; 
    border: 2px solid #e0e0e0; 
    border-radius: 8px;
    padding: 5px;
    background: white;
}
.preview-info { flex: 1; }
.preview-info p { margin: 5px 0; font-size: 13px; color: #666; }
.preview-info strong { color: #2c3e50; }

/* Placeholder jika tidak ada logo */
.logo-placeholder {
    background: #f8f9fa; 
    padding: 20px; 
    border-radius: 8px; 
    text-align: center; 
    color: #999; 
    margin-bottom: 15px;
    border: 1px dashed #ddd;
}
.logo-placeholder i {
    font-size: 32px; 
    margin-bottom: 10px;
}

/* Info Box */
.info-box {
    background: #e8f5e9; 
    border-left: 4px solid #27ae60; 
    padding: 15px; 
    border-radius: 5px; 
    margin: 20px 0;
}
.info-box p {
    margin: 0; 
    color: #1b5e20;
    font-weight: 500;
}

.btn-primary { 
    background: #3498db; 
    color: white; 
    width: 100%; 
    padding: 12px; 
    font-size: 16px; 
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-primary:hover { background: #2980b9; }

@media (max-width: 768px) {
    .preview-container { flex-direction: column; align-items: flex-start; }
    .preview-logo { max-width: 100%; }
}
</style>
@endsection

@section('content')
<div class="card">
    <h2 style="margin-top:0;"><i class="fas fa-cog"></i> Pengaturan Sistem</h2>
    <p style="color: #666; margin-bottom: 20px;">Kelola informasi dasar instansi Anda di sini</p>
    
    <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label><i class="fas fa-image"></i> Logo Instansi</label>
            
            @if($pengaturan && $pengaturan->logo)
            <div class="preview-container">
                <img src="{{ asset('storage/' . $pengaturan->logo) }}" class="preview-logo" alt="Logo" id="logoPreview">
                <div class="preview-info">
                    <p><strong>Logo Saat Ini:</strong></p>
                    <p>{{ $pengaturan->logo }}</p>
                </div>
            </div>
            @else
            <div class="logo-placeholder" id="logoPlaceholder">
                <i class="fas fa-image"></i>
                <p>Belum ada logo</p>
                <img src="" class="preview-logo" alt="Preview Logo" id="logoPreview" style="display: none; margin-top: 15px;">
            </div>
            @endif
            
            <input type="file" name="logo" accept="image/*" style="padding: 8px;" id="logoInput">
            <small>Format: JPG, PNG, GIF. Maksimal 2MB. Rekomendasi: 200x200px</small>
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
            <small>Deskripsi ini akan ditampilkan di beberapa bagian aplikasi</small>
        </div>
        
        <div class="info-box">
            <p>
                <i class="fas fa-info-circle"></i> Perubahan akan diterapkan langsung ke seluruh aplikasi
            </p>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Menambahkan JS inline untuk live preview gambar
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');

    if (logoInput) {
        logoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (logoPreview) {
                        // Tampilkan gambar yang baru dipilih
                        logoPreview.src = e.target.result;
                        logoPreview.style.display = 'block';
                    }
                    
                    if (logoPlaceholder) {
                        // Jika ada placeholder, sembunyikan teks "Belum ada logo"
                        // dan hanya tampilkan gambar di dalamnya.
                        const placeholderText = logoPlaceholder.querySelector('p');
                        const placeholderIcon = logoPlaceholder.querySelector('i');
                        if (placeholderText) placeholderText.style.display = 'none';
                        if (placeholderIcon) placeholderIcon.style.display = 'none';
                    }
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection