@extends('layouts.app')@extends('layouts.app')



@section('title', 'Pengaturan Audio & Notifikasi')@section('title', 'Pengaturan Audio & Notifikasi')



@section('styles')@section('styles')

<style><style>

    .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }    /* ... (Semua CSS Anda sudah benar) ... */

    .card h3 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }    .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }

    .form-group { margin-bottom: 15px; }    .card h3 { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }

    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }    .form-group { margin-bottom: 15px; }

    .form-group input, .form-group select, .form-group textarea {     .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }

        width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;     .form-group input, .form-group select, .form-group textarea { 

        transition: border-color 0.2s;         width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; 

    }        transition: border-color 0.2s; 

    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #3498db; outline: none; }    }

    .form-group textarea { min-height: 80px; resize: vertical; }    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #3498db; outline: none; }

        .form-group textarea { min-height: 80px; resize: vertical; }

    .btn-group { display: flex; gap: 10px; margin-top: 20px; }    

    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: 0.2s; }    .btn-group { display: flex; gap: 10px; margin-top: 20px; }

    .btn-primary { background: #3498db; color: white; }    .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: 0.2s; }

    .btn-primary:hover { background: #2980b9; }    .btn-primary { background: #3498db; color: white; }

    .btn-secondary { background: #95a5a6; color: white; }    .btn-primary:hover { background: #2980b9; }

    .btn-secondary:hover { background: #7f8c8d; }    .btn-secondary { background: #95a5a6; color: white; }

    .btn-success { background: #27ae60; color: white; }    .btn-secondary:hover { background: #7f8c8d; }

    .btn-success:hover { background: #229954; }    .btn-success { background: #27ae60; color: white; }

    .btn-success:hover { background: #229954; }

    .toggle-switch { display: inline-flex; align-items: center; gap: 10px; }

    .switch { position: relative; display: inline-block; width: 50px; height: 24px; }    /* TOGGLE SWITCH */

    .switch input { opacity: 0; width: 0; height: 0; }    .toggle-switch { display: inline-flex; align-items: center; gap: 10px; }

    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }    .switch { position: relative; display: inline-block; width: 50px; height: 24px; }

    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }    .switch input { opacity: 0; width: 0; height: 0; }

    input:checked + .slider { background-color: #27ae60; }    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }

    input:checked + .slider:before { transform: translateX(26px); }    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }

        input:checked + .slider { background-color: #27ae60; }

    .volume-container { display: flex; align-items: center; gap: 10px; }    input:checked + .slider:before { transform: translateX(26px); }

    .volume-container input[type="range"] { flex: 1; height: 4px; padding: 0; }    

    .volume-value { min-width: 50px; text-align: right; font-weight: 600; }    /* VOLUME SLIDER */

        .volume-container { display: flex; align-items: center; gap: 10px; }

    .preview-box { background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 15px; border: 1px solid #eee; }    .volume-container input[type="range"] { flex: 1; height: 4px; padding: 0; }

    .preview-text { color: #666; font-size: 14px; margin-bottom: 10px; }    .volume-value { min-width: 50px; text-align: right; font-weight: 600; }

    .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745; font-weight: 500; }    

    .info-box { background: #e3f2fd; color: #1976d2; padding: 12px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #1976d2; font-size: 14px; }    /* INFO & MESSAGES */

</style>    .preview-box { background: #f5f5f5; padding: 15px; border-radius: 4px; margin-top: 15px; border: 1px solid #eee; }

@endsection    .preview-text { color: #666; font-size: 14px; margin-bottom: 10px; }

    .success-message { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745; font-weight: 500; }

@section('content')    .info-box { background: #e3f2fd; color: #1976d2; padding: 12px; border-radius: 4px; margin-bottom: 15px; border-left: 4px solid #1976d2; font-size: 14px; }

<div class="card"></style>

    <h2 style="margin-top: 0;"><i class="fas fa-volume-up"></i> Pengaturan Audio & Notifikasi</h2>@endsection

    <p>Atur pengaturan suara untuk panggilan nomor antrian otomatis yang muncul di layar display (Bahasa Indonesia Saja).</p>

</div>@section('content')

<div class="card">

@if (session('success'))    <h2 style="margin-top: 0;"><i class="fas fa-volume-up"></i> Pengaturan Audio & Notifikasi</h2>

<div class="alert alert-success">    <p>Atur pengaturan suara untuk panggilan nomor antrian otomatis yang muncul di layar display.</p>

    <i class="fas fa-check-circle"></i> {{ session('success') }}</div>

</div>

@endif@if (session('success'))

<div class="alert alert-success">

<div class="card">    <i class="fas fa-check-circle"></i> {{ session('success') }}

    <h3><i class="fas fa-cog"></i> Konfigurasi Notifikasi Suara</h3></div>

    @endif

    <form action="{{ route('admin.audio-settings.update') }}" method="POST" id="audioSettingsForm">

        @csrf<div class="card">

            <h3><i class="fas fa-cog"></i> Konfigurasi Notifikasi Suara</h3>

        <div class="info-box">    

            <i class="fas fa-info-circle"></i> <strong>🔒 BAHASA TERKUNCI: INDONESIA SAJA</strong> - Pastikan speaker display aktif dan volume cukup besar.    <form action="{{ route('admin.audio-settings.update') }}" method="POST" id="audioSettingsForm">

        </div>        @csrf

        {{-- Kita tidak perlu @method('POST') jika rutenya adalah POST --}}

        <div class="form-group">        

            <label for="aktif" style="margin-bottom: 10px;">        <div class="info-box">

                <i class="fas fa-toggle-on"></i> Aktifkan Notifikasi Suara            <i class="fas fa-info-circle"></i> Pastikan speaker **Display Ruang Tunggu** aktif dan volume cukup besar. Audio akan disiarkan real-time.

            </label>        </div>

            <div class="toggle-switch">

                <input type="hidden" name="aktif" value="0">        <div class="form-group">

                <label class="switch">            <label for="aktif" style="margin-bottom: 10px;">

                    <input type="checkbox" name="aktif" value="1" id="aktifCheck"                 <i class="fas fa-toggle-on"></i> Aktifkan Notifikasi Suara

                        {{ $setting->aktif ? 'checked' : '' }} onchange="updateToggleLabel()">            </label>

                    <span class="slider"></span>            <div class="toggle-switch">

                </label>                                <input type="hidden" name="aktif" value="0">

                <span id="toggleLabel">{{ $setting->aktif ? 'Aktif' : 'Nonaktif' }}</span>                <label class="switch">

            </div>                    <input type="checkbox" name="aktif" value="1" id="aktifCheck" 

        </div>                        {{ $setting->aktif ? 'checked' : '' }} onchange="updateToggleLabel()">

                    <span class="slider"></span>

        <div class="form-group">                </label>

            <label for="tipe"><i class="fas fa-microphone"></i> Tipe Audio</label>                <span id="toggleLabel">{{ $setting->aktif ? 'Aktif' : 'Nonaktif' }}</span>

            <select name="tipe" id="tipe">            </div>

                <option value="text-to-speech" {{ $setting->tipe === 'text-to-speech' ? 'selected' : '' }}>        </div>

                    Text-to-Speech (Otomatis)

                </option>        <div class="form-group">

                <option value="audio-file" {{ $setting->tipe === 'audio-file' ? 'selected' : '' }}>            <label for="tipe"><i class="fas fa-microphone"></i> Tipe Audio</label>

                    File Audio (Custom)            <select name="tipe" id="tipe">

                </option>                <option value="text-to-speech" {{ $setting->tipe === 'text-to-speech' ? 'selected' : '' }}>

            </select>                    Text-to-Speech (Otomatis)

        </div>                </option>

                <option value="audio-file" {{ $setting->tipe === 'audio-file' ? 'selected' : '' }}>

        <div class="form-group">                    File Audio (Custom)

            <label for="bahasa"><i class="fas fa-language"></i> Bahasa</label>                </option>

            <select name="bahasa" id="bahasa" disabled>            </select>

                <option value="id" selected>🇮🇩 Indonesia (id) - BAHASA INDONESIA SAJA</option>        </div>

            </select>

            <small style="color: #999;">        <div class="form-group">

                <i class="fas fa-lock"></i> Sistem dikonfigurasi hanya untuk Bahasa Indonesia. Tidak bisa diubah.            <label for="bahasa"><i class="fas fa-language"></i> Bahasa</label>

            </small>            <select name="bahasa" id="bahasa" disabled>

        </div>                <option value="id" selected>Indonesia (id) - Bahasa Indonesia Saja</option>

            </select>

        <div class="form-group">            <small style="color: #999;">

            <label for="volume">                <i class="fas fa-lock"></i> Sistem ini dikonfigurasi untuk hanya menggunakan Bahasa Indonesia.

                <i class="fas fa-volume-high"></i> Volume Suara            </small>

            </label>        </div>        <div class="form-group">

            <div class="volume-container">            <label for="volume">

                <input type="range" name="volume" id="volume" min="0" max="100" value="{{ $setting->volume }}"                 <i class="fas fa-volume-high"></i> Volume Suara

                    style="cursor: pointer;">            </label>

                <span class="volume-value"><span id="volumeValue">{{ $setting->volume }}</span>%</span>            <div class="volume-container">

            </div>                <input type="range" name="volume" id="volume" min="0" max="100" value="{{ $setting->volume }}" 

        </div>                    style="cursor: pointer;">

                <span class="volume-value"><span id="volumeValue">{{ $setting->volume }}</span>%</span>

        <div class="form-group">            </div>

            <label for="format_pesan"><i class="fas fa-message"></i> Format Pesan</label>        </div>

            <textarea name="format_pesan" id="format_pesan" placeholder="Gunakan {nomor} untuk nomor antrian dan {lokasi} untuk nama loket">{{ $setting->format_pesan }}</textarea>

            <small style="color: #999;">        <div class="form-group">

                <i class="fas fa-lightbulb"></i> Contoh: "Nomor {nomor} silakan menuju ke {lokasi}".            <label for="format_pesan"><i class="fas fa-message"></i> Format Pesan</label>

            </small>            <textarea name="format_pesan" id="format_pesan" placeholder="Gunakan {nomor} untuk nomor antrian dan {lokasi} untuk nama loket">{{ $setting->format_pesan }}</textarea>

        </div>            <small style="color: #999;">

                <i class="fas fa-lightbulb"></i> Contoh: "Nomor {nomor} silakan menuju ke {lokasi}".

        <div class="form-group">            </small>

            <label><i class="fas fa-ear"></i> Preview Pesan (Bahasa Indonesia)</label>        </div>

            <div class="preview-box">

                <div class="preview-text">        <div class="form-group">

                    <strong>Contoh panggilan:</strong><br>            <label><i class="fas fa-ear"></i> Preview Pesan</label>

                    <span id="previewText"></span>            <div class="preview-box">

                </div>                <div class="preview-text">

                <button type="button" class="btn btn-success" onclick="testAudio()">                    <strong>Contoh panggilan:</strong><br>

                    <i class="fas fa-play"></i> Dengarkan Contoh (ID)                    <span id="previewText"></span>

                </button>                </div>

            </div>                <button type="button" class="btn btn-success" onclick="testAudio()">

        </div>                    <i class="fas fa-play"></i> Dengarkan Contoh

                </button>

        <div class="btn-group">            </div>

            <button type="submit" class="btn btn-primary">        </div>

                <i class="fas fa-save"></i> Simpan Pengaturan

            </button>        <div class="btn-group">

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">            <button type="submit" class="btn btn-primary">

                <i class="fas fa-arrow-left"></i> Kembali                <i class="fas fa-save"></i> Simpan Pengaturan

            </a>            </button>

        </div>            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">

    </form>                <i class="fas fa-arrow-left"></i> Kembali

</div>            </a>

        </div>

<div class="setting-card">    </form>

    <h3><i class="fas fa-info-circle"></i> Informasi Teknis</h3></div>

    <ul style="padding-left: 20px; line-height: 1.8;">

        <li>**Text-to-Speech:** Menggunakan Web Speech API (prioritas) + Google Translate TTS (fallback) untuk suara Bahasa Indonesia.</li><div class="setting-card">

        <li>**Placeholder Format:**    <h3><i class="fas fa-info-circle"></i> Informasi Teknis</h3>

            <ul style="padding-left: 20px; margin-top: 10px;">    <ul style="padding-left: 20px; line-height: 1.8;">

                <li><code>{nomor}</code> &rarr; Nomor antrian (contoh: A001)</li>        <li>**Text-to-Speech:** Menggunakan API Google Translate (fallback) atau Web Speech API (prioritas) untuk generate suara.</li>

                <li><code>{lokasi}</code> &rarr; Nama loket/ruangan (contoh: Ruang 1)</li>        <li>**Placeholder Format:**

            </ul>            <ul style="padding-left: 20px; margin-top: 10px;">

        </li>                <li><code>{nomor}</code> &rarr; Nomor antrian (contoh: A001)</li>

        <li>**Bahasa:** Sistem ini **DIKUNCI HANYA UNTUK BAHASA INDONESIA** (id-ID). Tidak ada opsi bahasa lain.</li>                <li><code>{lokasi}</code> &rarr; Nama loket/ruangan (contoh: Ruang 1)</li>

    </ul>            </ul>

</div>        </li>

@endsection    </ul>

</div>

@section('scripts')@endsection

<script>

    const defaultFormat = "Nomor {nomor} silakan menuju ke {lokasi}";@section('scripts')

    <script>

    function getMessageText() {    /* ... (Semua JavaScript Anda sudah benar) ... */

        const format = document.getElementById('format_pesan').value.trim() || defaultFormat;    const defaultFormat = "Nomor {nomor} silakan menuju ke {lokasi}";

        const preview = format    const defaultLang = 'id';

            .replace('{nomor}', 'A001')    

            .replace('{lokasi}', 'Ruang 1');    function getMessageText() {

        return preview;        const format = document.getElementById('format_pesan').value.trim() || defaultFormat;

    }        const preview = format

            .replace('{nomor}', 'A001')

    function playNotificationSound() {            .replace('{lokasi}', 'Ruang 1');

        try {        return preview;

            const audioContext = new (window.AudioContext || window.webkitAudioContext)();    }

            const gainNode = audioContext.createGain();

            gainNode.connect(audioContext.destination);    function playNotificationSound() {

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);        try {

            const times = [            const audioContext = new (window.AudioContext || window.webkitAudioContext)();

                { freq: 600, start: 0, end: 0.15 },            const gainNode = audioContext.createGain();

                { freq: 800, start: 0.25, end: 0.4 },            gainNode.connect(audioContext.destination);

            ];            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);

            times.forEach(({ freq, start, end }) => {            const times = [

                const osc = audioContext.createOscillator();                { freq: 600, start: 0, end: 0.15 },

                osc.frequency.value = freq;                { freq: 800, start: 0.25, end: 0.4 },

                osc.connect(gainNode);            ];

                osc.start(audioContext.currentTime + start);            times.forEach(({ freq, start, end }) => {

                osc.stop(audioContext.currentTime + end);                const osc = audioContext.createOscillator();

            });                osc.frequency.value = freq;

        } catch (e) {                osc.connect(gainNode);

            console.error('Beep audio error:', e);                osc.start(audioContext.currentTime + start);

        }                osc.stop(audioContext.currentTime + end);

    }            });

        } catch (e) {

    function playGoogleTTS(text) {            console.error('Beep audio error:', e);

        console.log('[AUDIO TEST] Fallback: Playing Google TTS (ID)...');        }

        try {    }

            const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text)}&tl=id&client=tw-ob`;

            const audio = new Audio(url);    function playGoogleTTS(text, lang) {

            audio.volume = document.getElementById('volume').value / 100;        console.log('[AUDIO] Fallback: Playing Google TTS...');

            audio.play().then(() => {        const langCode = lang || defaultLang;

                console.log('[AUDIO TEST] Google TTS playing successfully.');        try {

            }).catch(err => {            const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text)}&tl=${langCode}&client=tw-ob`;

                console.error('[AUDIO TEST] Google TTS play error:', err);            const audio = new Audio(url);

                alert('Tidak dapat memutar audio. Pastikan koneksi internet aktif.');            audio.volume = document.getElementById('volume').value / 100;

            });            audio.play().then(() => {

        } catch (e) {                console.log('[AUDIO] Google TTS playing successfully.');

            console.error('[AUDIO TEST] Google TTS exception:', e);            }).catch(err => {

        }                console.error('[AUDIO] Google TTS play error:', err);

    }                alert('Tidak dapat memutar audio. Pastikan koneksi internet aktif.');

            });

    function tryWebSpeech(text) {        } catch (e) {

        console.log('[AUDIO TEST] Priority: Trying Web Speech API (id-ID)...');            console.error('[AUDIO] Google TTS exception:', e);

        if (!('speechSynthesis' in window)) {        }

            console.warn('[AUDIO TEST] Web Speech API not available.');    }

            return false;

        }    function tryWebSpeech(text, lang) {

        try {        console.log('[AUDIO] Priority: Trying Web Speech API...');

            if (window.speechSynthesis.speaking) {        if (!('speechSynthesis' in window)) {

                window.speechSynthesis.cancel();            console.warn('[AUDIO] Web Speech API not available.');

            }            return false;

            const utterance = new SpeechSynthesisUtterance(text);        }

            utterance.lang = 'id-ID';         try {

            utterance.rate = 0.9;            if (window.speechSynthesis.speaking) {

            utterance.pitch = 1.0;                window.speechSynthesis.cancel();

            utterance.volume = document.getElementById('volume').value / 100;            }

            utterance.onend = () => { console.log('[AUDIO TEST] Web Speech completed.'); };            const utterance = new SpeechSynthesisUtterance(text);

            utterance.onerror = (e) => {             const langMap = {

                console.warn('[AUDIO TEST] Web Speech Error:', e.error, '-> Trying Google TTS');                'id': 'id-ID', 'en': 'en-US', 'jv': 'jv-ID', 'su': 'su-ID', 'ms': 'ms-MY'

                playGoogleTTS(text);            };

            };            utterance.lang = langMap[lang] || 'en-US'; 

            window.speechSynthesis.speak(utterance);            utterance.rate = 0.9;

            console.log('[AUDIO TEST] Web Speech started with language: id-ID');            utterance.pitch = 1.0;

            return true;            utterance.volume = document.getElementById('volume').value / 100;

        } catch (e) {            utterance.onend = () => { console.log('[AUDIO] Web Speech completed.'); };

            console.error('[AUDIO TEST] Web Speech exception:', e);            utterance.onerror = (e) => { 

            return false;                console.error('[AUDIO] Web Speech Error:', e.error, '-> Falling back to Google TTS');

        }                playGoogleTTS(text, lang);

    }            };

            window.speechSynthesis.speak(utterance);

    function testAudio() {            console.log(`[AUDIO] Web Speech started with language: ${utterance.lang}`);

        if (!document.getElementById('aktifCheck').checked) {            return true;

            alert('Notifikasi Suara saat ini Nonaktif. Silakan aktifkan untuk menguji.');        } catch (e) {

            return;            console.error('[AUDIO] Web Speech exception:', e);

        }            return false;

        const message = getMessageText();        }

        console.log('[AUDIO TEST] Initiating audio test:', message);    }

        const webSpeechSuccess = tryWebSpeech(message);

        if (!webSpeechSuccess && !('speechSynthesis' in window)) {    function testAudio() {

            playGoogleTTS(message);        if (!document.getElementById('aktifCheck').checked) {

        }            alert('Notifikasi Suara saat ini Nonaktif. Silakan aktifkan untuk menguji.');

    }            return;

        }

    function updateToggleLabel() {        const message = getMessageText();

        const isChecked = document.getElementById('aktifCheck').checked;        console.log('[TEST] Initiating audio test:', message);

        document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';        const webSpeechSuccess = tryWebSpeech(message, 'id');

    }        if (!webSpeechSuccess && !('speechSynthesis' in window)) {

            playGoogleTTS(message, 'id');

    document.getElementById('volume').addEventListener('input', function() {        }

        document.getElementById('volumeValue').textContent = this.value;    }    function updateToggleLabel() {

    });        const isChecked = document.getElementById('aktifCheck').checked;

        document.getElementById('toggleLabel').textContent = isChecked ? 'Aktif' : 'Nonaktif';

    function updatePreview() {    }

        document.getElementById('previewText').textContent = getMessageText();

    }    document.getElementById('volume').addEventListener('input', function() {

            document.getElementById('volumeValue').textContent = this.value;

    document.addEventListener('DOMContentLoaded', function() {    });

        updatePreview();

        updateToggleLabel();    function updatePreview() {

        document.getElementById('format_pesan').addEventListener('change', updatePreview);        document.getElementById('previewText').textContent = getMessageText();

        document.getElementById('format_pesan').addEventListener('keyup', updatePreview);    }

    });    

</script>    document.addEventListener('DOMContentLoaded', function() {

@endsection        updatePreview();

        updateToggleLabel();
        document.getElementById('format_pesan').addEventListener('change', updatePreview);
        document.getElementById('format_pesan').addEventListener('keyup', updatePreview);
        document.getElementById('bahasa').addEventListener('change', updatePreview);
    });
</script>
@endsection