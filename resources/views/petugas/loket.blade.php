<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Loket Pelayanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 900px;
            margin: auto;
        }
        
        /* Header */
        .header-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-card .title h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header-card .title p {
            margin: 0;
            color: #7f8c8d;
            font-size: 16px;
        }
        .header-card .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Tombol Buka/Tutup Loket */
        .btn-toggle-loket {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: 0.2s;
        }
        .btn-toggle-loket.status-aktif {
            background-color: #e74c3c;
            color: white;
        }
        .btn-toggle-loket.status-tutup {
            background-color: #27ae60;
            color: white;
        }
        .btn-logout {
            background: none;
            border: none;
            color: #95a5a6;
            font-size: 14px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-logout:hover { color: #e74c3c; }

        /* Card Antrian Saat Ini */
        .current-card {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        .current-card p {
            font-size: 18px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .current-card h2 {
            font-size: 72px;
            font-weight: 900;
            margin: 10px 0;
            letter-spacing: 2px;
        }
        .current-card .status-label {
            display: inline-block;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
        }
        .current-card.empty {
            background: #fff;
            color: #7f8c8d;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .current-card.empty h2 {
            font-size: 60px;
            color: #bdc3c7;
        }

        /* Tombol Aksi */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .btn-aksi {
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-aksi:disabled {
            background-color: #ecf0f1;
            color: #bdc3c7;
            cursor: not-allowed;
        }
        #btn-panggil { background-color: #27ae60; color: white; }
        #btn-panggil:hover:not(:disabled) { background-color: #229954; }
        #btn-layani { background-color: #3498db; color: white; }
        #btn-layani:hover:not(:disabled) { background-color: #2980b9; }
        
        .sub-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        #btn-selesai { background-color: #9b59b6; color: white; }
        #btn-selesai:hover:not(:disabled) { background-color: #8e44ad; }
        #btn-batalkan { background-color: #e74c3c; color: white; }
        #btn-batalkan:hover:not(:disabled) { background-color: #c0392b; }

        /* Grid Kolom Daftar */
        .lists-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .list-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .list-card h3 {
            padding: 15px 20px;
            background: #f9f9f9;
            border-bottom: 1px solid #f0f0f0;
            color: #2c3e50;
            font-size: 16px;
        }
        .list-card h3 .badge {
            background: #3498db;
            color: white;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }
        .list-card ul {
            list-style: none;
            padding: 10px;
            max-height: 250px;
            overflow-y: auto;
        }
        .list-card li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 10px;
            border-bottom: 1px dashed #f0f0f0;
            font-size: 14px;
        }
        .list-card li:last-child { border-bottom: none; }
        .list-card li strong {
            font-size: 16px;
            color: #333;
        }
        .list-card li span {
            font-size: 12px;
            color: #95a5a6;
        }
        .list-card .empty-list {
            padding: 30px;
            text-align: center;
            color: #95a5a6;
        }

        @media (max-width: 768px) {
            body { padding: 10px; }
            .header-card { flex-direction: column; gap: 15px; }
            .lists-grid { grid-template-columns: 1fr; }
            .current-card h2 { font-size: 56px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header-card">
            <div class="title">
                <h1>{{ $loket->nama_loket }}</h1>
                <p>{{ $loket->layanan->nama_layanan }}</p>
            </div>
            <div class="actions">
                <button class="btn-toggle-loket status-{{ $loket->status }}" id="btn-toggle-loket" onclick="toggleLoket()">
                    <i class="fas fa-power-off"></i>
                    <span id="toggle-loket-text">{{ $loket->status == 'aktif' ? 'Tutup Loket' : 'Buka Loket' }}</span>
                </button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="Logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="current-card empty" id="current-antrian-card">
            <p>Antrian Saat Ini</p>
            <h2 id="current-kode">-</h2>
            <span class="status-label" id="current-status">Tidak Ada Antrian</span>
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

        <div class="lists-grid">
            <div class="list-card">
                <h3>Daftar Menunggu <span class="badge" id="count-menunggu">0</span></h3>
                <ul id="list-menunggu">
                    <li class="empty-list">Tidak ada antrian menunggu.</li>
                </ul>
            </div>
            <div class="list-card">
                <h3>Riwayat Hari Ini <span class="badge" id="count-selesai">0</span></h3>
                <ul id="list-riwayat">
                    <li class="empty-list">Belum ada riwayat.</li>
                </ul>
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
                
                // Cek jika ada error dari server
                if (data.error) {
                    console.error('[ANTRIAN ERROR]', data.error);
                    updateCurrentCard(null);
                    updateWaitingList([], 0);
                    updateHistoryList([], 0);
                    return;
                }
                
                currentAntrian = data.current; // Simpan antrian saat ini
                loketStatus = data.loket_status; // Simpan status loket
                
                updateCurrentCard(data.current);
                updateWaitingList(data.waiting || [], data.stats?.menunggu_total || 0);
                updateHistoryList(data.history || [], data.stats?.selesai || 0);
                updateButtonState();
                updateLoketStatusUI();
                
                console.log('[ANTRIAN OK] Waiting:', data.waiting?.length || 0, 'History:', data.history?.length || 0);

            } catch (error) {
                console.error("[ANTRIAN ERROR] Gagal mengambil data antrian:", error);
                alert('❌ Gagal memuat data antrian. Hubungi administrator.\n\nError: ' + error.message);
            }
        }

        /**
         * Update Kartu Antrian Saat Ini
         */
        function updateCurrentCard(antrian) {
            if (antrian) {
                currentCard.classList.remove('empty');
                currentKode.textContent = antrian.kode_antrian;
                currentStatus.textContent = antrian.status.toUpperCase();
            } else {
                currentCard.classList.add('empty');
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
                            <span>${new Date(a.waktu_ambil).toLocaleTimeString('id-ID')}</span>
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
                html += `<li>
                            <div>
                                <strong>${a.kode_antrian}</strong>
                                <span style="display: block; color: ${a.status === 'batal' ? '#e74c3c' : '#27ae60'};">${a.status.toUpperCase()}</span>
                            </div>
                            <span>${new Date(a.waktu_selesai).toLocaleTimeString('id-ID')}</span>
                         </li>`;
            });
            listRiwayat.innerHTML = html;
        }

        /**
         * Mengatur tombol mana yang bisa diklik
         */
        function updateButtonState() {
            // Jika loket ditutup, nonaktifkan semua tombol aksi
            if (loketStatus === 'tutup') {
                btnPanggil.disabled = true;
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                currentCard.classList.add('empty');
                currentKode.textContent = "LOKET";
                currentStatus.textContent = "TUTUP";
                return;
            }

            if (currentAntrian) {
                if (currentAntrian.status === 'dipanggil') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = false;
                    btnSelesai.disabled = false; // Bisa langsung selesai
                    btnBatalkan.disabled = false;
                } else if (currentAntrian.status === 'dilayani') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = true;
                    btnSelesai.disabled = false;
                    btnBatalkan.disabled = false;
                }
            } else {
                // Tidak ada antrian dilayani
                btnPanggil.disabled = false;
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
                btnToggleLoket.classList.remove('status-tutup');
                btnToggleLoket.classList.add('status-aktif');
                toggleLoketText.textContent = 'Tutup Loket';
            } else {
                btnToggleLoket.classList.remove('status-aktif');
                btnToggleLoket.classList.add('status-tutup');
                toggleLoketText.textContent = 'Buka Loket';
            }
        }

        /**
         * Fungsi POST universal untuk tombol
         */
        async function postAksi(url, aksi) {
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
                    alert('Error: ' + data.message);
                }
                fetchAntrianData(); // Refresh data setelah aksi
            } catch (error) {
                console.error(`Gagal ${aksi}:`, error);
                alert(`Gagal ${aksi}. Periksa konsol.`);
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
            
            // 1. Ambil data pertama kali
            fetchAntrianData();
            
            // 2. Polling fallback setiap 5 detik
            setInterval(fetchAntrianData, 5000);

            // 3. Listener Real-time (Echo)
            if (typeof Echo !== 'undefined') {
                console.log('Echo terdeteksi, mencoba terhubung ke channel...');
                Echo.channel('antrian-channel')
                    .listen('.antrian.dipanggil', (e) => {
                        console.log('Event [antrian.dipanggil] diterima:', e);
                        // Jika antrian yang dipanggil relevan dengan layanan ini
                        if (e.layanan_id === {{ $loket->layanan_id }}) {
                            fetchAntrianData();
                        }
                    })
                    .listen('.loket.status.updated', (e) => {
                        console.log('Event [loket.status.updated] diterima:', e);
                        // Jika loket ini yang diupdate (misal oleh admin)
                        if (e.loket_id === {{ $loket->id }}) {
                            fetchAntrianData();
                        }
                    })
                    .error((error) => {
                        console.error('Echo connection error:', error);
                    });
                
                // Monitor koneksi Echo
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