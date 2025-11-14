@extends('layouts.app')
@section('title', 'Pengaturan Umum')

@section('styles')
<style>
/* === CSS Modern (Diambil dari UI Anda) === */
.card-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.form-row.full { grid-template-columns: 1fr; }
.form-group { margin-bottom: 0; }
.form-group label { display: block; font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 6px; }

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 10px; 
    font-size: 14px; color: #1e293b; background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.3s;
}
.form-group textarea {
    resize: vertical;
    min-height: 80px;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none; border-color: #3b82f6; background: white; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.form-group input[type="file"] {
    padding: 8px; /* Sedikit penyesuaian untuk input file */
}

.form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; }
.btn-primary { 
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; 
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    padding: 10px 16px; border: none; border-radius: 12px; font-weight: 700; 
    font-size: 14px; cursor: pointer; transition: all 0.3s; 
    display: flex; align-items: center; gap: 8px;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }

/* Logo Preview (Dari UI Lama Anda, disesuaikan) */
.preview-container { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; gap: 15px; border: 1px dashed #ddd; }
.preview-logo { max-width: 200px; max-height: 150px; margin: 0; border: 2px solid #e0e0e0; border-radius: 8px; padding: 5px; background: white; object-fit: contain; }
.preview-info { flex: 1; }
.preview-info p { margin: 5px 0; font-size: 13px; color: #666; }
.preview-info strong { color: #2c3e50; }
.logo-placeholder { background: #f8fafc; padding: 20px; border-radius: 8px; text-align: center; color: #999; margin-bottom: 15px; border: 1px dashed #ddd; }
.logo-placeholder i { font-size: 32px; margin-bottom: 10px; }
.logo-placeholder.has-preview i, .logo-placeholder.has-preview p { display: none; }

/* Alerts (Konsisten) */
.alert { padding: 15px; margin-bottom: 24px; border-radius: 12px; display: flex; align-items: flex-start; gap: 10px; font-weight: 500; }
.alert-success { color: #065f46; background-color: #d1fae5; border-color: #a7f3d0; }
.alert-danger { color: #991b1b; background-color: #fee2e2; border-color: #fca5a5; }

@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .btn-primary { width: 100%; justify-content: center; }
    .form-actions { flex-direction: column; }
}
</style>
@endsection

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle" style="font-size: 20px;"></i> <div>{{ session('success') }}</div>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i> 
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

<section class="card" role="region">
    <h3 class="card-title"><i class="fa-solid fa-sliders"></i> Pengaturan Umum Instansi</h3>
    
    <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group" style="margin-bottom: 16px;">
            <label for="logoInput"><i class="fas fa-image"></i> Logo Instansi</label>

            <div class="preview-container" id="logoPreviewContainer" style="{{ !($pengaturan && $pengaturan->logo) ? 'display:none;' : '' }}">
                <img src="{{ $pengaturan && $pengaturan->logo ? asset('logo/' . $pengaturan->logo) : '' }}" class="preview-logo" alt="Logo" id="logoPreview" style="{{ !($pengaturan && $pengaturan->logo) ? 'display:none;' : '' }}">
                <div class="preview-info">
                    <p><strong>Logo Saat Ini.</strong> Pilih file baru di bawah untuk mengganti.</p>
                </div>
            </div>
            <div class="logo-placeholder" id="logoPlaceholder" style="{{ $pengaturan && $pengaturan->logo ? 'display:none;' : '' }}">
                <i class="fas fa-image"></i>
                <p>Belum ada logo</p>
            </div>

            <input type="file" name="logo" accept="image/*" id="logoInput">
            <small style="color: #64748b; font-size: 11px;">Pilih file baru untuk mengganti logo. Maks 2MB.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="nama_instansi">Nama Instansi</label>
                <input type="text" id="nama_instansi" name="nama_instansi" value="{{ old('nama_instansi', $pengaturan->nama_instansi ?? '') }}" required placeholder="Contoh: RSUD Sehat Sejahtera">
            </div>
            <div class="form-group">
                <label for="telepon">Nomor Telepon</label>
                <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $pengaturan->telepon ?? '') }}" required placeholder="Contoh: (021) 12345678">
            </div>
        </div>
        
        <div class="form-row full">
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3" required placeholder="Alamat lengkap instansi">{{ old('alamat', $pengaturan->alamat ?? '') }}</textarea>
            </div>
        </div>

        <div class="form-row full">
            <div class="form-group">
                <label for="deskripsi">Deskripsi (Opsional)</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang instansi Anda">{{ old('deskripsi', $pengaturan->deskripsi ?? '') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="btn-save-settings">
                <i class="fa-solid fa-save"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</section>

@endsection

@section('scripts')
<script>
// 💡 INI ADALAH JAVASCRIPT UNTUK LIVE PREVIEW LOGO
document.addEventListener('DOMContentLoaded', function() {
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const logoPlaceholder = document.getElementById('logoPlaceholder');
    const logoPreviewContainer = document.getElementById('logoPreviewContainer');

    if (logoInput) {
        logoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (logoPreview) {
                        logoPreview.src = e.target.result;
                        logoPreview.style.display = 'block';
                    }
                    if (logoPreviewContainer) {
                        logoPreviewContainer.style.display = 'flex';
                    }
                    if (logoPlaceholder) {
                        logoPlaceholder.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endsection