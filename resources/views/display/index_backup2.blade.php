<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Ruang Tunggu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: #0f0f1e; 
            color: white; 
            overflow: hidden;
            height: 100vh;
        }
        .header { 
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 25px; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            border-bottom: 3px solid #3498db;
        }
        .header h1 { font-size: 36px; margin-bottom: 8px; font-weight: 700; }
        .header p { font-size: 14px; color: #aaa; }
        .fullscreen-btn { 
            position: fixed; 
            top: 20px; 
            right: 20px; 
            background: #3498db; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 8px; 
            cursor: pointer; 
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .fullscreen-btn:hover { background: #2980b9; }
        .loket-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); 
            gap: 25px; 
            padding: 25px; 
            height: calc(100vh - 140px);
            overflow-y: auto;
        }
        .loket-card { 
            padding: 35px; 
            border-radius: 12px; 
            text-align: center; 
            transition: all 0.4s ease;
            display: flex; 
            flex-direction: column; 
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
            border-top: 5px solid;
            min-height: 300px;
        }
        .loket-card h2 { font-size: 28px; margin-bottom: 8px; font-weight: 700; }
        .loket-card p { font-size: 16px; margin-bottom: 8px; opacity: 0.9; }
        .loket-card .nomor { 
            font-size: 96px; 
            font-weight: 900; 
            margin: 25px 0; 
            letter-spacing: 5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .loket-card .layanan { font-size: 18px; margin: 12px 0; font-weight: 600; }
        .loket-card .wait-time { 
            font-size: 18px; 
            margin-top: 15px; 
            padding: 12px; 
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            font-weight: 600;
            border-left: 4px solid rgba(255,255,255,0.3);
        }
        .wait-time-label { font-size: 12px; color: #ccc; }
        .status-aktif { background: #27ae60; border-top-color: #1e8449; }
        .status-tutup { background: #95a5a6; border-top-color: #7f8c8d; }
        .status-dipanggil { 
            background: #3498db; 
            border-top-color: #2980b9;
            animation: pulse 1s infinite;
        }
        .status-dilayani { background: #27ae60; border-top-color: #1e8449; }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 8px 25px rgba(0,0,0,0.4); }
            50% { box-shadow: 0 8px 35px rgba(52, 152, 219, 0.6); }
        }
        .empty-state { 
            grid-column: 1 / -1;
            text-align: center; 
            padding: 80px 20px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 12px;
            border: 2px dashed #3498db;
        }
        .empty-state i { font-size: 80px; color: #3498db; margin-bottom: 20px; opacity: 0.7; }
        .empty-state p { font-size: 18px; color: #ccc; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: #3498db; border-radius: 4px; }
        @media (max-width: 1024px) {
            .loket-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 15px; }
            .loket-card { padding: 25px; min-height: 250px; }
            .loket-card .nomor { font-size: 72px; }
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 24px; }
            .loket-grid { grid-template-columns: 1fr; gap: 15px; }
            .loket-card { padding: 20px; min-height: 200px; }
            .loket-card .nomor { font-size: 64px; }
            .fullscreen-btn { padding: 10px 15px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <button class="fullscreen-btn" onclick="toggleFullscreen()">
        <i class="fas fa-expand"></i> <span id="fullscreenText">Full Screen</span>
    </button>
    
    <div class="header">
        <h1><i class="fas fa-hospital"></i> {{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</h1>
        <p><i class="fas fa-tv"></i> Display Ruang Tunggu - Update Real-time</p>
    </div>
    
    <div class="loket-grid" id="loketDisplay">
        <div class="empty-state">
            <i class="fas fa-spinner" style="animation: spin 2s linear infinite;"></i>
            <p>Memuat data loket...</p>
        </div>
    </div>

    <script>
        @yield('scripts')
        
        let lastAudio = null;
        const audioVolume = {{ $audioSetting->volume / 100 ?? 0.5 }};
        const audioEnabled = {{ $audioSetting->aktif ? 'true' : 'false' }};
        let displayData = {};

        function formatWaitTime(minutes) {
            if (minutes <= 0) return "Segera Dipanggil";
            if (minutes < 1) return "Kurang dari 1 menit";
            if (minutes === 1) return "1 menit";
            return minutes + " menit";
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
                document.getElementById('fullscreenText').textContent = 'Exit Fullscreen';
            } else {
                document.exitFullscreen();
                document.getElementById('fullscreenText').textContent = 'Full Screen';
            }
        }

        function playNotificationSound() {
            console.log('[AUDIO] Playing notification sound...');
            if (!audioEnabled) return;
            
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                gainNode.gain.setValueAtTime(audioVolume * 0.3, audioContext.currentTime);
                
                // 3x beep: 600Hz → 800Hz → 600Hz
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime);
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.2);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.3);
                oscillator.start(audioContext.currentTime + 0.3);
                oscillator.stop(audioContext.currentTime + 0.5);
                
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.6);
                oscillator.start(audioContext.currentTime + 0.6);
                oscillator.stop(audioContext.currentTime + 0.9);
                
                console.log('[AUDIO] Notification sound played');
            } catch (e) {
                console.error('[AUDIO] Error playing notification:', e);
            }
        }

        function tryWebSpeech(kodeAntrian, namaLoket) {
            console.log('[AUDIO] Trying Web Speech API...');
            if (!('speechSynthesis' in window)) {
                console.warn('[AUDIO] Web Speech API not available');
                return false;
            }
            
            try {
                if (lastAudio) {
                    window.speechSynthesis.cancel();
                }
                
                // Split kode untuk clarity: A001 → A 0 0 1
                const parts = kodeAntrian.split('');
                const splitKode = parts.join(' ');
                
                const msg1 = `Nomor antrian ${splitKode}`;
                const msg2 = `Dimohon menuju ${namaLoket}`;
                
                const utterance1 = new SpeechSynthesisUtterance(msg1);
                utterance1.lang = 'id-ID';
                utterance1.rate = 0.8;
                utterance1.pitch = 1.0;
                utterance1.volume = audioVolume;
                
                utterance1.onend = () => {
                    console.log('[AUDIO] First message done, playing second...');
                    setTimeout(() => {
                        const utterance2 = new SpeechSynthesisUtterance(msg2);
                        utterance2.lang = 'id-ID';
                        utterance2.rate = 0.8;
                        utterance2.pitch = 0.95;
                        utterance2.volume = audioVolume;
                        window.speechSynthesis.speak(utterance2);
                    }, 300);
                };
                
                window.speechSynthesis.speak(utterance1);
                console.log('[AUDIO] Web Speech announcement started');
                return true;
            } catch (e) {
                console.error('[AUDIO] Web Speech error:', e);
                return false;
            }
        }

        function playGoogleTTS(kodeAntrian, namaLoket) {
            console.log('[AUDIO] Trying Google Translate TTS...');
            try {
                // Split kode untuk clarity
                const parts = kodeAntrian.split('');
                const splitKode = parts.join(' ');
                
                const text1 = `Nomor antrian ${splitKode}`;
                const text2 = `Dimohon menuju ${namaLoket}`;
                
                const url1 = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text1)}&tl=id&client=tw-ob`;
                
                const audio1 = new Audio(url1);
                audio1.volume = audioVolume;
                
                audio1.onended = () => {
                    console.log('[AUDIO] First audio done, playing second...');
                    setTimeout(() => {
                        const url2 = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text2)}&tl=id&client=tw-ob`;
                        const audio2 = new Audio(url2);
                        audio2.volume = audioVolume;
                        audio2.play().catch(e => {
                            console.error('[AUDIO] Error playing second audio:', e);
                        });
                    }, 300);
                };
                
                audio1.onerror = (e) => {
                    console.error('[AUDIO] Google TTS error:', e);
                };
                
                audio1.play().catch(e => {
                    console.error('[AUDIO] Error playing first audio:', e);
                });
                
                console.log('[AUDIO] Google TTS announcement started');
            } catch (e) {
                console.error('[AUDIO] Google TTS exception:', e);
            }
        }

        function playCallAudio(kodeAntrian, namaLoket) {
            if (!audioEnabled) {
                console.log('[AUDIO] Audio disabled');
                return;
            }
            
            console.log(`[AUDIO] Playing announcement for ${kodeAntrian} → ${namaLoket}`);
            
            // Play beep first
            playNotificationSound();
            
            // Wait for beep to finish, then play announcement
            setTimeout(() => {
                const webSpeechWorked = tryWebSpeech(kodeAntrian, namaLoket);
                if (!webSpeechWorked) {
                    // Fallback to Google TTS
                    playGoogleTTS(kodeAntrian, namaLoket);
                }
            }, 800);
        }

        function updateDisplay() {
            fetch('{{ route("display.data") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('[LOAD] Display data received:', data);
                    
                    if (data.success === false) {
                        console.error('[LOAD] API error:', data.message);
                        const loketDisplay = document.getElementById('loketDisplay');
                        loketDisplay.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading data: ${data.message || 'Unknown error'}</p>
                            </div>
                        `;
                        return;
                    }
                    
                    const loketDisplay = document.getElementById('loketDisplay');
                    
                    if (!data.lokets || data.lokets.length === 0) {
                        loketDisplay.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada loket yang tersedia</p>
                            </div>
                        `;
                        return;
                    }
                    
                    let html = '';
                    data.lokets.forEach(loket => {
                        const statusClass = loket.status === 'tutup' ? 'status-tutup' : 
                                          loket.antrian && loket.antrian.status === 'dipanggil' ? 'status-dipanggil' :
                                          loket.antrian && loket.antrian.status === 'dilayani' ? 'status-dilayani' : 'status-aktif';
                        
                        const kodeAntrian = loket.antrian ? loket.antrian.kode_antrian : '-';
                        const antrianStatus = loket.antrian ? loket.antrian.status : null;
                        
                        // Trigger audio jika ada perubahan
                        if (loket.antrian && displayData[loket.id]?.kode !== kodeAntrian) {
                            playCallAudio(kodeAntrian, loket.nama_loket);
                            displayData[loket.id] = { kode: kodeAntrian };
                        }
                        
                        html += `
                            <div class="loket-card ${statusClass}">
                                <h2>${loket.nama_loket}</h2>
                                <p>${loket.layanan}</p>
                                <div class="nomor">${kodeAntrian}</div>
                                ${loket.antrian ? `
                                    <div class="layanan">
                                        <i class="fas fa-${antrianStatus === 'dipanggil' ? 'bell' : 'check'}"></i>
                                        ${antrianStatus === 'dipanggil' ? 'SEGERA DIPANGGIL' : 'SEDANG DILAYANI'}
                                    </div>
                                ` : ''}
                                ${loket.status === 'tutup' ? '<p style="margin-top: 20px; color: #fff;"><i class="fas fa-lock"></i> LOKET DITUTUP</p>' : ''}
                            </div>
                        `;
                    });
                    
                    loketDisplay.innerHTML = html || '<div class="empty-state"><i class="fas fa-inbox"></i><p>Tidak ada antrian</p></div>';
                })
                .catch(error => {
                    console.error('[LOAD] Fetch error:', error);
                    const loketDisplay = document.getElementById('loketDisplay');
                    loketDisplay.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Error koneksi: ${error.message}</p>
                        </div>
                    `;
                });
        }

        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                document.getElementById('fullscreenText').textContent = 'Full Screen';
            }
        });

        updateDisplay();
        setInterval(updateDisplay, 5000);

        // ============================================================
        // REAL-TIME: Laravel Echo untuk WebSocket Broadcasting
        // ============================================================
        
        // Setup Laravel Echo untuk mendengarkan event real-time
        // Ini akan dipanggil langsung saat operator klik PANGGIL
        if (typeof Echo !== 'undefined') {
            console.log('[ECHO] Initializing Laravel Echo listener...');
            
            Echo.channel('antrian')
                .listen('antrian.dipanggil', (data) => {
                    console.log('[ECHO] Event received:', data);
                    
                    // Langsung play audio tanpa tunggu polling
                    playCallAudio(data.kode_antrian, data.nama_loket);
                    
                    // Update display
                    updateDisplay();
                    
                    console.log('[ECHO] Audio played for:', data.kode_antrian, 'at', data.nama_loket);
                })
                .error((e) => {
                    console.error('[ECHO] Channel error:', e);
                });
            
            console.log('[ECHO] Listener attached to "antrian" channel');
        } else {
            console.warn('[ECHO] Laravel Echo not loaded, falling back to polling only');
        }
    </script>

    <!-- Laravel Echo + Pusher untuk Real-time Broadcasting -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.14.2/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.1.0/dist/web/pusher.min.js"></script>
    <script>
        // Inisialisasi Echo dengan Pusher (Production)
        const echoConfig = {
            broadcaster: 'pusher',
            key: 'd05c41532b18dfd78059',
            cluster: 'ap1',
            encrypted: true,
        };

        console.log('[ECHO] Config:', echoConfig);

        window.Echo = new Echo(echoConfig);

        window.Echo.connector.pusher.connection.bind('error', function(err) {
            if (err.error && err.error.type === 'PushError') {
                console.error('[ECHO] Connection error:', err);
            }
        });

        window.Echo.connector.pusher.connection.bind('connected', function() {
            console.log('[ECHO] ✅ Connected to Pusher WebSocket');
        });

        window.Echo.connector.pusher.connection.bind('disconnected', function() {
            console.warn('[ECHO] ⚠️ Disconnected from Pusher WebSocket');
        });
    </script>
</body>
</html>
