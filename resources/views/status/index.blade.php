<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo.png') }}">
    <title>Lacak Status Antrian</title>
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
        .status-container { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
            max-width: 500px;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }
        .status-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .status-header h1 { font-size: 24px; margin-bottom: 8px; font-weight: 700; }
        .status-header p { font-size: 13px; opacity: 0.9; }
        .status-body { padding: 30px; }
        .status-number { 
            text-align: center; 
            padding: 25px; 
            background: #f8f9fa; 
            border-radius: 10px; 
            margin-bottom: 20px;
        }
        .status-number .label { 
            font-size: 12px; 
            color: #999; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .status-number .number { 
            font-size: 48px; 
            font-weight: 900; 
            color: #3498db; 
            letter-spacing: 3px;
        }
        .status-info { margin: 20px 0; }
        .info-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 0; 
            border-bottom: 1px solid #ecf0f1;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { 
            color: #999; 
            font-size: 13px; 
            font-weight: 500;
        }
        .info-value { 
            color: #2c3e50; 
            font-weight: 600;
            text-align: right;
        }
        .status-badge { 
            display: inline-block; 
            padding: 8px 16px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600;
            margin-top: 10px;
        }
        .badge-menunggu { 
            background: #e3f2fd; 
            color: #0d47a1;
        }
        .badge-dipanggil { 
            background: #fff3e0; 
            color: #e65100;
        }
        .badge-dilayani { 
            background: #e8f5e9; 
            color: #1b5e20;
        }
        .badge-selesai { 
            background: #d4edda; 
            color: #155724;
        }
        .badge-batal { 
            background: #f8d7da; 
            color: #721c24;
        }
        .timeline { margin: 20px 0; }
        .timeline-item { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 15px;
            position: relative;
        }
        .timeline-marker { 
            min-width: 32px; 
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            background: #3498db; 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 14px;
            position: relative;
            z-index: 2;
        }
        .timeline-item.completed .timeline-marker { 
            background: #27ae60; 
        }
        .timeline-item.current .timeline-marker { 
            background: #f39c12;
            animation: pulse 2s infinite;
        }
        .timeline-connector { 
            position: absolute; 
            left: 15px; 
            top: 32px; 
            bottom: -15px; 
            width: 2px; 
            background: #ecf0f1;
        }
        .timeline-item.completed .timeline-connector { 
            background: #27ae60; 
        }
        .timeline-item:last-child .timeline-connector { 
            display: none; 
        }
        .timeline-content { 
            flex: 1; 
            padding-top: 6px;
        }
        .timeline-title { 
            font-weight: 600; 
            color: #2c3e50; 
            font-size: 14px;
        }
        .timeline-time { 
            font-size: 12px; 
            color: #999; 
            margin-top: 4px;
        }
        .empty-state { 
            text-align: center; 
            padding: 40px 20px; 
            color: #999;
        }
        .empty-state i { 
            font-size: 48px; 
            color: #ddd; 
            margin-bottom: 15px;
            display: block;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(243, 156, 18, 0); }
        }
        @media (max-width: 768px) {
            .status-container { border-radius: 10px; }
            .status-header { padding: 20px; }
            .status-header h1 { font-size: 20px; }
            .status-number .number { font-size: 40px; }
            .status-body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="status-container">
        <div class="status-header">
            <h1><img src="{{ asset('img/logo.png') }}" alt="Logo Antrian Marhas" style="height:32px;vertical-align:middle;margin-right:8px;"> Antrian Marhas</h1>
            <p>Lacak Status Antrian Anda</p>
        </div>

        <div class="status-body" id="statusContent">
            <div class="empty-state">
                <i class="fas fa-spinner" style="animation: spin 2s linear infinite;"></i>
                <p>Memuat data antrian...</p>
            </div>
        </div>
    </div>

    <script>
        function loadStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const antrianId = urlParams.get('id');
            
            if (!antrianId) {
                document.getElementById('statusContent').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-qrcode"></i>
                        <p>Silakan scan QR code di struk antrian Anda</p>
                    </div>
                `;
                return;
            }
            
            fetch(`{{ route('status.show') }}?id=${antrianId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.antrian) {
                        const antrian = data.antrian;
                        const statusClass = `badge-${antrian.status}`;
                        const statusText = {
                            'menunggu': 'Sedang Menunggu',
                            'dipanggil': 'Segera Dipanggil',
                            'dilayani': 'Sedang Dilayani',
                            'selesai': 'Selesai',
                            'batal': 'Dibatalkan'
                        }[antrian.status] || antrian.status;

                        let timelineHtml = '';
                        
                        const items = [
                            { status: 'menunggu', label: 'Antrian Dibuat', time: antrian.waktu_ambil, icon: 'fa-plus-circle' },
                            { status: 'dipanggil', label: 'Dipanggil', time: antrian.waktu_panggil, icon: 'fa-bell' },
                            { status: 'dilayani', label: 'Sedang Dilayani', time: antrian.waktu_dilayani, icon: 'fa-user-md' },
                            { status: 'selesai', label: 'Selesai', time: antrian.waktu_selesai, icon: 'fa-check-circle' }
                        ];

                        items.forEach(item => {
                            const isCompleted = ['selesai', 'dilayani', 'dipanggil', 'menunggu'].indexOf(item.status) <= ['selesai', 'dilayani', 'dipanggil', 'menunggu'].indexOf(antrian.status);
                            const isCurrent = item.status === antrian.status;
                            
                            timelineHtml += `
                                <div class="timeline-item ${isCompleted ? 'completed' : ''} ${isCurrent ? 'current' : ''}">
                                    <div class="timeline-connector"></div>
                                    <div class="timeline-marker">
                                        <i class="fas ${item.icon}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">${item.label}</div>
                                        ${item.time ? `<div class="timeline-time">${new Date(item.time).toLocaleString('id-ID')}</div>` : ''}
                                    </div>
                                </div>
                            `;
                        });

                        document.getElementById('statusContent').innerHTML = `
                            <div class="status-number">
                                <div class="label">Nomor Antrian Anda</div>
                                <div class="number">${antrian.kode_antrian}</div>
                                <span class="status-badge ${statusClass}">
                                    <i class="fas fa-info-circle"></i> ${statusText}
                                </span>
                            </div>

                            <div class="status-info">
                                <div class="info-row">
                                    <span class="info-label"><i class="fas fa-stethoscope"></i> Layanan</span>
                                    <span class="info-value">${antrian.layanan.nama_layanan}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="fas fa-door-open"></i> Loket</span>
                                    <span class="info-value">${antrian.loket ? antrian.loket.nama_loket : 'Belum ditentukan'}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"><i class="fas fa-clock"></i> Waktu Ambil</span>
                                    <span class="info-value">${new Date(antrian.waktu_ambil).toLocaleTimeString('id-ID')}</span>
                                </div>
                            </div>

                            <div class="timeline">
                                ${timelineHtml}
                            </div>

                            ${antrian.status !== 'selesai' && antrian.status !== 'batal' ? `
                                <div style="background: #e3f2fd; border-radius: 8px; padding: 12px; margin-top: 20px; font-size: 12px; color: #0d47a1; text-align: center;">
                                    <i class="fas fa-info-circle"></i> Halaman akan diperbarui setiap 5 detik
                                </div>
                            ` : ''}
                        `;
                    } else {
                        document.getElementById('statusContent').innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i>
                                <p>Antrian tidak ditemukan</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('statusContent').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-wifi-off" style="color: #e74c3c;"></i>
                            <p>Gagal memuat data. Silakan cek koneksi Anda.</p>
                        </div>
                    `;
                });
        }

        loadStatus();
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('id')) {
            setInterval(loadStatus, 5000);
        }
    </script>
</body>
</html>
