@extends('layouts.app')

@section('title', 'Pengaturan Audio & Notifikasi')

@section('styles')
<style>
    /* ... (Semua CSS Anda sudah benar) ... */
    .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    .card h3 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
    .form-group input, .form-group select, .form-group textarea { 
        width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; 
        transition: border-color 0.2s; 
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #3498db; outline: none; }
    .form-group textarea { min-height: 80px; resize: vertical; }
    
    .btn-group { display: flex; gap: 10px; margin-top: 20px; }
    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: 0.2s; }
    .btn-primary { background: #3498db; color: white; }
    .btn-primary:hover { background: #2980b9; }
    .btn-secondary { background: #95a5a6; color: white; }
    .btn-secondary:hover { background: #7f8c8d; }
    .btn-success { background: #27ae60; color: white; }
    .btn-success:hover { background: #229954; }

    /* TOGGLE SWITCH */
    .toggle-switch { display: inline-flex; align-items: center; gap: 10px; }
    .switch { position: relative; display: inline-block; width: 50px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #27ae60; }
    input:checked + .slider:before { transform: translateX(26px); }
    
    /* VOLUME SLIDER */
    .volume-container { display: flex; align-items: center; gap: 10px; }
    .volume-container input[type="range"] { flex: 1; height: 4px; padding: 0; }
    .volume-value { min-width: 50px; text-align: right; font-weight: 600; }
    
    /* INFO & MESSAGES */
    .preview-box { background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 15px; border: 1px solid #eee; }
    .preview-text { color: #666; font-size: 14px; margin-bottom: 10px; }
    .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745; font-weight: 500; }
    .info-box { background: #e3f2fd; color: #1976d2; padding: 12px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #1976d2; font-size: 14px; }
</style>
@endsection

@section('content')
<div class="card">
    <h2 style="margin-top: 0;"><i class="fas fa-volume-up"></i> Pengaturan Audio & Notifikasi</h2>
    <p>Atur pengaturan suara untuk panggilan nomor antrian otomatis yang muncul di layar display.</p>
</div>

@if (session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="card">
    <h3><i class="fas fa-cog"></i> Konfigurasi Notifikasi Suara</h3>
    
    <form action="{{ route('admin.audio-settings.update') }}" method="POST" id="audioSettingsForm">
        @csrf
        {{-- Kita tidak perlu @method('POST') jika rutenya adalah POST --}}
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i> Pastikan speaker **Display Ruang Tunggu** aktif dan volume cukup besar. Audio akan disiarkan real-time.
        </div>

        <div class="form-group">
            <label for="aktif" style="margin-bottom: 10px;">
                <i class="fas fa-toggle-on"></i> Aktifkan Notifikasi Suara
            </label>
            <div class="toggle-switch">
                                <input type="hidden" name="aktif" value="0">
                <label class="switch">
                    <input type="checkbox" name="aktif" value="1" id="aktifCheck" 
                        {{ $setting->aktif ? 'checked' : '' }} onchange="updateToggleLabel()">
                    <span class="slider"></span>
                </label>
                <span id="toggleLabel">{{ $setting->aktif ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>

        <div class="form-group">
            <label for="tipe"><i class="fas fa-microphone"></i> Tipe Audio</label>
            <select name="tipe" id="tipe">
                <option value="text-to-speech" {{ $setting->tipe === 'text-to-speech' ? 'selected' : '' }}>
                    Text-to-Speech (Otomatis)
                </option>
                <option value="audio-file" {{ $setting->tipe === 'audio-file' ? 'selected' : '' }}>
                    File Audio (Custom)
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="bahasa"><i class="fas fa-language"></i> Bahasa</label>
            <select name="bahasa" id="bahasa" disabled>
                <option value="id" selected>Indonesia (id) - Bahasa Indonesia Saja</option>
            </select>
            <small style="color: #999;">
                <i class="fas fa-lock"></i> Sistem ini dikonfigurasi untuk hanya menggunakan Bahasa Indonesia.
            </small>
        </div>        <div class="form-group">
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
                <i class="fas fa-lightbulb"></i> Contoh: "Nomor {nomor} silakan menuju ke {lokasi}".
            </small>
        </div>

        <div class="form-group">
            <label><i class="fas fa-ear"></i> Preview Pesan</label>
            <div class="preview-box">
                <div class="preview-text">
                    <strong>Contoh panggilan:</strong><br>
                    <span id="previewText"></span>
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
        <li>**Text-to-Speech:** Menggunakan API Google Translate (fallback) atau Web Speech API (prioritas) untuk generate suara.</li>
        <li>**Placeholder Format:**
            <ul style="padding-left: 20px; margin-top: 10px;">
                <li><code>{nomor}</code> &rarr; Nomor antrian (contoh: A001)</li>
                <li><code>{lokasi}</code> &rarr; Nama loket/ruangan (contoh: Ruang 1)</li>
            </ul>
        </li>
    </ul>
</div>
@endsection

@section('scripts')
<script>
    /* ... (Semua JavaScript Anda sudah benar) ... */
    const defaultFormat = "Nomor {nomor} silakan menuju ke {lokasi}";
    const defaultLang = 'id';
    
    function getMessageText() {
        const format = document.getElementById('format_pesan').value.trim() || defaultFormat;
        const preview = format
            .replace('{nomor}', 'A001')
            .replace('{lokasi}', 'Ruang 1');
        return preview;
    }

    function playNotificationSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const gainNode = audioContext.createGain();
            gainNode.connect(audioContext.destination);
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            const times = [
                { freq: 600, start: 0, end: 0.15 },
                { freq: 800, start: 0.25, end: 0.4 },
            ];
            times.forEach(({ freq, start, end }) => {
                const osc = audioContext.createOscillator();
                osc.frequency.value = freq;
                osc.connect(gainNode);
                osc.start(audioContext.currentTime + start);
                osc.stop(audioContext.currentTime + end);
            });
        } catch (e) {
            console.error('Beep audio error:', e);
        }
    }

    function playGoogleTTS(text, lang) {
        console.log('[AUDIO] Fallback: Playing Google TTS...');
        const langCode = lang || defaultLang;
        try {
            const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text)}&tl=${langCode}&client=tw-ob`;
            const audio = new Audio(url);
            audio.volume = document.getElementById('volume').value / 100;
            audio.play().then(() => {
                console.log('[AUDIO] Google TTS playing successfully.');
            }).catch(err => {
                console.error('[AUDIO] Google TTS play error:', err);
                alert('Tidak dapat memutar audio. Pastikan koneksi internet aktif.');
            });
        } catch (e) {
            console.error('[AUDIO] Google TTS exception:', e);
        }
    }

    function tryWebSpeech(text, lang) {
        console.log('[AUDIO] Priority: Trying Web Speech API...');
        if (!('speechSynthesis' in window)) {
            console.warn('[AUDIO] Web Speech API not available.');
            return false;
        }
        try {
            if (window.speechSynthesis.speaking) {
                window.speechSynthesis.cancel();
            }
            const utterance = new SpeechSynthesisUtterance(text);
            const langMap = {
                'id': 'id-ID', 'en': 'en-US', 'jv': 'jv-ID', 'su': 'su-ID', 'ms': 'ms-MY'
            };
            utterance.lang = langMap[lang] || 'en-US'; 
            utterance.rate = 0.9;
            utterance.pitch = 1.0;
            utterance.volume = document.getElementById('volume').value / 100;
            utterance.onend = () => { console.log('[AUDIO] Web Speech completed.'); };
            utterance.onerror = (e) => { 
                console.error('[AUDIO] Web Speech Error:', e.error, '-> Falling back to Google TTS');
                playGoogleTTS(text, lang);
            };
            window.speechSynthesis.speak(utterance);
            console.log(`[AUDIO] Web Speech started with language: ${utterance.lang}`);
            return true;
        } catch (e) {
            console.error('[AUDIO] Web Speech exception:', e);
            return false;
        }
    }

    function testAudio() {
        if (!document.getElementById('aktifCheck').checked) {
            alert('Notifikasi Suara saat ini Nonaktif. Silakan aktifkan untuk menguji.');
            return;
        }
        const message = getMessageText();
        console.log('[TEST] Initiating audio test:', message);
        const webSpeechSuccess = tryWebSpeech(message, 'id');
        if (!webSpeechSuccess && !('speechSynthesis' in window)) {
            playGoogleTTS(message, 'id');
        }
    }    function updateToggleLabel() {
        const isChecked = document.getElementById('aktifCheck').checked;
        document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';
    }

    document.getElementById('volume').addEventListener('input', function() {
        document.getElementById('volumeValue').textContent = this.value;
    });

    function updatePreview() {
        document.getElementById('previewText').textContent = getMessageText();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
        updateToggleLabel();
        document.getElementById('format_pesan').addEventListener('change', updatePreview);
        document.getElementById('format_pesan').addEventListener('keyup', updatePreview);
        document.getElementById('bahasa').addEventListener('change', updatePreview);
    });
</script>
@endsection