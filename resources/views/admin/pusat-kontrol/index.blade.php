@extends('layouts.app')
@section('title', 'Pusat Kontrol Pemanggilan')

@section('styles')
<style>
.control-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}
.loket-control-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
}
.loket-control-card.status-tutup {
    background: #f1f2f6;
    opacity: 0.7;
}
.loket-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f0;
}
.loket-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 18px;
}
.loket-header p {
    margin: 0;
    font-size: 13px;
    color: #7f8c8d;
}
.loket-body {
    padding: 20px;
    text-align: center;
}
.current-number {
    font-size: 48px;
    font-weight: 700;
    color: #3498db;
    margin: 10px 0;
}
.current-number.idle {
    color: #bdc3c7;
}
.current-status {
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    color: #95a5a6;
}
.current-status.dipanggil { color: #3498db; }
.current-status.dilayani { color: #27ae60; }

.loket-footer {
    padding: 15px;
    background: #f9f9f9;
    border-top: 1px solid #f0f0f0;
    display: flex;
    gap: 10px;
}
.loket-footer .btn {
    flex: 1;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.2s;
}
.btn-panggil { background-color: #27ae60; color: white; }
.btn-selesai { background-color: #9b59b6; color: white; }
.btn-panggil:disabled,
.btn-selesai:disabled {
    background-color: #bdc3c7;
    cursor: not-allowed;
}
</style>
@endsection

@section('content')

<div class="card" style="margin-bottom: 20px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin: 0;"><i class="fas fa-desktop"></i> Pusat Kontrol Pemanggilan</h2>
    <div>
        <i class="fas fa-circle" style="color: #27ae60; font-size: 12px;"></i> Real-time
    </div>
</div>

<div class="control-grid" id="control-grid">
    <p>Memuat data loket...</p>
</div>
@endsection

@section('scripts')
@vite(['resources/js/bootstrap.js'])

<script>
    const URL_GET_DATA = "{{ route('admin.pusat-kontrol.data') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Elemen
    const controlGrid = document.getElementById('control-grid');

    /**
     * Fungsi utama untuk mengambil data terbaru dan me-render
     */
    async function fetchControlData() {
        try {
            console.log('Fetching data from:', URL_GET_DATA);
            const response = await fetch(URL_GET_DATA);
            console.log('Response status:', response.status);
            
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            console.log('Data received:', data);
            
            if (data.success) {
                renderControlGrid(data.lokets);
            } else {
                console.error('API returned success: false', data);
            }

        } catch (error) {
            console.error("Gagal mengambil data loket:", error);
            controlGrid.innerHTML = '<p style="color: red;">Gagal memuat data. Error: ' + error.message + '</p>';
        }
    }

    /**
     * Merender semua kartu loket
     */
    function renderControlGrid(lokets) {
        if (lokets.length === 0) {
            controlGrid.innerHTML = '<p>Belum ada loket yang dibuat di Manajemen Loket.</p>';
            return;
        }

        let html = '';
        lokets.forEach(loket => {
            let currentKode = '-';
            let currentStatus = 'IDLE';
            let statusClass = 'idle';
            let canPanggil = loket.status === 'aktif';
            let canSelesai = false;

            if (loket.antrian) {
                currentKode = loket.antrian.kode_antrian;
                currentStatus = loket.antrian.status.toUpperCase();
                statusClass = loket.antrian.status; // 'dipanggil' atau 'dilayani'
                canPanggil = false; // Tidak bisa panggil jika sedang ada antrian
                canSelesai = true;
            }

            html += `
                <div class="loket-control-card ${loket.status === 'tutup' ? 'status-tutup' : ''}" id="loket-kontrol-${loket.id}">
                    <div class="loket-header">
                        <h3>${loket.nama_loket}</h3>
                        <p>${loket.layanan} (Operator: ${loket.operator})</p>
                    </div>
                    <div class="loket-body">
                        <p>Sisa Antrian: <strong>${loket.waiting_count}</strong></p>
                        <div class="current-number ${statusClass}">${currentKode}</div>
                        <div class="current-status ${statusClass}">${currentStatus}</div>
                    </div>
                    <div class="loket-footer">
                        <button class="btn btn-panggil" 
                            onclick="aksiPanggil(${loket.id})" 
                            ${canPanggil ? '' : 'disabled'}>
                            <i class="fas fa-bullhorn"></i> Panggil
                        </button>
                        <button class="btn btn-selesai" 
                            onclick="aksiSelesai(${loket.id})"
                            ${canSelesai ? '' : 'disabled'}>
                            <i class="fas fa-check"></i> Selesai
                        </button>
                    </div>
                </div>
            `;
        });
        controlGrid.innerHTML = html;
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
            fetchControlData(); // Refresh data setelah aksi
        } catch (error) {
            console.error(`Gagal ${aksi}:`, error);
        }
    }

    // Fungsi Tombol
    function aksiPanggil(loketId) {
        let url = "{{ route('admin.pusat-kontrol.panggil', ['loket' => ':id']) }}".replace(':id', loketId);
        postAksi(url, 'memanggil');
    }
    
    function aksiSelesai(loketId) {
        let url = "{{ route('admin.pusat-kontrol.selesai', ['loket' => ':id']) }}".replace(':id', loketId);
        postAksi(url, 'menyelesaikan');
    }

    // ============================================================
    // INISIALISASI
    // ============================================================
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Ambil data pertama kali
        fetchControlData();
        
        // 2. Set Polling fallback (10 detik)
        setInterval(fetchControlData, 10000);

        // 3. Listener Real-time (Echo)
        if (typeof Echo !== 'undefined') {
            Echo.channel('antrian-channel')
                .listen('.antrian.dipanggil', (e) => {
                    console.log('Event [antrian.dipanggil] diterima:', e);
                    fetchControlData(); // Refresh seluruh grid
                })
                .listen('.loket.status.updated', (e) => {
                    console.log('Event [loket.status.updated] diterima:', e);
                    fetchControlData(); // Refresh seluruh grid
                });
        } else {
            console.warn('Echo (Pusher) tidak ditemukan. Halaman ini menggunakan polling.');
        }
    });

</script>
@endsection