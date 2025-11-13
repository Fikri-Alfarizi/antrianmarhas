<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Antrian Modern - {{ $pengaturan->nama_instansi ?? 'Rumah Sakit' }}</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

<style>
    /* Import Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    /* Reset dan Global */
    *, *::before, *::after {
        box-sizing: border-box;
    }
    
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #f9fafb 100%);
        color: #374151;
        display: flex;
        flex-direction: column;
        height: 100vh;
    }
    
    /* Kontainer Utama (agar konten berada di tengah) */
    .kios-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Header Modern */
    .header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 20px 40px;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 24px;
        flex-shrink: 0;
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 16px;
        overflow: hidden; /* Tambahkan agar logo tidak keluar */
    }

    .logo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .logo {
        font-size: 28px;
        color: white;
    }

    .brand-info {
        flex: 1;
    }

    .brand-name {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }

    .brand-address {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .status-badge {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* Main Content */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 40px;
        overflow: hidden;
        position: relative;
    }

    .welcome-section {
        text-align: center;
        margin-bottom: 40px;
        animation: fadeInUp 0.6s ease-out;
        flex-shrink: 0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .welcome-title {
        font-size: 42px;
        font-weight: 800;
        background: linear-gradient(135deg, #111827 0%, #374151 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 12px 0;
        letter-spacing: -1px;
    }

    .welcome-subtitle {
        font-size: 20px;
        color: #6b7280;
        margin: 0;
        font-weight: 500;
    }

    /* Services Grid Modern */
    .services-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto; /* Untuk layanan yang banyak */
        padding-bottom: 20px;
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        width: 100%;
        max-width: 1200px;
    }

    /* Service Card Modern */
    .service-card {
        background: white;
        border-radius: 20px;
        padding: 32px;
        border: 1px solid rgba(229, 231, 235, 0.5);
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-height: 220px;
    }
    
    .service-card:disabled {
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
        box-shadow: none;
        border: 1px solid #e5e7eb;
    }
    
    .service-card:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, transparent);
        transition: all 0.4s ease;
    }

    .service-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: transparent;
    }

    .service-card:hover::before {
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        animation: gradient 3s ease infinite;
    }
    
    .service-card:disabled:hover::before {
        animation: none;
        background: linear-gradient(90deg, transparent, transparent);
    }


    @keyframes gradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .service-icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .service-card:hover .service-icon-wrapper {
        transform: rotate(5deg) scale(1.1);
    }

    .service-icon {
        font-size: 36px;
    }
    /* Menggunakan warna yang sama untuk semua card layanan dari database */
    .service-icon.default { 
        background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        color: white;
    }

    .service-title {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }

    .service-desc {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        line-height: 1.5;
    }

    /* Modal Modern (Layaknya Receipt Screen) */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 24px;
        padding: 48px;
        width: 480px;
        max-width: 90%;
        text-align: center;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        transform: scale(0.8) translateY(20px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .modal-overlay.show .modal-content {
        transform: scale(1) translateY(0);
    }

    .modal-icon-wrapper {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: successPulse 0.6s ease;
    }

    @keyframes successPulse {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .modal-icon {
        font-size: 40px;
        color: white;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #6b7280;
        margin: 0 0 16px 0;
    }

    .modal-number {
        font-size: 72px;
        font-weight: 800;
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 0 20px 0;
        line-height: 1;
    }

    .modal-service {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 24px 0;
    }

    .modal-info {
        font-size: 16px;
        color: #6b7280;
        margin: 0 0 32px 0;
    }

    .modal-footer {
        font-size: 14px;
        color: #9ca3af;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }
    
    .modal-btn {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
        margin-top: 20px;
    }
    
    .modal-btn:hover {
        opacity: 0.9;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .header {
            padding: 16px 20px;
            flex-wrap: wrap;
        }
        
        .brand-name { font-size: 20px; }
        .brand-address { font-size: 12px; }
        
        .main-content {
            padding: 24px 20px;
        }
        
        .welcome-title { font-size: 32px; }
        .welcome-subtitle { font-size: 18px; }
        
        .services-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .service-card {
            padding: 24px;
            min-height: 180px;
        }
        
        .service-icon-wrapper {
            width: 60px;
            height: 60px;
        }
        
        .service-icon { font-size: 28px; }
        .service-title { font-size: 18px; }
        
        .modal-content {
            padding: 32px;
        }
        .modal-number { font-size: 56px; }
        .modal-service { font-size: 20px; }
    }

    @media (max-height: 800px) {
        .welcome-title { font-size: 36px; }
        .service-card { min-height: 200px; }
    }
</style>
</head>
<body>
    
<div class="kios-container">
    <header class="header">
        <div class="logo-container">
            @if(isset($pengaturan->logo) && $pengaturan->logo)
                <img src="{{ $pengaturan->logo }}" alt="Logo" class="logo-image">
            @else
                <i class="fa-solid fa-hospital logo"></i>
            @endif
        </div>
        <div class="brand-info">
            <h1 class="brand-name">{{ $pengaturan->nama_instansi ?? 'Rumah Sakit Ruang Coding' }}</h1>
            <p class="brand-address" id="brandAddress">{{ $pengaturan->alamat_instansi ?? 'Silakan pilih layanan untuk mengambil nomor antrian' }}</p>
        </div>
        <div class="status-badge">
            <i class="fa-solid fa-circle"></i>
            <span>Sistem Aktif</span>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-section">
            <h2 class="welcome-title">Selamat Datang</h2>
            <p class="welcome-subtitle" id="welcomeSubtitle">Silakan pilih layanan untuk mengambil nomor antrian</p>
        </div>
        
        <div class="services-container" id="layananScreen">
            <div class="services-grid" id="layananGrid">
                @if($layanans->isEmpty())
                    <p style="text-align: center; grid-column: 1 / -1; color: #9ca3af; font-size: 18px;">
                        <i class="fas fa-info-circle"></i> Saat ini tidak ada layanan yang tersedia.
                    </p>
                @else
                    @foreach($layanans as $layanan)
                        <button class="service-card" 
                            data-service="{{ $layanan->nama_layanan }}"
                            onclick="handleCetak({{ $layanan->id }})">
                            <div class="service-icon-wrapper service-icon default">
                                <i class="fa-solid fa-bell-concierge service-icon"></i>
                            </div>
                            <h3 class="service-title">{{ $layanan->nama_layanan }}</h3>
                            <p class="service-desc">{{ $layanan->keterangan ?? 'Tekan untuk mengambil antrian layanan ini.' }}</p>
                        </button>
                    @endforeach
                @endif
            </div>
        </div>
        
    </main>
</div>
    
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content">
        <div class="modal-icon-wrapper">
            <i class="fa-solid fa-check modal-icon"></i>
        </div>
        <h4 class="modal-title">Nomor Antrian Anda</h4>
        <div class="modal-number" id="modalNumber">A-001</div>
        <h3 class="modal-service" id="modalService">Pemeriksaan Umum</h3>
        <p class="modal-info">Silakan menunggu nomor Anda dipanggil. Struk sedang dicetak.</p>
        
        <button class="modal-btn" onclick="hideModal()">
            <i class="fas fa-arrow-left"></i> Selesai
        </button>
        
        <div class="modal-footer">
            Terima kasih atas kunjungan Anda
        </div>
    </div>
</div>
    
<div class="modal-overlay" id="loadingOverlay">
    <div class="modal-content" style="width: 200px; padding: 30px; border-radius: 50%; display: flex; justify-content: center; align-items: center; background: none; box-shadow: none;">
        <div class="service-icon-wrapper service-icon blue" style="width: 100px; height: 100px; margin: 0; animation: spin 1s linear infinite;">
            <i class="fa-solid fa-hourglass-half service-icon" style="font-size: 48px;"></i>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Pastikan route 'kios.cetak' sudah didefinisikan di web.php Anda
    const URL_CETAK = "{{ route('kios.cetak') }}"; 
    
    // Elemen UI Baru
    const loadingOverlay = document.getElementById('loadingOverlay');
    const modalOverlay = document.getElementById('modalOverlay');
    const modalNumber = document.getElementById('modalNumber');
    const modalService = document.getElementById('modalService');
    const welcomeSubtitle = document.getElementById('welcomeSubtitle');
    const layananGrid = document.getElementById('layananGrid');
    const serviceCards = document.querySelectorAll('.service-card');
    
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    
    /**
     * Mengatur status loading (menonaktifkan tombol dan menampilkan loading overlay)
     */
    function setLoading(isLoading) {
        if (isLoading) {
            loadingOverlay.classList.add('show');
            serviceCards.forEach(btn => {
                btn.disabled = true;
            });
        } else {
            loadingOverlay.classList.remove('show');
            serviceCards.forEach(btn => {
                btn.disabled = false;
            });
        }
    }
    
    /**
     * Memutar suara sukses (seperti di kode HTML Anda)
     */
    function playSuccessSound() {
        try {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(523.25, audioContext.currentTime); // C5
            oscillator.frequency.setValueAtTime(659.25, audioContext.currentTime + 0.1); // E5
            oscillator.frequency.setValueAtTime(783.99, audioContext.currentTime + 0.2); // G5
            
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.4);
        } catch (error) {
            console.error("Gagal memutar suara:", error);
        }
    }
    
    /**
     * Fungsi yang dipanggil saat tombol Selesai/Overlay ditekan
     */
    function hideModal() {
        modalOverlay.classList.remove('show');
        // Kembalikan subtitle ke awal
        welcomeSubtitle.textContent = 'Silakan pilih layanan untuk mengambil nomor antrian';
        
        // Opsional: Reload halaman setelah modal hilang agar antrian ter-reset
        // setTimeout(() => { window.location.reload(); }, 300);
    }
    
    // Event listener untuk menutup modal saat mengklik di luar konten modal
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            hideModal();
        }
    });

    /**
     * Fungsi utama untuk mengambil nomor antrian (TETAP SAMA)
     */
    async function handleCetak(layananId) {
        setLoading(true);
        
        const targetCard = document.querySelector(`.service-card[onclick*="handleCetak(${layananId})"]`);
        const serviceName = targetCard ? targetCard.getAttribute('data-service') : 'Layanan Tidak Dikenal';
        
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
                // 1. Isi data ke Modal
                modalService.textContent = data.antrian.nama_layanan || serviceName;
                modalNumber.textContent = data.antrian.kode_antrian;
                
                // 2. Tampilkan Modal
                modalOverlay.classList.add('show');
                welcomeSubtitle.textContent = 'Antrian Anda Berhasil Diambil'; // Ganti subtitle header
                playSuccessSound(); // Mainkan suara

                // Opsional: Otomatis tutup modal setelah 10 detik
                // setTimeout(() => {
                //   modalOverlay.classList.remove('show');
                //   welcomeSubtitle.textContent = 'Silakan pilih layanan untuk mengambil nomor antrian';
                // }, 10000); 
                
            } else {
                alert('Error: ' + data.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        } finally {
            // Sembunyikan loading spinner
            setLoading(false); 
        }
    }
</script>
</body>
</html>