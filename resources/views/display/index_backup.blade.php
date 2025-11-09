<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Ruang Tunggu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: white; overflow-x: hidden; }
        .header { background: #16213e; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.5); }
        .header h1 { font-size: 36px; margin-bottom: 10px; }
        .header p { font-size: 16px; color: #aaa; }
        .loket-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; padding: 20px; }
        .loket-card { padding: 30px; border-radius: 10px; text-align: center; transition: all 0.3s; display: flex; flex-direction: column; justify-content: center; }
        .loket-card h2 { font-size: 28px; margin-bottom: 10px; }
        .loket-card p { font-size: 18px; margin-bottom: 10px; }
        .loket-card .nomor { font-size: 72px; font-weight: bold; margin: 20px 0; }
        .loket-card .wait-time { font-size: 24px; margin-top: 15px; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 5px; font-weight: bold; }
        .wait-time-label { font-size: 14px; color: #ccc; }
        .status-aktif { background: #27ae60; }
        .status-tutup { background: #95a5a6; }
        .status-dipanggil { background: #3498db; animation: blink 1s infinite; }
        .status-dilayani { background: #27ae60; }
        .fullscreen-btn { position: fixed; top: 10px; right: 10px; background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; z-index: 1000; }
        .fullscreen-btn:hover { background: #2980b9; }
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.5; }
        }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 80px; color: #34495e; margin-bottom: 20px; }
    </style>
</head>
<body>
    <button class="fullscreen-btn" onclick="document.documentElement.requestFullscreen()">
        <i class="fas fa-expand"></i> Full Screen
    </button>
    
    <div class="header">
        <h1><i class="fas fa-hospital"></i> {{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</h1>
        <p><i class="fas fa-tv"></i> Display Ruang Tunggu - Update Otomatis Setiap 5 Detik</p>
    </div>
    
    <div class="loket-grid" id="loketDisplay">
        <div class="empty-state" style="grid-column: 1 / -1;">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat data loket...</p>
        </div>
    </div>

    <script>
        let lastAudio = null;
        const audioVolume = {{ $audioSetting->volume / 100 }};
        const audioEnabled = {{ $audioSetting->aktif ? 'true' : 'false' }};
        
        function formatWaitTime(minutes) {
            if (minutes <= 0) return "Segera";
            if (minutes < 1) return "< 1 menit";
            if (minutes === 1) return "1 menit";
            if (minutes < 60) return `${Math.round(minutes)} menit`;
            const hours = Math.floor(minutes / 60);
            const mins = Math.round(minutes % 60);
            return `${hours}jam ${mins}menit`;
        }
        
        function loadData() {
            fetch('{{ route("display.data") }}')
                .then(res => res.json())
                .then(data => {
                    const grid = document.getElementById('loketDisplay');
                    
                    if (data.length === 0) {
                        grid.innerHTML = `
                            <div class="empty-state" style="grid-column: 1 / -1;">
                                <i class="fas fa-info-circle"></i>
                                <p>Belum ada loket aktif</p>
                            </div>
                        `;
                        return;
                    }
                    
                    grid.innerHTML = '';
                    data.forEach(loket => {
                        const statusClass = loket.status === 'tutup' ? 'status-tutup' : 
                                          (loket.antrian_dilayani ? 'status-dilayani' : 
                                          (loket.antrian_dipanggil ? 'status-dipanggil' : 'status-aktif'));
                        
                        const nomorAntrian = loket.antrian_dilayani || loket.antrian_dipanggil || '-';
                        
                        const statusText = loket.status === 'tutup' ? 'TUTUP' :
                                         (loket.antrian_dilayani ? 'SEDANG DILAYANI' :
                                         (loket.antrian_dipanggil ? 'SILAKAN MASUK' : 'MENUNGGU'));
                        
                        const icon = loket.status === 'tutup' ? 'fa-door-closed' :
                                   (loket.antrian_dilayani ? 'fa-user-nurse' :
                                   (loket.antrian_dipanggil ? 'fa-phone-volume' : 'fa-clock'));
                        
                        grid.innerHTML += `
                            <div class="loket-card ${statusClass}">
                                <h2><i class="fas fa-door-open"></i> ${loket.nama_loket}</h2>
                                <p>${loket.layanan}</p>
                                <div class="nomor">${nomorAntrian}</div>
                                <p><i class="fas ${icon}"></i> ${statusText}</p>
                                <div class="wait-time">
                                    <div class="wait-time-label"><i class="fas fa-hourglass-half"></i> Estimasi Waktu Tunggu</div>
                                    <div>${formatWaitTime(loket.estimated_wait_minutes)}</div>
                                </div>
                            </div>
                        `;
                        
                        // Play audio jika ada antrian baru dipanggil dan audio enabled
                        if (loket.antrian_dipanggil && loket.audio_url && audioEnabled) {
                            // Hanya play sekali per nomor
                            if (lastAudio !== loket.antrian_dipanggil) {
                                lastAudio = loket.antrian_dipanggil;
                                
                                const audio = new Audio(loket.audio_url);
                                audio.volume = audioVolume;
                                audio.play().catch(err => {
                                    console.log('Audio autoplay blocked. User interaction required.');
                                });
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loketDisplay').innerHTML = `
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Gagal memuat data</p>
                        </div>
                    `;
                });
        }
        
        loadData();
        setInterval(loadData, 5000);
    </script>
</body>
</html>
