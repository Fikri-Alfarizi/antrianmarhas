<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Antrian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .container h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; text-transform: uppercase; }
        .btn { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { background: #2980b9; }
        .result { margin-top: 20px; padding: 20px; background: #ecf0f1; border-radius: 5px; display: none; }
        .result h3 { color: #2c3e50; margin-bottom: 15px; text-align: center; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #bdc3c7; }
        .info-row strong { color: #555; }
        .badge { padding: 5px 10px; border-radius: 3px; color: white; font-weight: bold; }
        .badge-warning { background: #f39c12; }
        .badge-info { background: #3498db; }
        .badge-success { background: #27ae60; }
        .badge-danger { background: #e74c3c; }
        .error-state { text-align: center; color: #e74c3c; padding: 20px; }
        .error-state i { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-search"></i> Cek Status Antrian</h2>
        
        <form id="formCek" onsubmit="cekStatus(event)">
            <div class="form-group">
                <label for="kode"><i class="fas fa-ticket-alt"></i> Kode Antrian</label>
                <input type="text" id="kode" placeholder="Contoh: A001" required>
                <small style="color: #666;">Masukkan kode antrian dari struk Anda</small>
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-search"></i> Cek Status
            </button>
        </form>
        
        <div id="result" class="result"></div>
    </div>

    <script>
        function cekStatus(e) {
            e.preventDefault();
            const kode = document.getElementById('kode').value.toUpperCase();
            const resultDiv = document.getElementById('result');
            
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Mencari...</p>';
            
            fetch(`{{ route("status.cek") }}?kode=${kode}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const antrian = data.antrian;
                        const badgeClass = 
                            antrian.status === 'menunggu' ? 'badge-warning' :
                            antrian.status === 'dipanggil' ? 'badge-info' :
                            antrian.status === 'dilayani' ? 'badge-info' :
                            antrian.status === 'selesai' ? 'badge-success' : 'badge-danger';
                        
                        const statusIcon = 
                            antrian.status === 'menunggu' ? 'fa-clock' :
                            antrian.status === 'dipanggil' ? 'fa-phone' :
                            antrian.status === 'dilayani' ? 'fa-user-nurse' :
                            antrian.status === 'selesai' ? 'fa-check-circle' : 'fa-times-circle';
                        
                        resultDiv.innerHTML = `
                            <h3><i class="fas fa-info-circle"></i> Informasi Antrian</h3>
                            <div class="info-row">
                                <strong><i class="fas fa-ticket-alt"></i> Kode Antrian:</strong>
                                <span style="font-size: 18px; font-weight: bold;">${antrian.kode_antrian}</span>
                            </div>
                            <div class="info-row">
                                <strong><i class="fas fa-notes-medical"></i> Layanan:</strong>
                                <span>${antrian.layanan}</span>
                            </div>
                            <div class="info-row">
                                <strong><i class="fas fa-door-open"></i> Loket:</strong>
                                <span>${antrian.loket || '-'}</span>
                            </div>
                            <div class="info-row">
                                <strong><i class="fas ${statusIcon}"></i> Status:</strong>
                                <span class="badge ${badgeClass}">${antrian.status.toUpperCase()}</span>
                            </div>
                            <div class="info-row">
                                <strong><i class="fas fa-clock"></i> Waktu Ambil:</strong>
                                <span>${antrian.waktu_ambil}</span>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <div class="error-state">
                                <i class="fas fa-exclamation-circle"></i>
                                <p><strong>${data.message}</strong></p>
                                <small>Pastikan kode antrian yang Anda masukkan benar</small>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><strong>Terjadi kesalahan</strong></p>
                            <small>Silakan coba lagi</small>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>
