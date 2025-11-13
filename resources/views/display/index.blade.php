<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Ruang Coding Antrian - Kiosk Ruang Tunggu</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

<style>
    /* Import Font (Menggunakan Inter agar konsisten) */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    /* Reset dan Global */
    *, *::before, *::after {
        box-sizing: border-box;
    }
    
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        /* Ganti background gelap lama menjadi putih/abu-abu terang dari desain baru */
        background: #f9fafb;
        color: #374151;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Kontainer Utama Kiosk */
    .kiosk-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        width: 100vw;
        padding: 20px;
    }

    /* Header Kiosk */
    .kiosk-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-shrink: 0;
        height: 60px;
    }

    .kiosk-logo {
        font-size: 32px;
        color: #2563eb;
        flex-shrink: 0;
    }

    .kiosk-brand-info {
        line-height: 1.3;
        flex: 1;
        min-width: 0;
    }

    .hospital-name {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hospital-address {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Body Kiosk (Layout 2 Kolom) */
    .kiosk-body {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 20px;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }

    /* Kolom Kiri: Sidebar Layanan */
    .service-sidebar {
        display: flex;
        flex-direction: column;
        gap: 14px;
        overflow-y: auto; /* Agar bisa digulir jika layanan banyak */
        padding-right: 5px; /* Memberi ruang scrollbar */
    }

    .service-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        /* Hapus flex: 1; agar tidak memanjang secara aneh saat overflow */
        display: flex;
        flex-direction: column;
        flex-shrink: 0; /* Agar tidak menyusut */
    }

    .service-header {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .service-icon {
        font-size: 20px;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }

    .service-name {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        line-height: 1.3;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* Warna-warna ikon di sidebar kiri */
    /* Tambahkan .default untuk layanan dari database yang tidak punya warna spesifik */
    .service-icon.default { color: #2563eb; }
    .service-icon.green { color: #10b981; }
    .service-icon.blue { color: #3b82f6; }
    .service-icon.indigo { color: #6366f1; }
    .service-icon.purple { color: #8b5cf6; }

    .service-queue-info {
        margin-top: 12px;
        padding-left: 36px;
        flex-shrink: 0;
    }

    p.next-label {
        margin: 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 500;
    }

    p.next-number {
        margin: 2px 0 0 0;
        font-size: 20px; /* Dibuat lebih besar */
        font-weight: 800; /* Dibuat lebih tebal */
        color: #111827;
        line-height: 1;
    }

    .loket-status {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
        font-size: 12px;
        color: #374151;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-dot.green { background-color: #16a34a; }
    .status-dot.red { background-color: #ef4444; }

    /* Kolom Kanan: Grid Ruangan */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); /* Auto-fit agar adaptif */
        gap: 20px;
        overflow-y: auto;
        align-content: start;
        padding-right: 5px; /* Memberi ruang scrollbar */
    }

    .room-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        min-height: 250px;
        transition: all 0.3s ease-in-out;
    }

    /* Warna untuk status Ruangan */
    .room-card.status-memanggil {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        border-color: #3b82f6;
        animation: pulse-ring 1.5s infinite;
    }
    
    .room-card.status-tersedia {
        border-color: #10b981;
    }
    
    .room-card.status-dilayani {
        border-color: #f59e0b;
    }

    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }


    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 18px 14px 18px;
        flex-shrink: 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .room-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 17px;
        font-weight: 700;
        color: #111827;
    }

    .room-title .fa-solid {
        color: #f59e0b;
        font-size: 18px;
    }

    .status-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 10px;
        flex-shrink: 0;
    }
    
    /* Status Ruangan (Loket) */
    .room-status.open {
        background: #d1fae5; /* green-100 */
        color: #065f46; /* green-800 */
    }
    .room-status.closed {
        background: #fee2e2; /* red-100 */
        color: #991b1b; /* red-800 */
    }
    .room-status.open .status-dot { background: #10b981; }
    .room-status.closed .status-dot { background: #ef4444; }


    .room-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        text-align: center;
        min-height: 120px; /* Minimal tinggi agar konsisten */
    }

    .body-nomor {
        font-size: 96px; /* Ukuran sangat besar */
        font-weight: 900;
        color: #3b82f6; /* Warna biru utama */
        margin: 0;
        line-height: 1.1;
        text-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .room-card.status-memanggil .body-nomor {
        color: #ef4444; /* Merah menyala saat dipanggil */
        animation: flash-text 1s infinite alternate;
    }

    @keyframes flash-text {
        from { opacity: 1; }
        to { opacity: 0.8; }
    }

    .body-status {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin: 10px 0 4px 0;
    }

    .body-subtext {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }
    
    .body-empty {
        font-size: 32px;
        color: #d1d5db;
    }


    .room-footer {
        padding: 14px 18px;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
        border-top: 1px solid #f3f4f6;
    }

    .room-footer .fa-solid {
        font-size: 15px;
        width: 16px;
        text-align: center;
        color: #6b7280;
        flex-shrink: 0;
    }
    
    /* ================================================= */
    /* OVERRIDES & UTILITIES */
    /* ================================================= */
    
    .fullscreen-btn { 
        position: fixed; 
        bottom: 20px; 
        right: 20px; 
        background: #2563eb; /* Warna biru modern */
        color: white; 
        border: none; 
        padding: 10px 18px; 
        border-radius: 12px; /* Lebih modern */
        cursor: pointer; 
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .fullscreen-btn:hover { background: #1d4ed8; }
    
    .status-bar {
        position: fixed; /* Ubah dari absolute ke fixed */
        top: 20px;
        right: 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        background: #ffffff; /* Ganti dari dark ke light */
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        z-index: 100;
        color: #4b5563; /* Text color */
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #9ca3af; /* Abu-abu default */
        animation: none; /* Hapus animasi pulse lama */
    }
    .status-indicator.connected {
        background: #10b981; /* Hijau */
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        animation: pulse-status-new 1.5s infinite;
    }
    
    @keyframes pulse-status-new {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    /* Hilangkan header lama */
    .header { display: none; }
    
    /* Scrollbar Styling (Modern) */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f3f4f6; }
    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }


    /* Responsive untuk layar sangat besar (seperti di desain baru) */
    @media (min-width: 1920px) {
        .kiosk-container { padding: 24px; }
        .kiosk-header { height: 70px; margin-bottom: 24px; }
        .kiosk-logo { font-size: 36px; }
        .hospital-name { font-size: 24px; }
        .hospital-address { font-size: 15px; }
        .kiosk-body { grid-template-columns: 360px 1fr; gap: 24px; }
        .service-card { padding: 20px; }
        .service-name { font-size: 16px; }
        p.next-number { font-size: 24px; }
        .room-title { font-size: 18px; }
        .body-nomor { font-size: 110px; }
        .body-status { font-size: 24px; }
        .body-subtext { font-size: 15px; }
        .room-grid { grid-template-columns: repeat(3, 1fr); }
    }

    /* Untuk layar 4K */
    @media (min-width: 2560px) {
        .kiosk-container { padding: 32px; }
        .kiosk-body { grid-template-columns: 400px 1fr; gap: 28px; }
        .room-grid { grid-template-columns: repeat(4, 1fr); }
    }
    
    /* Responsive untuk tablet/kecil (Ubah ke 1 kolom) */
    @media (max-width: 1024px) {
        .kiosk-body {
            grid-template-columns: 1fr; /* Ubah ke satu kolom */
        }
        .service-sidebar {
            flex-direction: row; /* Ubah sidebar menjadi horizontal */
            overflow-x: auto; /* Bisa digulir secara horizontal */
            overflow-y: hidden;
            padding-bottom: 5px;
            flex-shrink: 0;
        }
        .service-card {
            width: 250px; /* Batasi lebar agar bisa discroll */
            flex-shrink: 0;
        }
        .room-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .kiosk-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    padding: 8px;
    max-height: 90px; /* batas tinggi area logo */
}

.kiosk-logo-img {
    max-height: 80px; /* tinggi maksimum logo */
    width: auto; /* biar proporsional */
    object-fit: contain;
    background: transparent;
    border: none;
}

/* Responsif untuk layar kecil (tablet / hp) */
@media (max-width: 768px) {
    .kiosk-logo {
        max-height: 70px;
    }
    .kiosk-logo-img {
        max-height: 60px;
    }
}

    
</style>
</head>
<body>
    
<div class="kiosk-container">

    <header class="kiosk-header">
<div class="kiosk-logo">
    @if(isset($pengaturan->logo) && $pengaturan->logo)
        <img src="{{ $pengaturan->logo }}" alt="Logo" class="kiosk-logo-img">
    @else
        <i class="fa-solid fa-hospital"></i>
    @endif
</div>

        <div class="kiosk-brand-info">
            <h1 class="hospital-name">{{ $pengaturan->nama_instansi ?? 'Rumah Sakit' }}</h1>
            <p class="hospital-address">{{ $pengaturan->alamat_instansi ?? 'Display Ruang Tunggu Real-time' }}</p>
        </div>
    </header>

    <main class="kiosk-body">

        <aside class="service-sidebar" id="serviceSidebar">
            <!-- Sidebar akan diisi oleh JavaScript dari API -->
        </aside>

        <section class="room-grid" id="loketDisplay">
            <div class="empty-state" style="grid-column: 1 / -1; padding: 100px 20px; background: #ffffff; border: 2px dashed #e5e7eb; border-radius: 16px;">
                <i class="fa-solid fa-tv" style="font-size: 80px; color: #9ca3af; margin-bottom: 20px;"></i>
                <p style="font-size: 18px; color: #9ca3af; font-weight: 600;">Menunggu data panggilan antrian...</p>
            </div>
        </section>

    </main>

</div>

<div class="status-bar">
    <div class="status-indicator" id="statusIndicator"></div>
    <span id="statusText">Memuat Status...</span>
</div>

<button class="fullscreen-btn" onclick="toggleFullscreen()">
    <i class="fas fa-expand-arrows-alt"></i> <span id="fullscreenText">Tampilkan Layar Penuh</span>
</button>

@vite(['resources/js/bootstrap.js'])

<script>
    // ============================================================
    // SUPPRESS BROWSER EXTENSION MESSAGES (Safe to ignore)
    // ============================================================
    const originalError = console.error;
    console.error = function(...args) {
        const message = args[0]?.toString() || '';
        if (message.includes('message channel closed') || 
            message.includes('asynchronous response')) {
            return;
        }
        originalError.apply(console, args);
    };

    // ============================================================
    // AUDIO CONFIGURATION (DARI KODE ASLI)
    // ============================================================
    let lastAudio = null;
    const audioVolume = {{ $audioSetting->volume / 100 ?? 0.5 }};
    const audioEnabled = {{ $audioSetting->aktif === true ? 1 : 0 }} === 1;
    const audioTipe = '{{ $audioSetting->tipe ?? "text-to-speech" }}';
    
    // Language mapping - lebih lengkap untuk semua bahasa
    const audioLanguageMap = {
        'id': 'id-ID', 
        'en': 'en-US', 
        'jv': 'jv-ID', 
        'su': 'su-ID', 
        'ms': 'ms-MY', 
    };
    const audioLanguage = audioLanguageMap['{{ $audioSetting->bahasa ?? "id" }}'] || 'en-US';
    
    const audioFormatPesan = '{{ $audioSetting->format_pesan ?? "Nomor antrian {nomor} silakan menuju ke {lokasi}" }}';
    let displayData = {};
    let echoConnected = false;
    let playedAntrians = new Set(); // Track antrian yang sudah diplay audio
    let hasUserInteraction = false; // Flag untuk user interaction

    // ============================================================
    // USER INTERACTION HANDLER (ENABLE AUDIO PLAYBACK)
    // ============================================================
    document.addEventListener('click', function enableAudio() {
        hasUserInteraction = true;
        console.log('[AUDIO] User interaction detected - audio playback enabled');
    }, { once: true });
    
    document.addEventListener('touchstart', function enableAudio() {
        hasUserInteraction = true;
        console.log('[AUDIO] Touch detected - audio playback enabled');
    }, { once: true });

    // ============================================================
    // FULLSCREEN HANDLER (DARI KODE ASLI)
    // ============================================================
    function toggleFullscreen() {
        const doc = document.documentElement;
        const fullscreenText = document.getElementById('fullscreenText');
        
        if (!document.fullscreenElement) {
            doc.requestFullscreen().catch(err => {
                console.log('[FULLSCREEN] Error:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }

    document.addEventListener('fullscreenchange', function() {
        const fullscreenText = document.getElementById('fullscreenText');
        if (!document.fullscreenElement) {
            fullscreenText.textContent = 'Tampilkan Layar Penuh';
        } else {
            fullscreenText.textContent = 'Keluar Layar Penuh';
        }
    });

    // ============================================================
    // AUDIO PLAYBACK FUNCTIONS (DARI KODE ASLI)
    // ============================================================
    function playNotificationSound() {
        console.log('[AUDIO] Playing notification sound...');
        if (!audioEnabled) return;
        
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const gainNode = audioContext.createGain();
            gainNode.connect(audioContext.destination);
            gainNode.gain.setValueAtTime(audioVolume * 0.3, audioContext.currentTime);
            
            const times = [
                { freq: 600, start: 0, end: 0.15 },
                { freq: 800, start: 0.25, end: 0.4 },
                { freq: 600, start: 0.5, end: 0.65 }
            ];
            
            times.forEach(({ freq, start, end }) => {
                const osc = audioContext.createOscillator();
                osc.frequency.value = freq;
                osc.connect(gainNode);
                osc.start(audioContext.currentTime + start);
                osc.stop(audioContext.currentTime + end);
            });
            
            console.log('[AUDIO] Notification sound played');
        } catch (e) {
            console.error('[AUDIO] Error:', e);
        }
    }

    function tryWebSpeech(kodeAntrian, namaLoket) {
        console.log(`[AUDIO] Trying Web Speech API (${audioLanguage})...`);
        if (!('speechSynthesis' in window)) {
            console.warn('[AUDIO] Web Speech API not available');
            return false;
        }
        
        try {
            if (lastAudio) {
                window.speechSynthesis.cancel();
            }
            
            let msg1 = audioFormatPesan
                .replace('{nomor}', kodeAntrian)
                .replace('{lokasi}', namaLoket);
            
            if (!msg1 || msg1.includes('{')) {
                const splitKode = kodeAntrian.split('').join(' ');
                msg1 = `Nomor antrian ${splitKode}, dimohon menuju ${namaLoket}`;
            }
            
            console.log('[AUDIO] Message:', msg1);
            
            const utterance = new SpeechSynthesisUtterance(msg1);
            utterance.lang = audioLanguage; 
            utterance.rate = 0.8;
            utterance.pitch = 1.0;
            utterance.volume = audioVolume;
            
            let webSpeechFailed = false;
            
            utterance.onstart = () => { console.log(`[AUDIO] ✅ Speaking in ${audioLanguage}...`); };
            utterance.onend = () => { console.log('[AUDIO] ✅ Web Speech completed'); };
            
            utterance.onerror = (e) => {
                console.error(`[AUDIO] ❌ Web Speech error: ${e.error}`);
                webSpeechFailed = true;
                setTimeout(() => {
                    if (webSpeechFailed) {
                        console.log('[AUDIO] Fallback to Google TTS...');
                        playGoogleTTS(kodeAntrian, namaLoket);
                    }
                }, 500);
            };
            
            // Web Speech biasanya tidak perlu user interaction
            try {
                window.speechSynthesis.speak(utterance);
                console.log('[AUDIO] Web Speech announcement started');
            } catch (e) {
                console.error('[AUDIO] Web Speech speak error:', e);
                return false;
            }
            return true;
        } catch (e) {
            console.error('[AUDIO] Web Speech exception:', e);
            return false;
        }
    }

    function playGoogleTTS(kodeAntrian, namaLoket) {
        console.log('[AUDIO] Trying Google Translate TTS...');
        try {
            let message = audioFormatPesan
                .replace('{nomor}', kodeAntrian)
                .replace('{lokasi}', namaLoket);
            
            if (!message || message.includes('{')) {
                const splitKode = kodeAntrian.split('').join(' ');
                message = `Nomor antrian ${splitKode}. Dimohon menuju ${namaLoket}`;
            }
            
            const googleLangMap = {
                'id-ID': 'id', 'en-US': 'en', 'en-GB': 'en', 'jv-ID': 'jv', 'su-ID': 'su', 'ms-MY': 'ms',
            };
            const langCode = googleLangMap[audioLanguage] || 'en';
            
            console.log(`[AUDIO] Google TTS Message: "${message}" (Language: ${audioLanguage} → ${langCode})`);
            
            const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(message)}&tl=${langCode}&client=tw-ob`;
            
            const audio = new Audio(url);
            audio.volume = audioVolume;
            
            audio.onerror = (e) => { console.error('[AUDIO] Google TTS error:', e); };
            
            const playAudio = () => {
                audio.play().catch(e => {
                    console.error('[AUDIO] Google TTS play error:', e);
                    if (!hasUserInteraction) {
                        console.log('[AUDIO] Waiting for user interaction before retry...');
                        setTimeout(() => {
                            if (hasUserInteraction) {
                                audio.play().catch(e2 => console.error('[AUDIO] Retry failed:', e2));
                            }
                        }, 500);
                    }
                });
            };
            
            if (hasUserInteraction) {
                playAudio();
            } else {
                console.log('[AUDIO] Waiting for user interaction to play Google TTS...');
                document.addEventListener('click', playAudio, { once: true });
                document.addEventListener('touchstart', playAudio, { once: true });
            }
            
            console.log('[AUDIO] Google TTS announcement started');
        } catch (e) {
            console.error('[AUDIO] Exception:', e);
        }
    }

    function playCallAudio(kodeAntrian, namaLoket) {
        if (!audioEnabled) {
            console.log('[AUDIO] Audio disabled');
            return;
        }
        
        console.log(`[AUDIO] Playing announcement: ${kodeAntrian} → ${namaLoket}`);
        
        playNotificationSound();
        
        setTimeout(() => {
            const webSpeechWorked = tryWebSpeech(kodeAntrian, namaLoket);
            if (!webSpeechWorked) {
                playGoogleTTS(kodeAntrian, namaLoket);
            }
        }, 800);
    }

    // ============================================================
    // DISPLAY UPDATE (Disesuaikan dengan UI BARU)
    // ============================================================

    const loketDisplay = document.getElementById('loketDisplay');
    const serviceSidebar = document.getElementById('serviceSidebar');

    /**
     * Membuat card loket (Ruang) untuk UI baru
     */
    function createLoketCard(loket) {
        const statusLoket = loket.status === 'aktif' ? 'open' : 'closed';
        
        let antrianInfo = '';
        let roomStatusClass = '';
        let roomBodyContent = '';
        let roomFooterContent = '';

        if (loket.status === 'tutup') {
            roomStatusClass = 'status-tersedia';
            roomBodyContent = `
                <i class="fa-solid fa-lock body-empty"></i>
                <p class="body-status" style="color: #ef4444;">LOKET DITUTUP</p>
                <p class="body-subtext">Loket tidak melayani saat ini</p>
            `;
            roomFooterContent = `
                <i class="fa-solid fa-door-closed"></i>
                <span>Loket Ditutup</span>
            `;
        } else if (loket.antrian) {
            const antrianStatus = loket.antrian.status;
            const kodeAntrian = loket.antrian.kode_antrian;

            if (antrianStatus === 'dipanggil') {
                roomStatusClass = 'status-memanggil';
                roomBodyContent = `
                    <p class="body-nomor">${kodeAntrian}</p>
                    <p class="body-status">SEGERA MENUJU LOKET</p>
                    <p class="body-subtext">Nomor antrian Anda sedang dipanggil</p>
                `;
            } else if (antrianStatus === 'dilayani') {
                roomStatusClass = 'status-dilayani';
                roomBodyContent = `
                    <p class="body-nomor" style="color: #f59e0b;">${kodeAntrian}</p>
                    <p class="body-status">SEDANG DILAYANI</p>
                    <p class="body-subtext">Mohon menunggu giliran berikutnya</p>
                `;
            } else {
                roomStatusClass = 'status-tersedia';
                roomBodyContent = `
                    <i class="fa-regular fa-user-clock body-empty"></i>
                    <p class="body-status">Tersedia</p>
                    <p class="body-subtext">Menunggu antrian berikutnya</p>
                `;
            }
            
            roomFooterContent = `
                <i class="fa-solid fa-user-doctor"></i>
                <span>${loket.layanan}</span>
            `;
            
        } else {
            // Loket buka tapi tidak ada antrian yang sedang dipanggil/dilayani
            roomStatusClass = 'status-tersedia';
            roomBodyContent = `
                <i class="fa-regular fa-user-clock body-empty"></i>
                <p class="body-status">Tersedia</p>
                <p class="body-subtext">Menunggu panggilan antrian</p>
            `;
            roomFooterContent = `
                <i class="fa-solid fa-door-open"></i>
                <span>Loket Siap Melayani ${loket.layanan}</span>
            `;
        }

        return `
            <div class="room-card ${roomStatusClass}" id="roomCard-${loket.id}">
                <div class="room-header">
                    <div class="room-title">
                        <i class="fa-solid fa-door-closed"></i>
                        <span>${loket.nama_loket}</span>
                    </div>
                    <div class="status-badge room-status ${statusLoket}">
                        <span class="status-dot"></span>
                        ${statusLoket === 'open' ? 'Buka' : 'Tutup'}
                    </div>
                </div>
                <div class="room-body">
                    ${roomBodyContent}
                </div>
                <div class="room-footer">
                    ${roomFooterContent}
                </div>
            </div>
        `;
    }
    
    /**
     * Memperbarui informasi sidebar (Antrian Berikutnya)
     */
    function updateSidebarInfo(layananData) {
        // Build sidebar dari data API
        if (!layananData || layananData.length === 0) {
            serviceSidebar.innerHTML = `
                <div class="service-card" style="flex: 1; text-align: center; justify-content: center;">
                    <span class="service-name" style="margin: 0; color: #9ca3af;">Tidak ada layanan yang aktif.</span>
                </div>
            `;
            return;
        }
        
        let html = '';
        layananData.forEach(layanan => {
            const totalLoket = layanan.total_loket || 0;
            const loketAktif = layanan.loket_aktif || 0;
            const nextQueue = layanan.next_queue || 'Tidak ada antrian';
            const statusDotClass = loketAktif > 0 ? 'green' : 'red';
            
            html += `
                <div class="service-card" data-layanan-id="${layanan.id}" data-kode-layanan="${layanan.kode_layanan}">
                    <div class="service-header">
                        <i class="fa-solid fa-bell-concierge service-icon default"></i>
                        <span class="service-name">${layanan.nama_layanan}</span>
                    </div>
                    <div class="service-queue-info">
                        <p class="next-label">Antrian berikutnya</p>
                        <p class="next-number" id="nextQueue-${layanan.kode_layanan}">${nextQueue}</p>
                    </div>
                    <div class="loket-status">
                        <span class="status-dot ${statusDotClass}"></span>
                        <span id="loketStatus-${layanan.kode_layanan}">${loketAktif}/${totalLoket} Loket Aktif</span>
                    </div>
                </div>
            `;
        });
        
        serviceSidebar.innerHTML = html;
    }


    function updateDisplay() {
        fetch('{{ route("display.data") }}')
            .then(response => response.json())
            .then(data => {
                console.log('[LOAD] Display data:', data);
                
                if (data.success === false) {
                    loketDisplay.innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1; padding: 100px 20px; background: #ffffff; border: 2px dashed #ef4444; border-radius: 16px;">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                            <p style="color: #ef4444;">Error: ${data.message || 'Unknown error'}</p>
                        </div>
                    `;
                    return;
                }
                
                // 1. Update Loket Grid (Kanan)
                if (!data.lokets || data.lokets.length === 0) {
                    loketDisplay.innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1; padding: 100px 20px; background: #ffffff; border: 2px dashed #e5e7eb; border-radius: 16px;">
                            <i class="fa-solid fa-tv" style="font-size: 80px; color: #9ca3af; margin-bottom: 20px;"></i>
                            <p style="font-size: 18px; color: #9ca3af; font-weight: 600;">Tidak ada loket yang terdaftar.</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    data.lokets.forEach(loket => {
                        html += createLoketCard(loket);

                        // ⭐ DETECT ANTRIAN BARU YANG DIPANGGIL & TRIGGER AUDIO
                        if (loket.antrian && loket.antrian.status === 'dipanggil') {
                            const antrianKey = `${loket.id}_${loket.antrian.kode_antrian}`;
                            if (!playedAntrians.has(antrianKey)) {
                                playedAntrians.add(antrianKey);
                                console.log(`[POLLING] Detected new antrian dipanggil: ${loket.antrian.kode_antrian} at ${loket.nama_loket}`);
                                playCallAudio(loket.antrian.kode_antrian, loket.nama_loket);
                            }
                        }
                    });
                    loketDisplay.innerHTML = html;
                }
                
                // 2. Update Sidebar (Kiri)
                updateSidebarInfo(data.layanans || []);

            })
            .catch(error => {
                console.error('[LOAD] Error:', error);
                loketDisplay.innerHTML = `
                    <div class="empty-state" style="grid-column: 1 / -1; padding: 100px 20px; background: #ffffff; border: 2px dashed #ef4444; border-radius: 16px;">
                        <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
                        <p style="font-size: 18px; color: #ef4444; font-weight: 600;">Error koneksi: ${error.message}. Periksa koneksi backend Anda.</p>
                    </div>
                `;
            });
    }

    // ============================================================
    // REAL-TIME WebSocket LISTENER (DARI KODE ASLI)
    // ============================================================

    document.addEventListener('DOMContentLoaded', () => {
        console.log('[INIT] Page loaded, initializing listeners...');
        
        updateDisplay();
        
        // Polling fallback (setiap 5 detik)
        setInterval(updateDisplay, 5000);
        
        // Setup Laravel Echo listener
        if (typeof Echo !== 'undefined') {
            console.log('[ECHO] Setting up WebSocket listener...');
            
            Echo.channel('antrian')
                .listen('antrian.dipanggil', (data) => {
                    console.log('[ECHO] Event received:', data);
                    
                    // Langsung play audio real-time
                    playCallAudio(data.kode_antrian, data.nama_loket);
                    
                    // Update display
                    updateDisplay();
                })
                .error((e) => {
                    console.error('[ECHO] Channel error:', e);
                });
            
            // Monitor connection status
            if (Echo.connector && Echo.connector.pusher) {
                Echo.connector.pusher.connection.bind('connected', function() {
                    console.log('[ECHO] ✅ Connected to WebSocket');
                    echoConnected = true;
                    updateConnectionStatus(true);
                });
                
                Echo.connector.pusher.connection.bind('disconnected', function() {
                    console.warn('[ECHO] ⚠️ Disconnected from WebSocket');
                    echoConnected = false;
                    updateConnectionStatus(false);
                });
            }
        } else {
            console.warn('[ECHO] Echo not available, using polling only');
            updateConnectionStatus(false);
        }
    });

    /**
     * Update connection status indicator
     */
    function updateConnectionStatus(connected) {
        const indicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');
        
        if (connected) {
            indicator.classList.add('connected');
            statusText.textContent = 'Terhubung (Real-time)';
        } else {
            indicator.classList.remove('connected');
            statusText.textContent = 'Polling (5 detik)';
        }
    }

    updateConnectionStatus(false); // Initial status update
</script>
</body>
</html>