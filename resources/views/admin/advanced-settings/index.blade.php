@extends('layouts.app')
@section('title', 'Pengaturan Lanjutan')

@section('styles')
<style>
/* CSS ini SAMA PERSIS dengan halaman Audio Settings */
.setting-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
.setting-card h3 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
.form-group input, .form-group select { 
    width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; 
}
.form-group small { color: #999; font-size: 0.8rem; margin-top: 5px; display: block; }

/* Input khusus (Time, Color) */
.form-group input[type="time"],
.form-group input[type="color"] {
    padding: 8px;
    height: 40px;
}
.form-group input[type="color"] {
    max-width: 100px;
}

/* Tombol */
.btn-group { display: flex; gap: 10px; margin-top: 20px; }
.btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
.btn-primary { background: #3498db; color: white; }
.btn-primary:hover { background: #2980b9; }

/* Toggle Switch */
.toggle-switch { display: inline-flex; align-items: center; gap: 10px; }
.switch { position: relative; display: inline-block; width: 50px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #27ae60; }
input:checked + .slider:before { transform: translateX(26px); }

/* Grid untuk form */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
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
</style>
@endsection

@section('content')

<div class="card">
    <h2 style="margin-top:0;"><i class="fas fa-wrench"></i> Pengaturan Lanjutan</h2>
    <p style="color: #666; margin-bottom: 20px;">Atur parameter teknis dan operasional sistem.</p>
</div>

@if (session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="setting-card">
    <form action="{{ route('admin.advanced-settings.update') }}" method="POST">
        @csrf
        @method('POST')
        
        <h3><i class="fas fa-clock"></i> Pengaturan Antrian</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="queue_timeout_minutes">Batas Waktu Panggilan (Menit)</label>
                <input type="number" name="queue_timeout_minutes" id="queue_timeout_minutes" 
                       value="{{ $setting->queue_timeout_minutes ?? 30 }}" min="0">
                <small>Waktu sebelum antrian dianggap batal jika tidak dilayani. (0 = tidak ada batas)</small>
            </div>
            <div class="form-group">
                <label for="auto_cancel_timeout">Batalkan Otomatis</label>
                <div class="toggle-switch">
                    <input type="hidden" name="auto_cancel_timeout" value="0">
                    <label class="switch">
                        <input type="checkbox" name="auto_cancel_timeout" value="1" id="auto_cancel_timeout" 
                               {{ $setting->auto_cancel_timeout ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <span id="toggleLabel">{{ $setting->auto_cancel_timeout ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <small>Jika aktif, status antrian otomatis menjadi 'batal' setelah timeout.</small>
            </div>
        </div>

        <h3 style="margin-top: 20px;"><i class="fas fa-tv"></i> Pengaturan Tampilan (Display)</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="theme_color">Warna Tema Utama</label>
                <input type="color" name="theme_color" id="theme_color" 
                       value="{{ $setting->theme_color ?? '#3498db' }}">
                <small>Warna utama untuk tombol dan header display.</small>
            </div>
            <div class="form-group">
                <label for="display_refresh_seconds">Refresh Rate Display (Detik)</label>
                <input type="number" name="display_refresh_seconds" id="display_refresh_seconds" 
                       value="{{ $setting->display_refresh_seconds ?? 5 }}" min="1">
                <small>Seberapa sering layar display me-refresh data (jika WebSocket gagal).</small>
            </div>
        </div>

        <h3 style="margin-top: 20px;"><i class="fas fa-calendar-alt"></i> Pengaturan Jam Kerja</h3>
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
</div>
@endsection

@section('scripts')
<script>
// JavaScript untuk update label toggle
document.getElementById('auto_cancel_timeout').addEventListener('change', function() {
    const isChecked = this.checked;
    document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';
});

// Panggil sekali saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const isChecked = document.getElementById('auto_cancel_timeout').checked;
    document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';
});
</script>
@endsection