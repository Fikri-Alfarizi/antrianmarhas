<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Ruang Tunggu - Real-time</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Reset & Dark Theme */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: #0f0f1e; 
            color: white; 
            overflow: hidden;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header { 
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 20px 25px; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border-bottom: 3px solid #3498db;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .header img { max-height: 50px; margin-right: 15px; }
        .header h1 { font-size: 32px; margin: 0; font-weight: 700; }

        /* Tombol Fullscreen & Status */
        .fullscreen-btn, .status-bar {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.3);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 13px;
            z-index: 100;
        }
        .fullscreen-btn {
            left: 20px;
            cursor: pointer;
            border: none;
            color: white;
            transition: 0.2s;
        }
        .fullscreen-btn:hover { background: #3498db; }
        .status-bar {
            right: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e74c3c;
            animation: pulse-status 2s infinite;
        }
        .status-indicator.connected {
            background: #27ae60;
            box-shadow: 0 0 10px rgba(39, 174, 96, 0.6);
        }
        @keyframes pulse-status { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        /* Grid Loket */
        .loket-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
            gap: 20px; 
            padding: 20px; 
            flex-grow: 1; /* Mengisi sisa ruang */
            overflow-y: auto;
        }
        .loket-card { 
            padding: 25px; 
            border-radius: 12px; 
            text-align: center; 
            transition: all 0.4s ease;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
            border-top: 5px solid;
            display: flex; 
            flex-direction: column; 
            justify-content: center;
            min-height: 250px;
        }
        .loket-card h2 { font-size: 26px; margin-bottom: 5px; font-weight: 700; }
        .loket-card p { font-size: 16px; margin-bottom: 15px; opacity: 0.8; }
        .loket-card .nomor { 
            font-size: 80px; 
            font-weight: 900; 
            margin: 15px 0; 
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        /* Status Warna Card */
        .loket-card.status-aktif { /* Aktif tapi kosong */
            background: #2c3e50; 
            border-top-color: #7f8c8d;
        }
        .loket-card.status-tutup { 
            background: #57606f; 
            border-top-color: #e74c3c;
        }
        .loket-card.status-tutup .nomor {
            font-size: 50px;
            color: #bdc3c7;
        }
        .loket-card.status-dipanggil { 
            background: #3498db; 
            border-top-color: #2980b9;
            animation: pulse-call 1.2s infinite;
        }
        .loket-card.status-dilayani { 
            background: #27ae60; 
            border-top-color: #1e8449; 
        }
        
        @keyframes pulse-call {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 25px rgba(0,0,0,0.4); }
            50% { transform: scale(1.02); box-shadow: 0 8px 35px rgba(52, 152, 219, 0.6); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: #3498db; border-radius: 4px; }
        
        @media (max-width: 768px) {
            .header h1 { font-size: 20px; }
            .header img { max-height: 30px; }
            .loket-grid { grid-template-columns: 1fr; gap: 15px; padding: 15px; }
            .loket-card .nomor { font-size: 64px; }
            .fullscreen-btn { display: none; } /* Sembunyikan di HP */
            .status-bar { position: static; transform: none; margin-top: -10px; margin-bottom: 10px; justify-content: center; }
        }
    </style>
</head>
<body>

    <button class="fullscreen-btn" onclick="toggleFullscreen()">
        <i class="fas fa-expand"></i> <span id="fullscreenText">Full Screen</span>
    </button>

    <div class="status-bar">
        <div class="status-indicator" id="statusIndicator"></div>
        <span id="statusText">Menghubungkan...</span>
    </div>
    
    <div class="header">
        @if($pengaturan && $pengaturan->logo)
            <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo">
        @endif
        <h1>{{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</h1>
    </div>
    
    <div class="loket-grid" id="loket-grid">
        </div>

    @vite(['resources/js/bootstrap.js'])

    <script>
        // ============================================================
        // PENGATURAN & VARIABEL
        // ============================================================
        const URL_GET_DATA = "{{ route('display.data') }}";
        const REFRESH_INTERVAL_MS = {{ $audioSetting->display_refresh_seconds ?? 5 }} * 1000;
        
        // Pengaturan audio dari Controller
        const audioSettings = {
            enabled: {{ $audioSetting->aktif ? 'true' : 'false' }},
            volume: {{ $audioSetting->volume ?? 80 }} / 100,
            lang: '{{ $audioSetting->bahasa ?? "id" }}',
            format: '{{ $audioSetting->format_pesan ?? "Nomor {nomor} silakan menuju ke {lokasi}" }}'
        };

        // Elemen DOM
        const loketGrid = document.getElementById('loket-grid');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');

        // ============================================================
        // FUNGSI UTAMA DISPLAY
        // ============================================================

        /**
         * Mengambil data terbaru dari server (Polling Fallback)
         */
        async function fetchDisplayData() {
            try {
                const response = await fetch(URL_GET_DATA);
                if (!response.ok) throw new Error('Gagal mengambil data');
                const data = await response.json();
                
                if (data.success) {
                    renderDisplay(data.lokets);
                } else {
                    console.warn('Gagal memuat data:', data.message);
                }
            } catch (error) {
                console.error('Error polling data:', error);
                updateConnectionStatus(false); // Tandai koneksi gagal jika fetch error
            }
        }

        /**
         * Merender kartu loket ke dalam grid
         */
        function renderDisplay(lokets) {
            if (!loketGrid) return;
            
            let html = '';
            if (lokets.length === 0) {
                html = '<p style="font-size: 18px; text-align: center; grid-column: 1 / -1;">Belum ada loket yang dikonfigurasi.</p>';
            }

            lokets.forEach(loket => {
                let kodeAntrian = '---';
                let statusText = 'TERSEDIA';
                let statusClass = 'status-aktif'; // Default (Aktif, tapi idle)

                if (loket.status === 'tutup') {
                    statusClass = 'status-tutup';
                    statusText = 'LOKET TUTUP';
                    kodeAntrian = '<i class="fas fa-lock"></i>';
                } else if (loket.antrian) {
                    kodeAntrian = loket.antrian.kode_antrian;
                    if (loket.antrian.status === 'dipanggil') {
                        statusClass = 'status-dipanggil';
                        statusText = 'DIPANGGIL';
                    } else if (loket.antrian.status === 'dilayani') {
                        statusClass = 'status-dilayani';
                        statusText = 'SEDANG DILAYANI';
                    }
                }

                html += `
                    <div class="loket-card ${statusClass}" id="loket-${loket.id}">
                        <h2>${loket.nama_loket}</h2>
                        <p>${loket.layanan}</p>
                        <div class="nomor">${kodeAntrian}</div>
                        <p style="font-weight: 600; margin-top: 10px;">${statusText}</p>
                    </div>
                `;
            });
            loketGrid.innerHTML = html;
        }

        /**
         * Update Indikator Status Koneksi
         */
        function updateConnectionStatus(isConnected) {
            if (isConnected) {
                statusIndicator.classList.add('connected');
                statusText.textContent = 'Terhubung (Real-time)';
            } else {
                statusIndicator.classList.remove('connected');
                statusText.textContent = `Polling (${REFRESH_INTERVAL_MS / 1000}d)`;
            }
        }

        /**
         * Fullscreen Toggle
         */
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error: ${err.message}`);
                });
                document.getElementById('fullscreenText').textContent = 'Keluar';
            } else {
                document.exitFullscreen();
                document.getElementById('fullscreenText').textContent = 'Full Screen';
            }
        }
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                document.getElementById('fullscreenText').textContent = 'Full Screen';
            }
        });


        // ============================================================
        // LOGIKA AUDIO (Diambil dari kode Anda sebelumnya)
        // ============================================================

        /**
         * Memainkan beep notifikasi (3x tone)
         */
        function playNotificationSound() {
            if (!audioSettings.enabled) return;
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const gainNode = audioContext.createGain();
                gainNode.connect(audioContext.destination);
                gainNode.gain.setValueAtTime(audioSettings.volume * 0.3, audioContext.currentTime);
                
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
            } catch (e) {
                console.error('[AUDIO] Error:', e);
            }
        }

        /**
         * Prioritas: Web Speech API (Browser built-in TTS)
         */
        function tryWebSpeech(text, lang) {
            if (!('speechSynthesis' in window)) {
                console.warn('[AUDIO] Web Speech API not available');
                return false;
            }
            try {
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                }
                
                const utterance = new SpeechSynthesisUtterance(text);
                const langMap = {'id': 'id-ID', 'en': 'en-US', 'jv': 'jv-ID', 'su': 'su-ID', 'ms': 'ms-MY'};
                utterance.lang = langMap[lang] || 'id-ID';
                utterance.rate = 0.8;
                utterance.pitch = 1.0;
                utterance.volume = audioSettings.volume;
                
                utterance.onerror = (e) => { 
                    console.error('[AUDIO] Web Speech Error:', e.error, '-> Fallback ke Google TTS');
                    playGoogleTTS(text, lang); // Fallback otomatis
                };
                
                window.speechSynthesis.speak(utterance);
                return true;
            } catch (e) {
                console.error('[AUDIO] Web Speech exception:', e);
                return false;
            }
        }

        /**
         * Fallback: Google Translate TTS
         */
        function playGoogleTTS(text, lang) {
            try {
                const langCode = lang || 'id';
                const url = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text)}&tl=${langCode}&client=tw-ob`;
                const audio = new Audio(url);
                audio.volume = audioSettings.volume;
                audio.play().catch(e => console.error('[AUDIO] Google TTS play error:', e));
            } catch (e) {
                console.error('[AUDIO] Google TTS exception:', e);
            }
        }
        
        /**
         * Fungsi Panggilan Audio Utama
         */
        function playCallAudio(kodeAntrian, namaLoket) {
            if (!audioSettings.enabled) {
                console.log('[AUDIO] Audio dinonaktifkan.');
                return;
            }
            
            console.log(`[AUDIO] Memainkan panggilan: ${kodeAntrian} -> ${namaLoket}`);
            
            // Format pesan dari settings
            let message = audioSettings.format
                .replace('{nomor}', kodeAntrian)
                .replace('{lokasi}', namaLoket);

            playNotificationSound();
            
            setTimeout(() => {
                const webSpeechWorked = tryWebSpeech(message, audioSettings.lang);
                if (!webSpeechWorked) {
                    playGoogleTTS(message, audioSettings.lang);
                }
            }, 800); // Jeda setelah beep
        }

        // ============================================================
        // INISIALISASI HALAMAN
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('[DISPLAY] Halaman dimuat.');
            
            // 1. Ambil data awal
            fetchDisplayData();
            
            // 2. Set Polling Fallback
            setInterval(fetchDisplayData, REFRESH_INTERVAL_MS);
            
            // 3. Setup Listener Real-time (Echo)
            if (typeof Echo !== 'undefined') {
                console.log('[ECHO] Menghubungkan ke channel: antrian-channel');
                
                Echo.channel('antrian-channel')
                    // Event saat petugas memanggil
                    .listen('.antrian.dipanggil', (e) => {
                        console.log('[ECHO] Event: antrian.dipanggil', e);
                        
                        // Mainkan suara HANYA jika statusnya 'dipanggil'
                        if (e.status === 'dipanggil') {
                            playCallAudio(e.kode_antrian, e.nama_loket);
                        }
                        
                        // Selalu refresh tampilan
                        fetchDisplayData();
                    })
                    // Event saat petugas buka/tutup loket
                    .listen('.loket.status.updated', (e) => {
                        console.log('[ECHO] Event: loket.status.updated', e);
                        fetchDisplayData();
                    })
                    .error((e) => {
                        console.error('[ECHO] Channel error:', e);
                        updateConnectionStatus(false);
                    });

                // Monitor koneksi Echo
                if (Echo.connector && Echo.connector.pusher) {
                    Echo.connector.pusher.connection.bind('connected', () => {
                        console.log('[ECHO] Terhubung (Real-time Aktif).');
                        updateConnectionStatus(true);
                    });
                    Echo.connector.pusher.connection.bind('disconnected', () => {
                        console.warn('[ECHO] Terputus.');
                        updateConnectionStatus(false);
                    });
                }
            } else {
                console.warn('[ECHO] Echo (Pusher) tidak ditemukan. Halaman ini HANYA akan menggunakan polling.');
                updateConnectionStatus(false);
            }
        });
        
    </script>
</body>
</html>