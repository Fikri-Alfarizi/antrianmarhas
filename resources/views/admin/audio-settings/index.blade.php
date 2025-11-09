@extends('layouts.app')
@section('title', 'Pengaturan Audio & Notifikasi')

@section('styles')
<style>
    .setting-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
    .form-group textarea { min-height: 80px; resize: vertical; }
    .btn-group { display: flex; gap: 10px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
    .btn-primary { background: #3498db; color: white; }
    .btn-primary:hover { background: #2980b9; }
    .btn-secondary { background: #95a5a6; color: white; }
    .btn-secondary:hover { background: #7f8c8d; }
    .btn-success { background: #27ae60; color: white; }
    .btn-success:hover { background: #229954; }
    .toggle-switch { display: inline-flex; align-items: center; gap: 10px; }
    .switch { position: relative; display: inline-block; width: 50px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #27ae60; }
    input:checked + .slider:before { transform: translateX(26px); }
    .volume-container { display: flex; align-items: center; gap: 10px; }
    .volume-container input { flex: 1; }
    .volume-value { min-width: 50px; text-align: right; font-weight: 600; }
    .preview-box { background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 15px; }
    .preview-text { color: #666; font-size: 14px; margin-bottom: 10px; }
    .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745; }
    .info-box { background: #e3f2fd; color: #1976d2; padding: 12px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #1976d2; }
</style>
@endsection

@section('content')
<div class="card">
    <h2><i class="fas fa-volume-up"></i> Pengaturan Audio & Notifikasi</h2>
    <p>Atur pengaturan suara untuk panggilan nomor antrian otomatis</p>
</div>

@if (session('success'))
<div class="success-message">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="setting-card">
    <h3><i class="fas fa-cog"></i> Konfigurasi Notifikasi Suara</h3>
    
    <form action="{{ route('admin.audio-settings.update') }}" method="POST" id="audioSettingsForm">
        @csrf
        @method('POST')
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i> Sistem akan otomatis memutar suara saat nomor antrian dipanggil di loket
        </div>

        <div class="form-group">
            <label for="aktif" style="margin-bottom: 10px;">
                <i class="fas fa-toggle-on"></i> Aktifkan Notifikasi Suara
            </label>
            <div class="toggle-switch">
                <label class="switch">
                    <input type="hidden" name="aktif" value="0">
                    <input type="checkbox" name="aktif" value="1" id="aktifCheck" {{ $setting->aktif ? 'checked' : '' }} onchange="updateToggleLabel()">
                    <span class="slider"></span>
                </label>
                <span id="toggleLabel">{{ $setting->aktif ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>

        <div class="form-group">
            <label for="tipe"><i class="fas fa-microphone"></i> Tipe Audio</label>
            <select name="tipe" id="tipe">
                <option value="text-to-speech" {{ $setting->tipe === 'text-to-speech' ? 'selected' : '' }}>
                    <i class="fas fa-robot"></i> Text-to-Speech (Otomatis)
                </option>
                <option value="audio-file" {{ $setting->tipe === 'audio-file' ? 'selected' : '' }}>
                    <i class="fas fa-file-audio"></i> File Audio (Custom)
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="bahasa"><i class="fas fa-language"></i> Bahasa</label>
            <select name="bahasa" id="bahasa">
                <option value="id" {{ $setting->bahasa === 'id' ? 'selected' : '' }}>Indonesia</option>
                <option value="en" {{ $setting->bahasa === 'en' ? 'selected' : '' }}>English</option>
                <option value="jv" {{ $setting->bahasa === 'jv' ? 'selected' : '' }}>Jawa</option>
                <option value="su" {{ $setting->bahasa === 'su' ? 'selected' : '' }}>Sunda</option>
                <option value="ms" {{ $setting->bahasa === 'ms' ? 'selected' : '' }}>Melayu</option>
            </select>
        </div>

        <div class="form-group">
            <label for="volume">
                <i class="fas fa-volume-high"></i> Volume Suara
            </label>
            <div class="volume-container">
                <input type="range" name="volume" id="volume" min="0" max="100" value="{{ $setting->volume }}" 
                    style="cursor: pointer;">
                <span class="volume-value"><span id="volumeValue">{{ $setting->volume }}</span>%</span>
            </div>
        </div>

        <div class="form-group">
            <label for="format_pesan"><i class="fas fa-message"></i> Format Pesan</label>
            <textarea name="format_pesan" id="format_pesan" placeholder="Gunakan {nomor} untuk nomor antrian dan {lokasi} untuk nama loket">{{ $setting->format_pesan }}</textarea>
            <small style="color: #999;">
                <i class="fas fa-lightbulb"></i> Contoh: "Nomor {nomor} silakan menuju ke {lokasi}"
            </small>
        </div>

        <div class="form-group">
            <label><i class="fas fa-ear"></i> Preview Pesan</label>
            <div class="preview-box">
                <div class="preview-text">
                    <strong>Contoh panggilan:</strong><br>
                    <span id="previewText">Nomor A001 silakan menuju ke Ruang 1</span>
                </div>
                <button type="button" class="btn btn-success" onclick="testAudio()">
                    <i class="fas fa-play"></i> Dengarkan Contoh
                </button>
            </div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>

<div class="setting-card">
    <h3><i class="fas fa-info-circle"></i> Informasi Teknis</h3>
    <ul style="padding-left: 20px; line-height: 1.8;">
        <li><strong>Text-to-Speech:</strong> Menggunakan API Google Translate untuk generate suara real-time</li>
        <li><strong>Kecepatan:</strong> Notifikasi suara diputar dalam 1-2 detik setelah nomor dipanggil</li>
        <li><strong>Kompatibilitas:</strong> Bekerja di semua browser modern (Chrome, Firefox, Safari, Edge)</li>
        <li><strong>Persyaratan:</strong> Koneksi internet untuk Text-to-Speech</li>
        <li><strong>Placeholder Format:</strong>
            <ul style="padding-left: 20px; margin-top: 10px;">
                <li><code>{nomor}</code> → Nomor antrian (contoh: A001)</li>
                <li><code>{lokasi}</code> → Nama loket/ruangan (contoh: Ruang 1)</li>
            </ul>
        </li>
    </ul>
</div>

@endsection

@section('scripts')
<script>
    // Update toggle label
    function updateToggleLabel() {
        const isChecked = document.getElementById('aktifCheck').checked;
        document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';
    }

    // Update volume display
    document.getElementById('volume').addEventListener('input', function() {
        document.getElementById('volumeValue').textContent = this.value;
    });

    // Update preview text
    function updatePreview() {
        const format = document.getElementById('format_pesan').value;
        const preview = format
            .replace('{nomor}', 'A001')
            .replace('{lokasi}', 'Ruang 1');
        document.getElementById('previewText').textContent = preview;
    }

    document.getElementById('format_pesan').addEventListener('change', updatePreview);
    document.getElementById('format_pesan').addEventListener('keyup', updatePreview);

    // Test audio
    function testAudio() {
        console.log('[AUDIO] Testing audio...');
        const format = document.getElementById('format_pesan').value;
        const preview = format
            .replace('{nomor}', 'A001')
            .replace('{lokasi}', 'Ruang 1');
        
        console.log('[AUDIO] Test text:', preview);
        
        // Try Web Speech API first
        try {
            if ('speechSynthesis' in window) {
                console.log('[AUDIO] Using Web Speech API');
                const utterance = new SpeechSynthesisUtterance(preview);
                utterance.lang = 'id-ID';
                utterance.rate = 0.8;
                utterance.pitch = 1.0;
                utterance.volume = document.getElementById('volume').value / 100;
                
                utterance.onerror = (e) => {
                    console.error('[AUDIO] Web Speech error:', e);
                    playGoogleTTS(preview);
                };
                
                window.speechSynthesis.speak(utterance);
                console.log('[AUDIO] Web Speech started');
                return;
            }
        } catch (e) {
            console.error('[AUDIO] Web Speech exception:', e);
        }
        
        // Fallback to Google TTS
        playGoogleTTS(preview);
    }

    // Google TTS for test
    function playGoogleTTS(text) {
        console.log('[AUDIO] Using Google TTS for:', text);
        try {
            const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text)}&tl=id&client=tw-ob`;
            console.log('[AUDIO] TTS URL:', url);
            const audio = new Audio(url);
            audio.volume = document.getElementById('volume').value / 100;
            audio.play().then(() => {
                console.log('[AUDIO] Google TTS playing');
            }).catch(err => {
                console.error('[AUDIO] Google TTS error:', err);
                alert('Tidak bisa memutar audio. Periksa:\n1. Koneksi internet aktif\n2. Browser support audio\n3. Volume setting tidak 0');
            });
        } catch (e) {
            console.error('[AUDIO] Google TTS exception:', e);
            alert('Error: ' + e.message);
        }
    }

    // Initialize preview
    updatePreview();
</script>
@endsection
