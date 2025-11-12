@extends('layouts.app')
@section('title', 'Pengaturan Lanjutan')

@section('styles')
<style>
/* ========================================================= */
/* --- 1. Base Styling (Konsisten dengan App Layout) --- */
/* ========================================================= */
.page-header { margin-bottom: 32px; }
.page-title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; }
.page-subtitle { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; }

.card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 24px;
    margin-bottom: 24px;
}
.card-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ========================================================= */
/* --- 2. Form Styling (Modern) --- */
/* ========================================================= */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.form-group { margin-bottom: 10px; } /* Disesuaikan agar tidak terlalu renggang */
.form-group label { 
    display: block; 
    margin-bottom: 6px; 
    font-weight: 600; 
    font-size: 12px; 
    color: #475569;
}
.form-group input, 
.form-group select {
    width: 100%;
    padding: 10px 14px; 
    border: 1px solid #e2e8f0; 
    border-radius: 10px; 
    font-size: 14px;
    color: #1e293b;
    background: #ffffff;
    font-family: 'Inter', sans-serif;
    transition: all 0.3s;
}
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.form-group small { 
    color: #64748b; /* Warna disamakan */
    font-size: 11px; /* Dikecilkan */
    margin-top: 6px; 
    display: block; 
}

/* Input khusus (Time, Color) */
.form-group input[type="time"],
.form-group input[type="color"] {
    padding: 8px;
    height: 44px; /* Samakan tinggi */
}
.form-group input[type="color"] {
    max-width: 100px;
}

/* Tombol (Modern) */
.btn-group { display: flex; gap: 10px; margin-top: 20px; }
.btn-primary { 
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); 
    color: white; 
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    padding: 10px 16px; border: none; border-radius: 12px; font-weight: 700; 
    font-size: 14px; cursor: pointer; transition: all 0.3s; 
    display: flex; align-items: center; gap: 8px;
}
.btn-primary:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); 
}

/* ========================================================= */
/* --- 3. Toggle Switch Styling (Modern) --- */
/* ========================================================= */
.toggle-switch-container { display: flex; align-items: center; gap: 10px; }
.switch { position: relative; display: inline-block; width: 48px; height: 24px; } /* Ukuran disamakan dengan UI Pengaturan */
.switch input { opacity: 0; width: 0; height: 0; }
.slider { 
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; 
    background-color: #cbd5e1; /* Warna nonaktif modern */
    transition: .4s; border-radius: 24px; 
}
.slider:before { 
    position: absolute; content: ""; height: 20px; width: 20px; left: 2px; bottom: 2px; 
    background-color: white; transition: .4s; border-radius: 50%; 
}
input:checked + .slider { 
    background-color: #3b82f6; /* Warna aktif modern */
}
input:checked + .slider:before { 
    transform: translateX(24px); /* Disesuaikan dengan ukuran baru */
}

/* ========================================================= */
/* --- 4. Alerts & Responsive --- */
/* ========================================================= */
.alert {
    padding: 15px; margin-bottom: 24px; border-radius: 12px; display: flex; 
    align-items: flex-start; gap: 10px; font-weight: 500; 
}
.alert-success { color: #065f46; background-color: #d1fae5; border-color: #a7f3d0; }
.alert-danger { color: #991b1b; background-color: #fee2e2; border-color: #fca5a5; }

@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

<div class="page-header">
    <h1 class="page-title">Pengaturan Lanjutan</h1>
    <p class="page-subtitle">‎ </p>
</div>

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

<section class="card">
    <form action="{{ route('admin.advanced-settings.update') }}" method="POST">
        @csrf
        
        <h3 class="card-title"><i class="fas fa-clock"></i> Pengaturan Antrian</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="queue_timeout_minutes">Batas Waktu Panggilan (Menit)</label>
                <input type="number" name="queue_timeout_minutes" id="queue_timeout_minutes" 
                       value="{{ $setting->queue_timeout_minutes ?? 30 }}" min="0">
                <small>Waktu sebelum antrian dianggap batal jika tidak dilayani. (0 = tidak ada batas)</small>
            </div>
            <div class="form-group">
                <label for="auto_cancel_timeout">Batalkan Otomatis</label>
                <div class="toggle-switch-container">
                    <input type="hidden" name="auto_cancel_timeout" value="0">
                    <label class="switch">
                        <input type="checkbox" name="auto_cancel_timeout" value="1" id="auto_cancel_timeout" 
                               {{ ($setting->auto_cancel_timeout ?? 0) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span id="toggleLabel" style="font-weight: 600; font-size: 13px; color: #475569;">...</span>
                </div>
                <small>Jika aktif, status antrian otomatis menjadi 'batal' setelah timeout.</small>
            </div>
        </div>

        <h3 class="card-title" style="margin-top: 30px;"><i class="fas fa-tv"></i> Pengaturan Tampilan (Display)</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="theme_color">Warna Tema Utama</label>
                <input type="color" name="theme_color" id="theme_color" 
                       value="{{ $setting->theme_color ?? '#3b82f6' }}">
                <small>Warna utama untuk tombol dan header display.</small>
            </div>
            <div class="form-group">
                <label for="display_refresh_seconds">Refresh Rate Display (Detik)</label>
                <input type="number" name="display_refresh_seconds" id="display_refresh_seconds" 
                       value="{{ $setting->display_refresh_seconds ?? 5 }}" min="1">
                <small>Seberapa sering layar display me-refresh data (jika WebSocket gagal).</small>
            </div>
        </div>

        <h3 class="card-title" style="margin-top: 30px;"><i class="fas fa-calendar-alt"></i> Pengaturan Jam Kerja</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="working_hours_start">Jam Buka</label>
                <input type="time" name="working_hours_start" id="working_hours_start" 
                       value="{{ $setting->working_hours_start ? \Carbon\Carbon::parse($setting->working_hours_start)->format('H:i') : '08:00' }}">
                <small>Kios cetak antrian hanya aktif setelah jam ini.</small>
            </div>
            <div class="form-group">
                <label for="working_hours_end">Jam Tutup</label>
                <input type="time" name="working_hours_end" id="working_hours_end" 
                       value="{{ $setting->working_hours_end ? \Carbon\Carbon::parse($setting->working_hours_end)->format('H:i') : '17:00' }}">
                <small>Kios cetak antrian nonaktif setelah jam ini.</small>
            </div>
        </div>
        
        <div class="btn-group" style="justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
</section>
@endsection

@section('scripts')
<script>
// JavaScript untuk update label toggle
const toggleCheckbox = document.getElementById('auto_cancel_timeout');
const toggleLabel = document.getElementById('toggleLabel');

function updateToggleLabel() {
    const isChecked = toggleCheckbox.checked;
    if (isChecked) {
        toggleLabel.textContent = 'Aktif';
        toggleLabel.style.color = '#3b82f6';
    } else {
        toggleLabel.textContent = 'Nonaktif';
        toggleLabel.style.color = '#64748b';
    }
}

// Panggil sekali saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    updateToggleLabel();
});

// Panggil saat nilainya berubah
toggleCheckbox.addEventListener('change', function() {
    updateToggleLabel();
});
</script>
@endsection