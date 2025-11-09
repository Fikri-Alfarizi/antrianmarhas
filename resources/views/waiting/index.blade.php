<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Antrian - {{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .waiting-container { 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
            max-width: 600px;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }
        .waiting-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .waiting-header h1 { font-size: 24px; margin-bottom: 8px; font-weight: 700; }
        .waiting-header p { font-size: 13px; opacity: 0.9; margin: 0; }
        .waiting-body { padding: 40px 30px; }
        .antrian-number { 
            text-align: center; 
            margin-bottom: 40px;
        }
        .antrian-label { 
            font-size: 14px; 
            color: #999; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .antrian-number-display { 
            font-size: 80px; 
            font-weight: 900; 
            color: #3498db; 
            letter-spacing: 8px;
            margin: 20px 0;
            animation: pulse 2s infinite;
        }
        .antrian-service { 
            font-size: 16px; 
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .antrian-time { 
            font-size: 12px; 
            color: #999;
            margin-bottom: 30px;
        }
        .status-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin-bottom: 20px;
        }
        .status-title { 
            font-size: 14px; 
            font-weight: 600; 
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-content { 
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 15px 0;
        }
        .status-item { 
            text-align: center;
        }
        .status-item i { 
            font-size: 32px; 
            margin-bottom: 8px;
            color: #3498db;
        }
        .status-item p { 
            font-size: 12px; 
            color: #666;
            margin: 0;
        }
        .status-item.active i { 
            color: #f39c12;
            animation: bounce 1s infinite;
        }
        .status-item.completed i { 
            color: #27ae60;
        }
        .current-calling { 
            background: #fff3e0; 
            border: 2px solid #f39c12; 
            border-radius: 10px; 
            padding: 20px; 
            text-align: center;
            margin-bottom: 20px;
        }
        .current-calling h3 { 
            font-size: 13px; 
            color: #e65100;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .current-calling-number { 
            font-size: 48px; 
            font-weight: bold; 
            color: #f39c12;
        }
        .position-info { 
            background: #e3f2fd; 
            border-left: 4px solid #3498db; 
            padding: 15px; 
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .position-info p { 
            margin: 8px 0; 
            font-size: 14px;
            color: #1565c0;
        }
        .position-info strong { 
            color: #0d47a1;
            font-weight: 700;
        }
        .counter-section { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px;
            margin-bottom: 20px;
        }
        .counter-box { 
            background: #f0f2f5; 
            padding: 15px; 
            border-radius: 8px; 
            text-align: center;
        }
        .counter-box .number { 
            font-size: 28px; 
            font-weight: bold; 
            color: #3498db;
        }
        .counter-box .label { 
            font-size: 12px; 
            color: #666;
            margin-top: 5px;
        }
        .action-buttons { 
            display: flex; 
            gap: 10px;
            margin-top: 20px;
        }
        .btn { 
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary { 
            background: #3498db; 
            color: white;
        }
        .btn-primary:hover { 
            background: #2980b9;
        }
        .btn-secondary { 
            background: #ecf0f1; 
            color: #666;
        }
        .btn-secondary:hover { 
            background: #d5dbdb;
        }
        .loading { 
            text-align: center; 
            padding: 30px;
        }
        .loading i { 
            font-size: 40px; 
            color: #3498db; 
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
            display: block;
        }
        .loading p { 
            color: #666; 
            font-size: 14px;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .waiting-header { padding: 25px; }
            .waiting-body { padding: 25px 20px; }
            .antrian-number-display { font-size: 60px; letter-spacing: 5px; }
            .counter-section { gap: 10px; }
            .action-buttons { flex-direction: column; }
            .btn { padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="waiting-container">
        <div class="waiting-header">
            <h1><i class="fas fa-hourglass-half"></i> Menunggu Antrian</h1>
            <p>{{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</p>
        </div>

        <div class="waiting-body" id="waitingContent">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Memuat data antrian Anda...</p>
            </div>
        </div>
    </div>

    <script>
        const antrianId = new URLSearchParams(window.location.search).get('id');
        let currentCallingNumber = null;
        let updateInterval = null;

        async function loadAntrianData() {
            if (!antrianId) {
                document.getElementById('waitingContent').innerHTML = `
                    <div style="text-align: center; padding: 40px 30px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #e74c3c; margin-bottom: 15px; display: block;"></i>
                        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Data antrian tidak ditemukan</p>
                        <button onclick="window.location.href='{{ route('kios.index') }}'" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Kios
                        </button>
                    </div>
                `;
                return;
            }

            try {
                const response = await fetch(`{{ route('antrian.status') }}?id=${antrianId}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message);
                }

                const antrian = data.antrian;
                const currentCalling = data.current_calling;
                const antrianDisebelum = data.antrian_disebelum;
                const pengaturan = data.pengaturan;

                currentCallingNumber = currentCalling?.kode_antrian;

                let html = `
                    <div class="antrian-number">
                        <div class="antrian-label">Nomor Antrian Anda</div>
                        <div class="antrian-number-display">${antrian.kode_antrian}</div>
                        <div class="antrian-service">${antrian.layanan.nama_layanan}</div>
                        <div class="antrian-time">
                            <i class="fas fa-clock"></i> ${new Date(antrian.waktu_ambil).toLocaleTimeString('id-ID')}
                        </div>
                    </div>
                `;

                if (currentCalling) {
                    html += `
                        <div class="current-calling">
                            <h3><i class="fas fa-bullhorn"></i> SEDANG DIPANGGIL</h3>
                            <div class="current-calling-number">${currentCalling.kode_antrian}</div>
                            <p style="font-size: 12px; color: #e65100; margin-top: 8px;">
                                ${currentCalling.loket?.nama_loket || 'Loket tidak ditentukan'}
                            </p>
                        </div>
                    `;
                }

                if (antrianDisebelum > 0) {
                    const estimasiMenit = antrianDisebelum * 5;
                    html += `
                        <div class="position-info">
                            <p><strong>Posisi Anda:</strong> #${antrianDisebelum + 1}</p>
                            <p><strong>Antrian Sebelumnya:</strong> ${antrianDisebelum} antrian</p>
                            <p><strong>Estimasi Waktu:</strong> ~${estimasiMenit} menit</p>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="position-info" style="background: #e8f5e9; border-left-color: #27ae60;">
                            <p style="color: #1b5e20;"><i class="fas fa-check-circle"></i> <strong>Anda Berikutnya!</strong></p>
                            <p style="color: #1b5e20;">Siap-siaplah untuk dipanggil</p>
                        </div>
                    `;
                }

                html += `
                    <div class="counter-section">
                        <div class="counter-box">
                            <div class="number">${data.selesai || 0}</div>
                            <div class="label"><i class="fas fa-check"></i> Sudah Selesai</div>
                        </div>
                        <div class="counter-box">
                            <div class="number">${data.menunggu || 0}</div>
                            <div class="label"><i class="fas fa-clock"></i> Menunggu</div>
                        </div>
                    </div>

                    <div class="status-section">
                        <div class="status-title">
                            <i class="fas fa-list"></i> Status Antrian
                        </div>
                        <div class="status-content">
                            <div class="status-item completed">
                                <i class="fas fa-check-circle"></i>
                                <p>Dibuat</p>
                            </div>
                            <div class="status-item ${antrian.status !== 'menunggu' ? 'completed' : 'active'}">
                                <i class="fas fa-bell"></i>
                                <p>Dipanggil</p>
                            </div>
                            <div class="status-item ${antrian.status === 'dilayani' ? 'active' : antrian.status === 'selesai' ? 'completed' : ''}">
                                <i class="fas fa-user-md"></i>
                                <p>Dilayani</p>
                            </div>
                            <div class="status-item ${antrian.status === 'selesai' ? 'completed' : ''}">
                                <i class="fas fa-check"></i>
                                <p>Selesai</p>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button class="btn btn-secondary" onclick="window.location.href='{{ route('kios.index') }}'">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </button>
                    </div>
                `;

                document.getElementById('waitingContent').innerHTML = html;

                // Auto play notification jika status changed
                if (antrian.status === 'dipanggil' && !sessionStorage.getItem('notified_' + antrianId)) {
                    playNotification(`Nomor ${antrian.kode_antrian} sedang dipanggil`);
                    sessionStorage.setItem('notified_' + antrianId, 'true');
                }

            } catch (error) {
                console.error('Error:', error);
                document.getElementById('waitingContent').innerHTML = `
                    <div class="loading" style="color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>${error.message || 'Gagal memuat data antrian'}</p>
                    </div>
                `;
            }
        }

        function playNotification(text) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            }
        }

        // Initial load
        loadAntrianData();

        // Auto refresh setiap 3 detik
        updateInterval = setInterval(loadAntrianData, 3000);

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (updateInterval) clearInterval(updateInterval);
        });
    </script>
</body>
</html>
