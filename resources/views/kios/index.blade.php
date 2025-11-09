<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Antrian - {{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; width: 100%; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .kios-wrapper { width: 100%; max-width: 1000px; }
        .kios-header { 
            text-align: center; 
            margin-bottom: 40px; 
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .kios-header-logo { 
            height: 80px; 
            margin-bottom: 15px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
        .kios-header-logo img { max-height: 100%; max-width: 200px; }
        .kios-header h1 { 
            font-size: 32px; 
            font-weight: 700; 
            color: #2c3e50; 
            margin-bottom: 8px;
        }
        .kios-header p { 
            font-size: 14px; 
            color: #666;
            margin: 0;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .service-button {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 30px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            font-weight: 600;
            min-height: 200px;
        }
        .service-button:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        .service-button:active {
            transform: translateY(-4px) scale(0.98);
        }
        .service-button i { 
            font-size: 48px; 
            margin-bottom: 15px;
            color: #3498db;
        }
        .service-label { 
            text-align: center;
            line-height: 1.3;
        }
        .kios-footer {
            text-align: center;
            color: white;
            margin-top: 20px;
            font-size: 12px;
        }
        .loading { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; }
        .loading-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 40px; border-radius: 12px; text-align: center; }
        .loading-content i { font-size: 48px; color: #3498db; animation: spin 1s linear infinite; margin-bottom: 15px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 768px) {
            .kios-header { margin-bottom: 30px; padding: 20px; }
            .kios-header h1 { font-size: 24px; }
            .services-grid { gap: 15px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
            .service-button { padding: 20px 15px; min-height: 160px; }
            .service-button i { font-size: 40px; }
            .service-button { font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="kios-wrapper">
        <div class="kios-header">
            <div class="kios-header-logo">
                @if($pengaturan->logo)
                <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo">
                @else
                <i class="fas fa-hospital" style="font-size: 60px; color: #3498db;"></i>
                @endif
            </div>
            <h1>{{ $pengaturan->nama_instansi ?? 'Sistem Antrian' }}</h1>
            <p><i class="fas fa-mouse"></i> Silakan sentuh untuk mengambil nomor antrian</p>
        </div>

        <div class="services-grid">
            @forelse($layanans as $layanan)
            <button class="service-button" onclick="takeQueueNumber({{ $layanan->id }}, '{{ $layanan->nama_layanan }}')">
                <i class="fas fa-plus-circle"></i>
                <div class="service-label">{{ $layanan->nama_layanan }}</div>
            </button>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; background: white; padding: 40px; border-radius: 12px;">
                <i class="fas fa-info-circle" style="font-size: 40px; color: #f39c12; margin-bottom: 15px;"></i>
                <p style="color: #666;">Belum ada layanan yang tersedia. Silakan hubungi admin.</p>
            </div>
            @endforelse
        </div>

        <div class="kios-footer">
            <p><i class="fas fa-clock"></i> Jam Operasional: Setiap Hari | <i class="fas fa-phone"></i> {{ $pengaturan->telepon ?? '-' }}</p>
        </div>
    </div>

    <div id="loading" class="loading">
        <div class="loading-content">
            <i class="fas fa-spinner"></i>
            <p style="color: #333; margin-top: 15px;">Memproses...</p>
        </div>
    </div>

    <script>
        let printAttempts = 0;
        const maxRetries = 3;

        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        function takeQueueNumber(layananId, layananNama) {
            showLoading();
            
            fetch('{{ route("kios.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    layanan_id: layananId
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showSuccessMessage(data.antrian, layananNama);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        }

        function showSuccessMessage(antrian, layananNama) {
            const messageDiv = document.createElement('div');
            messageDiv.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 10000; text-align: center; min-width: 350px; animation: slideUp 0.3s ease;';
            messageDiv.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 60px; color: #27ae60; margin-bottom: 15px; display: block;"></i>
                <h2 style="color: #2c3e50; margin: 15px 0; font-size: 24px;">Nomor Antrian Anda</h2>
                <p style="font-size: 80px; font-weight: bold; color: #3498db; margin: 30px 0; letter-spacing: 10px; font-family: 'Courier New', monospace;">${antrian.kode_antrian}</p>
                <p style="color: #666; margin: 15px 0; font-size: 16px;"><strong>Layanan:</strong> ${layananNama}</p>
                <p style="color: #999; font-size: 13px; margin: 10px 0;">Harap catat atau screenshot nomor ini</p>
                <button onclick="this.parentElement.remove();" style="background: #3498db; color: white; border: none; padding: 12px 40px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-top: 20px; font-weight: 600;">
                    <i class="fas fa-check"></i> OK, Ambil Antrian Lagi
                </button>
            `;
            document.body.appendChild(messageDiv);
            
            // Auto close setelah 5 detik
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 5000);
        }

        // Add CSS animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideUp {
                from {
                    transform: translate(-50%, -60%);
                    opacity: 0;
                }
                to {
                    transform: translate(-50%, -50%);
                    opacity: 1;
                }
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
