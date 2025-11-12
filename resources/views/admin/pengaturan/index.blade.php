@extends('layouts.app')
@section('title', 'Pengaturan Umum')

@section('styles')
<style>
/* Grup Form */
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
    transition: border-color 0.2s;
}
.form-group input:focus,
.form-group textarea:focus {
    border-color: #3498db;
    outline: none;
}
.form-group input[type="file"] {
    padding: 0.5rem;
    background: #fdfdfd;
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
/* Menyembunyikan placeholder text/icon saat preview JS aktif */
.logo-placeholder.has-preview i,
.logo-placeholder.has-preview p {
    display: none;
}

/* Notifikasi */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}
.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

/* Tombol */
.btn { 
    padding: 12px 20px; 
    border: none; 
    border-radius: 4px; 
    cursor: pointer; 
    font-weight: 600; 
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-primary { 
    background: #3498db; 
    color: white; 
    width: 100%;
    justify-content: center;
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
    <h2 style="margin-top:0;"><i class="fas fa-cog"></i> Pengaturan Umum</h2>
    <p style="color: #666; margin-bottom: 20px;">Kelola informasi dasar instansi Anda di sini</p>
    
    @if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> 
        <div>
            <strong>Gagal menyimpan!</strong>
            <ul style="margin: 5px 0 0 20px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

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
            
            <input type="file" name="logo" accept="image/*" id="logoInput">
            <small>Pilih file baru untuk mengganti logo. Maks 2MB.</small>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-hospital"></i> Nama Instansi</label>
            <input type="text" name="nama_instansi" value="{{ old('nama_instansi', $pengaturan->nama_instansi ?? '') }}" required 
                   placeholder="Contoh: RSUD Sehat Sejahtera">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
            <textarea name="alamat" rows="3" required 
                      placeholder="Alamat lengkap instansi">{{ old('alamat', $pengaturan->alamat ?? '') }}</textarea>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Nomor Telepon</label>
            <input type="text" name="telepon" value="{{ old('telepon', $pengaturan->telepon ?? '') }}" required 
                   placeholder="Contoh: (021) 12345678">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-info-circle"></i> Deskripsi (Opsional)</label>
            <textarea name="deskripsi" rows="3" 
                      placeholder="Deskripsi singkat tentang instansi Anda">{{ old('deskripsi', $pengaturan->deskripsi ?? '') }}</textarea>
            <small>Deskripsi ini akan ditampilkan di beberapa bagian aplikasi</small>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
// 💡 INI ADALAH JAVASCRIPT UNTUK LIVE PREVIEW LOGO
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
                        // 1. Tampilkan gambar yang baru dipilih
                        logoPreview.src = e.target.result;
                        logoPreview.style.display = 'block';
                    }
                    
                    if (logoPlaceholder) {
                        // 2. Sembunyikan icon dan text "Belum ada logo"
                        logoPlaceholder.classList.add('has-preview');
                    }
                };
                
                // 3. Baca file sebagai URL
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection