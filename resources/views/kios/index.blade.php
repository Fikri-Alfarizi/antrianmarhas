<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kios Antrian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        /* Body (Layar Penuh) */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Container Utama */
        .kios-container {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Header Kios */
        .kios-header {
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        .kios-header img {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .kios-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
        }
        .kios-header p {
            font-size: 20px;
            opacity: 0.9;
            margin-top: 5px;
            /* ID ini akan kita ubah dengan JS */
            id: "kios-subtitle"; 
        }

        /* Grid Tombol Layanan */
        .layanan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px;
        }
        
        /* Tombol Layanan */
        .btn-layanan {
            background: white;
            color: #3498db;
            border: 2px solid #3498db;
            padding: 30px 25px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 150px;
        }
        .btn-layanan:hover {
            background: #3498db;
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        .btn-layanan:disabled {
            background: #ecf0f1;
            color: #bdc3c7;
            border-color: #ecf0f1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-layanan i {
            font-size: 40px;
            margin-bottom: 15px;
        }

        /* --- HALAMAN NOMOR ANDA (BARU) --- */
        .receipt-screen {
            padding: 50px 30px;
            text-align: center;
        }
        .receipt-screen p {
            font-size: 22px;
            color: #7f8c8d;
        }
        #receipt-kode-display {
            font-size: 100px;
            font-weight: 900;
            color: #3498db;
            margin: 20px 0;
        }
        #receipt-layanan-display {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 30px;
        }
        .btn-kembali {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 20px;
        }
        .btn-kembali:hover {
            background: #34495e;
        }
        /* --- AKHIR HALAMAN NOMOR ANDA --- */

        /* Loading Spinner (Tersembunyi) */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9998;
            justify-content: center;
            align-items: center;
        }
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* HAPUS SEMUA CSS @media print */
        
    </style>
</head>
<body>

    <div class="kios-container">
        <div class="kios-header">
            @if($pengaturan && $pengaturan->logo)
                <img src="{{ asset('storage/' . $pengaturan->logo) }}" alt="Logo">
            @endif
            <h1>{{ $pengaturan->nama_instansi ?? 'Selamat Datang' }}</h1>
            <p id="kios-subtitle">Silakan pilih layanan Anda</p>
        </div>

        <div class="layanan-grid" id="layanan-grid">
            @if($layanans->isEmpty())
                <p style="text-align: center; grid-column: 1 / -1; color: #95a5a6; font-size: 18px;">
                    <i class="fas fa-info-circle"></i> Saat ini tidak ada layanan yang tersedia.
                </p>
            @else
                @foreach($layanans as $layanan)
                <button class="btn-layanan" onclick="handleCetak({{ $layanan->id }})">
                    <i class="fas fa-concierge-bell"></i>
                    <span>{{ $layanan->nama_layanan }}</span>
                </button>
                @endforeach
            @endif
        </div>
        
        <div class="receipt-screen" id="receipt-screen" style="display: none;">
            <p>Nomor Antrian Anda</p>
            <h2 id="receipt-kode-display">A001</h2>
            <span id="receipt-layanan-display">Pemeriksaan Umum</span>
            <p style="margin-top: 20px;">Silakan tunggu nomor Anda dipanggil.</p>
            
            <button class="btn-kembali" onclick="showLayananScreen()">
                <i class="fas fa-arrow-left"></i> Kembali ke Awal
            </button>
        </div>

    </div>

    <div class="loading-overlay" id="loading-overlay">
        <div class="spinner"></div>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const URL_CETAK = "{{ route('kios.cetak') }}";
        
        // Elemen Layar
        const loadingOverlay = document.getElementById('loading-overlay');
        const layananGrid = document.getElementById('layanan-grid');
        const receiptScreen = document.getElementById('receipt-screen');
        const kiosSubtitle = document.getElementById('kios-subtitle');

        /**
         * Mengatur status loading (menonaktifkan tombol)
         */
        function setLoading(isLoading) {
            const buttons = layananGrid.getElementsByTagName('button');
            if (isLoading) {
                loadingOverlay.style.display = 'flex';
                for (let btn of buttons) {
                    btn.disabled = true;
                }
            } else {
                loadingOverlay.style.display = 'none';
                for (let btn of buttons) {
                    btn.disabled = false;
                }
            }
        }

        /**
         * Fungsi utama untuk mengambil nomor antrian
         */
        async function handleCetak(layananId) {
            setLoading(true);
            
            try {
                const response = await fetch(URL_CETAK, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        layanan_id: layananId
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal mengambil nomor antrian.');
                }

                if (data.success) {
                    // --- PERUBAHAN LOGIKA ---
                    
                    // 1. Isi data ke Layar 2
                    document.getElementById('receipt-kode-display').textContent = data.antrian.kode_antrian;
                    document.getElementById('receipt-layanan-display').textContent = data.antrian.nama_layanan;
                    
                    // 2. Ganti Tampilan Layar
                    layananGrid.style.display = 'none';
                    receiptScreen.style.display = 'block';
                    kiosSubtitle.textContent = 'Antrian Anda Berhasil Diambil';
                    
                    // 3. HAPUS PANGGILAN PRINT
                    // window.print(); // <-- Dihapus
                    
                } else {
                    alert('Error: ' + data.message);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                setLoading(false);
            }
        }
        
        /**
         * Fungsi untuk tombol Kembali
         * Menyembunyikan layar nomor dan menampilkan layar layanan
         */
        function showLayananScreen() {
            receiptScreen.style.display = 'none';
            layananGrid.style.display = 'grid'; // Kembalikan ke 'grid'
            kiosSubtitle.textContent = 'Silakan pilih layanan Anda';
        }
        
        // HAPUS FUNGSI populateReceipt()

    </script>
</body>
</html>