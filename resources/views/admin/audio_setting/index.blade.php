@extends('layouts.app')

@section('title', 'Pengaturan Audio')

@section('content')
<div class="settings-container">
    <!-- Header -->
    <div class="settings-header">
        <div>
            <h1><i class="fas fa-volume-up"></i> Pengaturan Audio Announcement</h1>
            <p>Konfigurasi pengumuman antrian dan suara sistem</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h4>Terjadi kesalahan!</h4>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="settings-grid">
        <!-- Main Settings Card -->
        <div class="settings-card">
            <div class="card-header">
                <h2><i class="fas fa-sliders-h"></i> Pengaturan Umum</h2>
            </div>

            <form action="{{ route('admin.audio_setting.update') }}" method="POST" class="settings-form">
                @csrf

                <!-- Tipe Audio -->
                <div class="form-group">
                    <label for="tipe">
                        <i class="fas fa-microphone"></i> Tipe Audio
                        <span class="badge-info">Wajib</span>
                    </label>
                    <select id="tipe" name="tipe" class="form-control" onchange="updateTipeInfo()">
                        @foreach ($types as $key => $value)
                            <option value="{{ $key }}" @selected($audioSetting->tipe === $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Pilih antara pengolahan teks real-time atau file audio yang telah direkam sebelumnya.
                    </small>
                </div>

                <!-- Info Tipe -->
                <div class="info-box" id="tipe-info">
                    <i class="fas fa-info-circle"></i>
                    <span id="tipe-info-text">Sistem akan mengkonversi teks menjadi suara secara otomatis menggunakan Text-to-Speech.</span>
                </div>

                <!-- Bahasa -->
                <div class="form-group">
                    <label for="bahasa">
                        <i class="fas fa-globe"></i> Bahasa
                        <span class="badge-info">Wajib</span>
                    </label>
                    <select id="bahasa" name="bahasa" class="form-control">
                        @foreach ($languages as $key => $value)
                            <option value="{{ $key }}" @selected($audioSetting->bahasa === $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Bahasa yang digunakan untuk Text-to-Speech.
                    </small>
                </div>

                <!-- Volume -->
                <div class="form-group">
                    <label for="volume">
                        <i class="fas fa-volume-up"></i> Volume
                        <span class="badge-info">Wajib</span>
                    </label>
                    <div class="volume-slider-container">
                        <input type="range" id="volume" name="volume" min="0" max="100" 
                               value="{{ $audioSetting->volume }}" class="volume-slider" 
                               oninput="updateVolumeDisplay(this.value)">
                        <span class="volume-value" id="volume-value">{{ $audioSetting->volume }}%</span>
                    </div>
                    <small class="form-text text-muted">
                        Sesuaikan tingkat volume pengumuman (0-100%).
                    </small>
                </div>

                <!-- Status Aktif -->
                <div class="form-group">
                    <label for="aktif" class="checkbox-label">
                        <input type="hidden" name="aktif" value="0">
                        <input type="checkbox" id="aktif" name="aktif" class="form-checkbox" value="1" @checked($audioSetting->aktif)>
                        <span><i class="fas fa-check-circle"></i> Audio Announcement Diaktifkan</span>
                    </label>
                    <small class="form-text text-muted">
                        Jika diaktifkan, sistem akan memutarkan pengumuman antrian secara otomatis.
                    </small>
                </div>

                <!-- Format Pesan -->
                <div class="form-group">
                    <label for="format_pesan">
                        <i class="fas fa-message"></i> Format Pesan
                        <span class="badge-info">Wajib</span>
                    </label>
                    <textarea id="format_pesan" name="format_pesan" rows="3" class="form-control" 
                              placeholder="Contoh: Nomor antrian {nomor} silakan menuju ke {lokasi}">{{ $audioSetting->format_pesan }}</textarea>
                    <small class="form-text text-muted">
                        Gunakan <code>{nomor}</code> untuk nomor antrian dan <code>{lokasi}</code> untuk lokasi loket.
                        <br><strong>Contoh:</strong> "Nomor antrian {nomor} silakan menuju ke {lokasi} di SMK Marhas Margahayu"
                    </small>
                </div>

                <!-- Voice URL (untuk audio-file) -->
                <div class="form-group" id="voice-url-group" style="display: none;">
                    <label for="voice_url">
                        <i class="fas fa-link"></i> URL File Audio (Opsional)
                    </label>
                    <input type="url" id="voice_url" name="voice_url" class="form-control" 
                           value="{{ $audioSetting->voice_url }}" 
                           placeholder="https://example.com/audio.mp3">
                    <small class="form-text text-muted">
                        URL file audio custom jika menggunakan tipe "File Audio Custom".
                    </small>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="testAudioAnnouncement()">
                        <i class="fas fa-play-circle"></i> Test Audio
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="card-header">
                <h2><i class="fas fa-lightbulb"></i> Informasi</h2>
            </div>

            <div class="info-content">
                <h4>Cara Menggunakan Format Pesan</h4>
                <ul>
                    <li>
                        <strong>{nomor}</strong> - Akan digantikan dengan nomor antrian
                        <br><em>Contoh: "Nomor 042" jika kode antrian adalah "042"</em>
                    </li>
                    <li>
                        <strong>{lokasi}</strong> - Akan digantikan dengan nama loket
                        <br><em>Contoh: "Loket Pendaftaran" jika nama loket adalah "Pendaftaran"</em>
                    </li>
                </ul>

                <hr>

                <h4>Status Saat Ini</h4>
                <div class="status-display">
                    <div class="status-item">
                        <strong>Audio:</strong>
                        <span class="badge @if($audioSetting->aktif) badge-success @else badge-danger @endif">
                            @if($audioSetting->aktif) <i class="fas fa-check"></i> Aktif @else <i class="fas fa-times"></i> Nonaktif @endif
                        </span>
                    </div>
                    <div class="status-item">
                        <strong>Bahasa:</strong>
                        <span class="badge badge-info">{{ $languages[$audioSetting->bahasa] ?? $audioSetting->bahasa }}</span>
                    </div>
                    <div class="status-item">
                        <strong>Tipe:</strong>
                        <span class="badge badge-info">{{ $types[$audioSetting->tipe] ?? $audioSetting->tipe }}</span>
                    </div>
                    <div class="status-item">
                        <strong>Volume:</strong>
                        <span class="badge badge-info">{{ $audioSetting->volume }}%</span>
                    </div>
                </div>

                <hr>

                <h4>Tips</h4>
                <ul>
                    <li>Pastikan volume tidak terlalu rendah agar dapat didengar dengan jelas</li>
                    <li>Uji pengaturan menggunakan tombol "Test Audio" sebelum menyimpan</li>
                    <li>Perubahan akan langsung berlaku di semua layar yang terbuka</li>
                    <li>Format pesan sebaiknya singkat dan jelas untuk pengalaman terbaik</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Test Audio Modal -->
    <div id="test-audio-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-volume-up"></i> Test Pengumuman Audio</h2>
                <button class="modal-close" onclick="closeTestAudioModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="test-nomor">
                        <i class="fas fa-hashtag"></i> Nomor Antrian
                    </label>
                    <input type="text" id="test-nomor" class="form-control" placeholder="Contoh: 042" value="042">
                </div>
                <div class="form-group">
                    <label for="test-lokasi">
                        <i class="fas fa-map-pin"></i> Lokasi/Loket
                    </label>
                    <input type="text" id="test-lokasi" class="form-control" placeholder="Contoh: Loket 1" value="Loket 1">
                </div>
                <div id="test-preview" class="test-preview" style="display: none;">
                    <strong>Pesan yang akan diputar:</strong>
                    <p id="test-preview-text"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeTestAudioModal()">
                    <i class="fas fa-times"></i> Tutup
                </button>
                <button class="btn btn-primary" onclick="playTestAudio()">
                    <i class="fas fa-play-circle"></i> Putar Test Audio
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .settings-header {
        margin-bottom: 30px;
    }

    .settings-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--text-primary);
    }

    .settings-header p {
        color: var(--text-secondary);
        font-size: 15px;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-top: 30px;
    }

    .settings-card, .info-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .card-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-secondary);
    }

    .card-header h2 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: var(--text-primary);
    }

    .settings-form {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge-info {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-primary);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-left: auto;
    }

    .form-control {
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-control textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-text {
        font-size: 13px;
        color: var(--text-secondary);
    }

    .form-text code {
        background: var(--bg-primary);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
        color: var(--accent-primary);
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        user-select: none;
    }

    .form-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--accent-primary);
    }

    .volume-slider-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .volume-slider {
        flex: 1;
        height: 6px;
        border-radius: 3px;
        background: var(--border-color);
        outline: none;
        -webkit-appearance: none;
        appearance: none;
    }

    .volume-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--accent-primary);
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        transition: all 0.2s ease;
    }

    .volume-slider::-webkit-slider-thumb:hover {
        width: 20px;
        height: 20px;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .volume-slider::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: var(--accent-primary);
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        transition: all 0.2s ease;
    }

    .volume-slider::-moz-range-thumb:hover {
        width: 20px;
        height: 20px;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .volume-value {
        min-width: 50px;
        text-align: right;
        font-weight: 600;
        color: var(--accent-primary);
        font-size: 14px;
    }

    .info-box {
        background: rgba(59, 130, 246, 0.08);
        border-left: 4px solid var(--accent-primary);
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 14px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-box i {
        color: var(--accent-primary);
        flex-shrink: 0;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 10px;
    }

    .btn {
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-primary), #2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary {
        background: var(--border-color);
        color: var(--text-primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary:hover {
        background: rgba(226, 232, 240, 0.8);
        transform: translateY(-2px);
    }

    .info-card {
        display: flex;
        flex-direction: column;
    }

    .info-content {
        padding: 20px;
        flex: 1;
        overflow-y: auto;
    }

    .info-content h4 {
        font-size: 14px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 10px;
        color: var(--text-primary);
    }

    .info-content ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-content li {
        padding: 8px 0;
        font-size: 13px;
        line-height: 1.6;
        color: var(--text-secondary);
    }

    .info-content li strong {
        color: var(--text-primary);
    }

    .info-content li em {
        display: block;
        font-style: normal;
        margin-top: 3px;
        font-size: 12px;
        color: var(--text-muted);
        padding-left: 15px;
    }

    .info-content hr {
        border: none;
        border-top: 1px solid var(--border-color);
        margin: 15px 0;
    }

    .status-display {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 13px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: var(--accent-success);
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: var(--accent-danger);
    }

    .badge-info {
        background: rgba(59, 130, 246, 0.2);
        color: var(--accent-primary);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border-left: 4px solid var(--accent-success);
        color: var(--accent-success);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border-left: 4px solid var(--accent-danger);
        color: var(--accent-danger);
    }

    .alert ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .alert li {
        padding: 3px 0;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: var(--text-primary);
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .test-preview {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px;
    }

    .test-preview strong {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        color: var(--text-primary);
    }

    .test-preview p {
        margin: 0;
        font-size: 14px;
        color: var(--text-secondary);
        font-style: italic;
        line-height: 1.5;
    }

    .modal-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }

    /* Dark mode support */
    :root {
        --bg-primary: #f9fafb;
        --bg-secondary: #ffffff;
        --bg-card: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border-color: #e2e8f0;
        --accent-primary: #3b82f6;
        --accent-success: #10b981;
        --accent-danger: #ef4444;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    [data-theme="dark"] {
        --bg-primary: #0f172a;
        --bg-secondary: #1e293b;
        --bg-card: #1e293b;
        --text-primary: #f1f5f9;
        --text-secondary: #cbd5e1;
        --text-muted: #64748b;
        --border-color: #334155;
        --accent-primary: #60a5fa;
        --accent-success: #34d399;
        --accent-danger: #f87171;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
    }
</style>

<script>
    // Update tipe info
    function updateTipeInfo() {
        const tipe = document.getElementById('tipe').value;
        const tipeInfo = {
            'text-to-speech': 'Sistem akan mengkonversi teks menjadi suara secara otomatis menggunakan Text-to-Speech.',
            'audio-file': 'Sistem akan memutar file audio yang sudah direkam sebelumnya. URL file audio harus tersedia.',
        };
        
        document.getElementById('tipe-info-text').textContent = tipeInfo[tipe] || '';
        
        // Show/hide voice_url input based on tipe
        const voiceUrlGroup = document.getElementById('voice-url-group');
        if (tipe === 'audio-file') {
            voiceUrlGroup.style.display = 'block';
        } else {
            voiceUrlGroup.style.display = 'none';
        }
    }

    // Update volume display
    function updateVolumeDisplay(value) {
        document.getElementById('volume-value').textContent = value + '%';
    }

    // Test audio
    function testAudioAnnouncement() {
        const modal = document.getElementById('test-audio-modal');
        modal.style.display = 'flex';
    }

    function closeTestAudioModal() {
        const modal = document.getElementById('test-audio-modal');
        modal.style.display = 'none';
    }

    async function playTestAudio() {
        const nomor = document.getElementById('test-nomor').value;
        const lokasi = document.getElementById('test-lokasi').value;

        if (!nomor || !lokasi) {
            alert('Mohon isi nomor antrian dan lokasi');
            return;
        }

        try {
            const response = await fetch('{{ route("admin.audio_setting.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nomor, lokasi })
            });

            const data = await response.json();

            if (data.success) {
                // Show preview
                document.getElementById('test-preview-text').textContent = data.pesan;
                document.getElementById('test-preview').style.display = 'block';

                // Play audio using Web Speech API
                if ('speechSynthesis' in window) {
                    const utterance = new SpeechSynthesisUtterance(data.pesan);
                    utterance.lang = data.bahasa === 'id' ? 'id-ID' : data.bahasa;
                    utterance.rate = 0.9;
                    utterance.pitch = 1;
                    utterance.volume = data.volume / 100;
                    
                    window.speechSynthesis.cancel(); // Cancel any ongoing speech
                    window.speechSynthesis.speak(utterance);
                } else {
                    alert('Browser Anda tidak mendukung fitur suara');
                }
            } else {
                alert('Gagal membuat test audio: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membuat test audio');
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', () => {
        updateTipeInfo();

        // Close modal when clicking outside
        document.getElementById('test-audio-modal').addEventListener('click', (e) => {
            if (e.target.id === 'test-audio-modal') {
                closeTestAudioModal();
            }
        });
    });
</script>
@endsection
