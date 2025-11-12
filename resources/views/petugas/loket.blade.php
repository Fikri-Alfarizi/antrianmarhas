<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Loket Pelayanan - {{ $loket->nama_loket }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        /* Import Font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        /* Reset & Base */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah scroll di body */
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc; /* Latar belakang modern */
            color: #1e293b;
            font-size: 14px;
        }

        /* Container Utama (Full Screen Grid) */
        .petugas-container {
            display: grid;
            grid-template-columns: 2fr 1fr; /* Pembagian 2 kolom */
            height: 100vh;
            width: 100vw;
        }

        /* ================================= */
        /* === KOLOM KIRI (AKSI) === */
        /* ================================= */
        .kolom-kiri {
            display: flex;
            flex-direction: column;
            padding: 32px;
            gap: 24px;
            overflow-y: auto; /* Scroll darurat jika layar sangat kecil */
            background: #f8fafc;
        }

        /* Info Header */
        .header-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 24px;
        }
        .header-info h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .header-info p {
            font-size: 15px;
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        /* Tombol Buka/Tutup & Logout (Gaya Modern) */
        .btn-header {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-toggle-loket.btn-success {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .btn-toggle-loket.btn-success:hover { background-color: #bbf7d0; }
        .btn-toggle-loket.btn-danger {
            background-color: #fee2e2;
            color: #ef4444;
        }
        .btn-toggle-loket.btn-danger:hover { background-color: #fecaca; }
        
        .btn-logout {
            background: none;
            color: #64748b;
            font-size: 20px; /* Hanya ikon */
            padding: 8px;
        }
        .btn-logout:hover {
            color: #ef4444;
            background-color: #fee2e2;
        }

        /* Kartu Antrian Saat Ini */
        .current-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            flex-grow: 1; /* Mengisi ruang sisa */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .current-card p {
            font-size: 18px;
            font-weight: 500;
            color: #64748b;
            margin: 0 0 10px 0;
        }
        .current-card .nomor-display {
            font-size: 120px;
            font-weight: 900;
            margin: 10px 0;
            line-height: 1;
        }
        .current-card .status-display {
            font-size: 20px;
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 12px;
            margin-top: 10px;
        }

        /* Status Warna Kartu Antrian */
        .current-card.status-idle {
            background: #f8fafc;
            border-style: dashed;
        }
        .current-card.status-idle .nomor-display { color: #cbd5e1; }
        .current-card.status-idle .status-display { background: #e2e8f0; color: #64748b; }
        
        .current-card.status-dipanggil { border-color: #3b82f6; }
        .current-card.status-dipanggil .nomor-display { color: #3b82f6; }
        .current-card.status-dipanggil .status-display { background: #dbeafe; color: #2563eb; }
        
        .current-card.status-dilayani { border-color: #10b981; }
        .current-card.status-dilayani .nomor-display { color: #10b981; }
        .current-card.status-dilayani .status-display { background: #dcfce7; color: #16a34a; }

        .current-card.status-tutup {
            background: #f1f5f9;
            border-color: #e2e8f0;
            border-style: dashed;
        }
        .current-card.status-tutup .nomor-display { color: #94a3b8; font-size: 90px; }
        .current-card.status-tutup .status-display { background: #e2e8f0; color: #64748b; }


        /* Tombol Aksi */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .btn-aksi {
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-aksi:disabled {
            background: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Warna Tombol Aksi */
        #btn-panggil { background-color: #10b981; color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); }
        #btn-panggil:hover:not(:disabled) { background-color: #059669; transform: translateY(-2px); }
        
        #btn-layani { background-color: #3b82f6; color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2); }
        #btn-layani:hover:not(:disabled) { background-color: #2563eb; transform: translateY(-2px); }
        
        .sub-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        #btn-selesai { background-color: #8b5cf6; color: white; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2); }
        #btn-selesai:hover:not(:disabled) { background-color: #7c3aed; }
        
        #btn-batalkan { background-color: #ef4444; color: white; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2); }
        #btn-batalkan:hover:not(:disabled) { background-color: #dc2626; }

        /* ================================= */
        /* === KOLOM KANAN (INFO) === */
        /* ================================= */
        .kolom-kanan {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: #ffffff;
            border-left: 1px solid #e2e8f0;
            overflow: hidden; /* Penting */
        }
        .list-wrapper {
            flex: 1; /* Membuat kedua list membagi ruang 50/50 */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Penting */
        }
        .list-wrapper:first-child {
            border-bottom: 1px solid #e2e8f0;
        }
        .list-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .list-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-header .badge {
            background: #e2e8f0;
            color: #475569;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        /* Ini adalah list yang bisa scroll */
        .list-body {
            overflow-y: auto;
            flex-grow: 1;
            padding: 8px;
        }
        .list-body ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .list-body li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        .list-body li:hover {
            background-color: #f8fafc;
        }
        .list-body li strong {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .list-body li span {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .list-body .empty-list {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 14px;
        }
        
        /* Style untuk Riwayat */
        #list-riwayat li strong {
            font-weight: 500;
        }
        #list-riwayat li .status-selesai {
            color: #16a34a;
            font-weight: 600;
        }
        #list-riwayat li .status-batal {
            color: #ef4444;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .petugas-container {
                grid-template-columns: 1fr; /* Stack kolom di mobile */
                height: auto;
                overflow-y: auto; /* Izinkan scroll di mobile */
            }
            .kolom-kanan {
                height: 600px; /* Batasi tinggi kolom kanan di mobile */
            }
            .current-card .nomor-display {
                font-size: 90px;
            }
        }
    </style>
</head>
<body>

    <div class="petugas-container">
        
        <div class="kolom-kiri">
            
            <div class="header-card">
                <div class="header-info">
                    <h1>{{ $loket->nama_loket }}</h1>
                    <p>{{ $loket->layanan->nama_layanan }}</p>
                </div>
                <div class="header-actions">
                    <button class="btn-header btn-toggle-loket" id="btn-toggle-loket" onclick="toggleLoket()">
                        <i class="fas fa-power-off"></i>
                        <span id="toggle-loket-text">...</span>
                    </button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-header btn-logout" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="current-card status-idle" id="current-antrian-card">
                <p>Antrian Saat Ini</p>
                <h2 class="nomor-display" id="current-kode">-</h2>
                <span class="status-display" id="current-status">Tidak Ada Antrian</span>
            </div>

            <div class="action-grid">
                <button class="btn-aksi" id="btn-panggil" onclick="panggil()">
                    <i class="fas fa-bullhorn"></i> PANGGIL BERIKUTNYA
                </button>
                <button class="btn-aksi" id="btn-layani" onclick="layani()">
                    <i class="fas fa-play-circle"></i> MULAI LAYANI
                </button>
                <div class="sub-actions">
                    <button class="btn-aksi" id="btn-selesai" onclick="selesai()">
                        <i class="fas fa-check-circle"></i> SELESAI
                    </button>
                    <button class="btn-aksi" id="btn-batalkan" onclick="batalkan()">
                        <i class="fas fa-times-circle"></i> BATALKAN
                    </button>
                </div>
            </div>

        </div>

        <div class="kolom-kanan">
            
            <div class="list-wrapper">
                <div class="list-header">
                    <h3>
                        Daftar Menunggu
                        <span class="badge" id="count-menunggu">0</span>
                    </h3>
                </div>
                <div class="list-body">
                    <ul id="list-menunggu">
                        <li class="empty-list">Memuat antrian...</li>
                    </ul>
                </div>
            </div>
            
            <div class="list-wrapper">
                <div class="list-header">
                    <h3>
                        Riwayat Hari Ini
                        <span class="badge" id="count-selesai">0</span>
                    </h3>
                </div>
                <div class="list-body">
                    <ul id="list-riwayat">
                        <li class="empty-list">Memuat riwayat...</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
    
    @vite(['resources/js/bootstrap.js'])

    <script>
        // URLs
        const URL_GET_LIST = "{{ route('petugas.loket.list') }}";
        const URL_PANGGIL = "{{ route('petugas.loket.panggil') }}";
        const URL_LAYANI = "{{ route('petugas.loket.layani') }}";
        const URL_SELESAI = "{{ route('petugas.loket.selesai') }}";
        const URL_BATALKAN = "{{ route('petugas.loket.batalkan') }}";
        const URL_TOGGLE_LOKET = "{{ route('petugas.loket.tutup') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Elemen Tombol
        const btnPanggil = document.getElementById('btn-panggil');
        const btnLayani = document.getElementById('btn-layani');
        const btnSelesai = document.getElementById('btn-selesai');
        const btnBatalkan = document.getElementById('btn-batalkan');
        const btnToggleLoket = document.getElementById('btn-toggle-loket');
        const toggleLoketText = document.getElementById('toggle-loket-text');

        // Elemen Data
        const currentCard = document.getElementById('current-antrian-card');
        const currentKode = document.getElementById('current-kode');
        const currentStatus = document.getElementById('current-status');
        const listMenunggu = document.getElementById('list-menunggu');
        const listRiwayat = document.getElementById('list-riwayat');
        const countMenunggu = document.getElementById('count-menunggu');
        const countSelesai = document.getElementById('count-selesai');

        // Variabel Status
        let currentAntrian = null;
        let loketStatus = '{{ $loket->status }}';

        /**
         * Fungsi utama untuk mengambil data terbaru
         */
        async function fetchAntrianData() {
            try {
                const response = await fetch(URL_GET_LIST);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                
                if (data.error) {
                    console.error('[ANTRIAN ERROR]', data.error);
                    updateCurrentCard(null);
                    updateWaitingList([], 0);
                    updateHistoryList([], 0);
                    return;
                }
                
                currentAntrian = data.current; 
                loketStatus = data.loket_status; 
                
                updateCurrentCard(data.current);
                updateWaitingList(data.waiting || [], data.stats?.menunggu_total || 0);
                updateHistoryList(data.history || [], data.stats?.selesai_total || 0); // Menggunakan stats.selesai_total
                updateButtonState();
                updateLoketStatusUI();
                
                console.log('[ANTRIAN OK] Waiting:', data.waiting?.length || 0, 'History:', data.history?.length || 0);

            } catch (error) {
                console.error("[ANTRIAN ERROR] Gagal mengambil data antrian:", error);
                // Non-aktifkan tombol jika fetch gagal
                btnPanggil.disabled = true;
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                currentStatus.textContent = "Error Koneksi";
            }
        }

        /**
         * Update Kartu Antrian Saat Ini
         */
        function updateCurrentCard(antrian) {
            // Hapus semua status kelas
            currentCard.classList.remove('status-idle', 'status-dipanggil', 'status-dilayani', 'status-tutup');

            if (loketStatus === 'tutup') {
                currentCard.classList.add('status-tutup');
                currentKode.textContent = "LOKET";
                currentStatus.textContent = "TUTUP";
                return;
            }

            if (antrian) {
                currentKode.textContent = antrian.kode_antrian;
                currentStatus.textContent = antrian.status.toUpperCase();
                
                if (antrian.status === 'dipanggil') {
                    currentCard.classList.add('status-dipanggil');
                } else if (antrian.status === 'dilayani') {
                    currentCard.classList.add('status-dilayani');
                } else {
                    currentCard.classList.add('status-idle'); // Fallback
                }
            } else {
                currentCard.classList.add('status-idle');
                currentKode.textContent = "-";
                currentStatus.textContent = "Tidak Ada Antrian";
            }
        }

        /**
         * Update Daftar Menunggu
         */
        function updateWaitingList(waiting, total) {
            countMenunggu.textContent = total;
            if (waiting.length === 0) {
                listMenunggu.innerHTML = '<li class="empty-list">Tidak ada antrian menunggu.</li>';
                return;
            }
            let html = '';
            waiting.forEach(a => {
                html += `<li>
                            <strong>${a.kode_antrian}</strong>
                            <span>${new Date(a.waktu_ambil).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}</span>
                         </li>`;
            });
            listMenunggu.innerHTML = html;
        }

        /**
         * Update Daftar Riwayat
         */
        function updateHistoryList(history, total) {
            countSelesai.textContent = total;
            if (history.length === 0) {
                listRiwayat.innerHTML = '<li class="empty-list">Belum ada riwayat.</li>';
                return;
            }
            let html = '';
            history.forEach(a => {
                const statusClass = a.status === 'batal' ? 'status-batal' : 'status-selesai';
                const time = a.waktu_selesai || a.waktu_panggil || a.waktu_ambil; // Fallback waktu
                html += `<li>
                            <div>
                                <strong>${a.kode_antrian}</strong>
                                <span style="display: block;" class="${statusClass}">${a.status.toUpperCase()}</span>
                            </div>
                            <span>${new Date(time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                         </li>`;
            });
            listRiwayat.innerHTML = html;
        }

        /**
         * Mengatur tombol mana yang bisa diklik
         */
        function updateButtonState() {
            if (loketStatus === 'tutup') {
                btnPanggil.disabled = true;
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                return;
            }

            if (currentAntrian) {
                if (currentAntrian.status === 'dipanggil') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = false;
                    btnSelesai.disabled = false;
                    btnBatalkan.disabled = false;
                } else if (currentAntrian.status === 'dilayani') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = true;
                    btnSelesai.disabled = false;
                    btnBatalkan.disabled = false;
                }
            } else {
                btnPanggil.disabled = false; // Bisa memanggil jika tidak ada antrian
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
            }
        }
        
        /**
         * Mengatur UI tombol Buka/Tutup Loket
         */
        function updateLoketStatusUI() {
            if (loketStatus === 'aktif') {
                btnToggleLoket.classList.remove('btn-success');
                btnToggleLoket.classList.add('btn-danger');
                toggleLoketText.textContent = 'Tutup Loket';
            } else {
                btnToggleLoket.classList.remove('btn-danger');
                btnToggleLoket.classList.add('btn-success');
                toggleLoketText.textContent = 'Buka Loket';
            }
        }

        /**
         * Fungsi POST universal untuk tombol
         */
        async function postAksi(url, aksi) {
            // Nonaktifkan semua tombol sementara
            btnPanggil.disabled = true;
            btnLayani.disabled = true;
            btnSelesai.disabled = true;
            btnBatalkan.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                });
                const data = await response.json();
                if (!data.success) {
                    alert('Error: ' (data.message || 'Aksi gagal'));
                }
                fetchAntrianData(); // Refresh data setelah aksi
            } catch (error) {
                console.error(`Gagal ${aksi}:`, error);
                alert(`Gagal ${aksi}. Periksa konsol.`);
                fetchAntrianData(); // Refresh data
            }
        }

        // Fungsi Tombol
        function panggil() { postAksi(URL_PANGGIL, 'memanggil'); }
        function layani() { postAksi(URL_LAYANI, 'melayani'); }
        function selesai() { postAksi(URL_SELESAI, 'menyelesaikan'); }
        function batalkan() { 
            if (confirm('Apakah Anda yakin ingin MEMBATALKAN antrian ini?')) {
                postAksi(URL_BATALKAN, 'membatalkan'); 
            }
        }
        async function toggleLoket() {
            const aksi = (loketStatus === 'aktif') ? 'menutup' : 'membuka';
            if (confirm(`Apakah Anda yakin ingin ${aksi} loket?`)) {
                try {
                    const response = await fetch(URL_TOGGLE_LOKET, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                    });
                    const data = await response.json();
                    if (data.success) {
                        loketStatus = data.new_status;
                        updateLoketStatusUI();
                        updateButtonState(); // Nonaktifkan/aktifkan tombol
                        updateCurrentCard(currentAntrian); // Update kartu besar
                    }
                } catch (error) {
                    console.error('Gagal toggle loket:', error);
                }
            }
        }

        // ============================================================
        // INISIALISASI
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Halaman Loket Petugas Dimuat.');
            
            fetchAntrianData();
            
            setInterval(fetchAntrianData, 5000); // Polling fallback

            if (typeof Echo !== 'undefined') {
                console.log('Echo terdeteksi, mencoba terhubung ke channel...');
                Echo.channel('antrian') // Nama channel publik
                    .listen('.antrian.dipanggil', (e) => {
                        console.log('Event [antrian.dipanggil] diterima:', e);
                        // Cek apakah event ini untuk layanan yang dilayani loket ini
                        if (e.layanan_id === {{ $loket->layanan_id }}) {
                            fetchAntrianData();
                        }
                    })
                    .listen('.loket.status.updated', (e) => {
                        console.log('Event [loket.status.updated] diterima:', e);
                        // Cek apakah event ini untuk loket ini
                        if (e.loket_id === {{ $loket->id }}) {
                            fetchAntrianData();
                        }
                    })
                    .error((error) => {
                        console.error('Echo connection error:', error);
                    });
                
                Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('Echo BERHASIL terhubung (Real-time Aktif).');
                });
                Echo.connector.pusher.connection.bind('disconnected', () => {
                    console.warn('Echo TERPUTUS (Mengandalkan polling 5 detik).');
                });

            } else {
                console.warn('Echo (Pusher) tidak ditemukan. Halaman akan menggunakan polling 5 detik.');
            }
        });

    </script>
</body>
</html>