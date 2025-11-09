@extends('layouts.app')
@section('title', 'Loket Pemanggilan')

@section('styles')
<style>
.loket-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 15px; background: #e3f2fd; border-radius: 8px; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin: 20px 0; }
.stat-box { 
    padding: 20px; 
    border-radius: 8px; 
    text-align: center; 
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-top: 4px solid;
}
.stat-box h3 { font-size: 36px; margin: 10px 0; font-weight: 700; }
.stat-box p { font-size: 13px; margin: 0; }
.stat-primary { background: #3498db; border-top-color: #2980b9; }
.stat-warning { background: #f39c12; border-top-color: #d68910; }
.stat-success { background: #27ae60; border-top-color: #1e8449; }
.stat-danger { background: #e74c3c; border-top-color: #c0392b; }
.action-buttons { display: flex; gap: 10px; flex-wrap: wrap; margin: 30px 0; }
.btn-lg { font-size: 18px; padding: 20px 30px; }
.action-buttons .btn { flex: 1; min-width: 150px; }
.antrian-info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3498db; }
.antrian-info h4 { margin-bottom: 10px; color: #2c3e50; }
.antrian-info p { margin: 8px 0; }
.performance-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
.performance-card { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #3498db; }
.performance-card h4 { margin: 0 0 15px 0; color: #2c3e50; display: flex; align-items: center; gap: 8px; }
.metric { display: flex; justify-content: space-between; margin: 10px 0; font-size: 13px; }
.metric-label { color: #666; }
.metric-value { font-weight: 600; color: #2c3e50; }
.progress-bar { width: 100%; height: 24px; background: #ecf0f1; border-radius: 4px; margin: 8px 0; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #27ae60, #2ecc71); text-align: center; color: white; font-size: 11px; line-height: 24px; font-weight: 600; }
.antrian-list { max-height: 300px; overflow-y: auto; }
.antrian-item { 
    padding: 12px; 
    background: white; 
    margin-bottom: 8px; 
    border-radius: 5px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    border-left: 4px solid #3498db;
}
.antrian-item.dipanggil { border-left-color: #f39c12; background: #fffbea; }
.antrian-item.dilayani { border-left-color: #27ae60; background: #f0f8f0; }
.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.status-badge.menunggu { background: #e3f2fd; color: #0d47a1; }
.status-badge.dipanggil { background: #fff3e0; color: #e65100; }
.status-badge.dilayani { background: #e8f5e9; color: #1b5e20; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.modal-content { background: white; padding: 30px; border-radius: 10px; max-width: 500px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
.modal-header { font-size: 20px; font-weight: 600; margin-bottom: 15px; color: #2c3e50; }
.modal-close { float: right; background: none; border: none; font-size: 24px; cursor: pointer; }
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .action-buttons { flex-direction: column; }
    .action-buttons .btn { width: 100%; }
}
</style>
@endsection

@section('content')
<div class="card">
    <div class="loket-info">
        <div>
            <h2 style="margin: 0;"><i class="fas fa-door-open"></i> {{ $loket->nama_loket }}</h2>
            <p style="margin: 5px 0 0 0; color: #666;">{{ $loket->layanan->nama_layanan }}</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; color: #666;"><i class="fas fa-clock"></i> {{ now()->format('H:i:s') }}</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box stat-primary">
            <h3>{{ $statistik['total'] ?? 0 }}</h3>
            <p><i class="fas fa-list"></i> Total Hari Ini</p>
        </div>
        <div class="stat-box stat-warning">
            <h3>{{ $statistik['menunggu'] ?? 0 }}</h3>
            <p><i class="fas fa-clock"></i> Menunggu</p>
        </div>
        <div class="stat-box stat-success">
            <h3>{{ $statistik['selesai'] ?? 0 }}</h3>
            <p><i class="fas fa-check"></i> Selesai</p>
        </div>
        <div class="stat-box stat-danger">
            <h3>{{ $statistik['batal'] ?? 0 }}</h3>
            <p><i class="fas fa-ban"></i> Dibatalkan</p>
        </div>
    </div>

    <div class="antrian-info" id="antrianTerakhir">
        <h4><i class="fas fa-bell"></i> Antrian Terakhir Dipanggil</h4>
        <p id="antrianStatus" style="font-size: 24px; font-weight: bold; color: #3498db; margin: 10px 0;">-</p>
        <p id="antrianWaktu" style="font-size: 12px; color: #999; margin: 0;">-</p>
    </div>

    <div class="action-buttons">
        <button class="btn btn-primary btn-lg" onclick="pangilAntrian()">
            <i class="fas fa-phone"></i> PANGGIL ANTRIAN
        </button>
        <button class="btn btn-success btn-lg" onclick="layaniAntrian()">
            <i class="fas fa-check"></i> LAYANI
        </button>
        <button class="btn btn-warning btn-lg" onclick="selesaiAntrian()">
            <i class="fas fa-flag-checkered"></i> SELESAI
        </button>
        <button class="btn btn-danger btn-lg" onclick="batalkanAntrian()">
            <i class="fas fa-times"></i> BATALKAN
        </button>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
        <h3><i class="fas fa-chart-bar"></i> Performa Hari Ini</h3>
        
        <div class="performance-grid">
            <div class="performance-card">
                <h4><i class="fas fa-target"></i> Target</h4>
                <div class="metric">
                    <span class="metric-label">Target:</span>
                    <span class="metric-value">{{ $performanceMetrics['target'] ?? 50 }} pelanggan</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Tercapai:</span>
                    <span class="metric-value">{{ $performanceMetrics['completed'] ?? 0 }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, ($performanceMetrics['completed'] ?? 0) / ($performanceMetrics['target'] ?? 50) * 100) }}%;">
                        {{ round(($performanceMetrics['completed'] ?? 0) / ($performanceMetrics['target'] ?? 50) * 100) }}%
                    </div>
                </div>
            </div>

            <div class="performance-card" style="border-left-color: #f39c12;">
                <h4><i class="fas fa-hourglass-half"></i> Waktu Rata-rata</h4>
                <div class="metric">
                    <span class="metric-label">Per Pelanggan:</span>
                    <span class="metric-value">{{ $performanceMetrics['avg_time'] ?? 0 }} menit</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Total Hari Ini:</span>
                    <span class="metric-value">{{ $performanceMetrics['total_time'] ?? 0 }} menit</span>
                </div>
            </div>

            <div class="performance-card" style="border-left-color: #27ae60;">
                <h4><i class="fas fa-star"></i> Rating Performa</h4>
                <div class="metric">
                    <span class="metric-label">Rating:</span>
                    <span class="metric-value" style="color: #f39c12;">{{ $performanceMetrics['rating'] ?? 'N/A' }}</span>
                </div>
                <p style="font-size: 12px; color: #666; margin: 10px 0 0 0;">
                    <i class="fas fa-info-circle"></i> Berdasarkan target dan efisiensi
                </p>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
        <h3><i class="fas fa-list"></i> Daftar Antrian Menunggu</h3>
        <div class="antrian-list" id="antrianList">
            <p style="text-align: center; color: #999;">Memuat data...</p>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        <div class="modal-header">
            <i class="fas fa-question-circle"></i> Konfirmasi
        </div>
        <p id="confirmMessage" style="color: #666; margin: 15px 0;"></p>
        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
            <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
            <button class="btn btn-primary" id="confirmBtn" onclick="confirmAction()">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
// Global state
let currentAction = null;
const CSRF_TOKEN = '{{ csrf_token() }}';

// Load antrian list dari API
function loadAntrianList() {
    console.log('[LOAD] Fetching antrian list...');
    
    fetch('{{ route("petugas.loket.list") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[LOAD] Response:', data);
        
        if (!data.success) {
            console.error('[LOAD] API error:', data.message);
            document.getElementById('antrianList').innerHTML = `
                <p style="text-align: center; color: #e74c3c;">
                    <i class="fas fa-exclamation-circle"></i> ${data.message || 'Error loading data'}
                </p>
            `;
            return;
        }
        
        // Update antrian list
        const list = document.getElementById('antrianList');
        if (!data.antrians || data.antrians.length === 0) {
            list.innerHTML = '<p style="text-align: center; color: #999;"><i class="fas fa-inbox"></i> Tidak ada antrian</p>';
        } else {
            list.innerHTML = data.antrians.map(a => `
                <div class="antrian-item ${a.status === 'dipanggil' ? 'dipanggil' : a.status === 'dilayani' ? 'dilayani' : ''}">
                    <div>
                        <strong style="font-size: 18px; color: #3498db;">${a.kode_antrian}</strong>
                        <span style="margin: 0 10px; color: #999;">-</span>
                        <span>${a.layanan?.nama_layanan || 'Unknown'}</span>
                    </div>
                    <span class="status-badge ${a.status}">
                        <i class="fas fa-${a.status === 'menunggu' ? 'clock' : a.status === 'dipanggil' ? 'bell' : 'check'}"></i>
                        ${a.status === 'menunggu' ? 'Menunggu' : a.status === 'dipanggil' ? 'Dipanggil' : a.status === 'dilayani' ? 'Dilayani' : 'Selesai'}
                    </span>
                </div>
            `).join('');
        }
        
        // Update last called
        if (data.last_called) {
            document.getElementById('antrianStatus').textContent = data.last_called.kode_antrian;
            const waktu = new Date(data.last_called.waktu_panggil);
            document.getElementById('antrianWaktu').textContent = waktu.toLocaleTimeString('id-ID');
        }
    })
    .catch(error => {
        console.error('[LOAD] Fetch error:', error);
        document.getElementById('antrianList').innerHTML = `
            <p style="text-align: center; color: #e74c3c;">
                <i class="fas fa-exclamation-circle"></i> Error: ${error.message}
            </p>
        `;
    });
}

// Show confirmation modal
function showConfirm(message, action) {
    console.log('[CONFIRM] Showing modal for action:', action);
    currentAction = action;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmModal').style.display = 'flex';
}

// Close modal
function closeModal() {
    console.log('[MODAL] Closing');
    document.getElementById('confirmModal').style.display = 'none';
    currentAction = null;
}

// Execute action after confirmation
function confirmAction() {
    console.log('[ACTION] Confirming action:', currentAction);
    
    let endpoint = null;
    let message = null;
    
    if (currentAction === 'panggil') {
        endpoint = '{{ route("petugas.loket.panggil") }}';
        message = 'PANGGIL ANTRIAN';
    } else if (currentAction === 'layani') {
        endpoint = '{{ route("petugas.loket.layani") }}';
        message = 'LAYANI';
    } else if (currentAction === 'selesai') {
        endpoint = '{{ route("petugas.loket.selesai") }}';
        message = 'SELESAI';
    } else if (currentAction === 'batalkan') {
        endpoint = '{{ route("petugas.loket.batalkan") }}';
        message = 'BATALKAN';
    }
    
    if (!endpoint) {
        console.error('[ACTION] Unknown action:', currentAction);
        return;
    }
    
    closeModal();
    console.log('[ACTION] Executing POST to:', endpoint);
    
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log('[ACTION] Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[ACTION] Response data:', data);
        
        if (data.success) {
            showAlert('Sukses', `${message} berhasil dilakukan`, 'success');
            
            // Play sound jika panggil
            if (currentAction === 'panggil' && data.antrian) {
                playAnnouncement(data.antrian);
            }
            
            // Reload after 1 second
            setTimeout(() => {
                loadAntrianList();
            }, 1000);
        } else {
            showAlert('Error', data.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(error => {
        console.error('[ACTION] Fetch error:', error);
        showAlert('Error', 'Koneksi error: ' + error.message, 'error');
    });
}

// Button handlers
function pangilAntrian() {
    console.log('[BUTTON] Panggil Antrian clicked');
    showConfirm('Apakah Anda yakin ingin memanggil nomor antrian berikutnya?', 'panggil');
}

function layaniAntrian() {
    console.log('[BUTTON] Layani clicked');
    showConfirm('Apakah antrian sedang Anda layani?', 'layani');
}

function selesaiAntrian() {
    console.log('[BUTTON] Selesai clicked');
    showConfirm('Apakah pelayanan sudah selesai?', 'selesai');
}

function batalkanAntrian() {
    console.log('[BUTTON] Batalkan clicked');
    showConfirm('Apakah Anda yakin ingin membatalkan antrian ini?', 'batalkan');
}

// Play announcement dengan TTS yang lebih reliable
function playAnnouncement(antrian) {
    const kode = antrian.kode_antrian;
    const ruangan = antrian.loket?.nama_loket || 'Ruangan';
    
    console.log('[AUDIO] Playing announcement - Nomor:', kode, 'Ruangan:', ruangan);
    
    // Play notification sound first
    playNotificationSound();
    
    // Wait a bit, then play announcement
    setTimeout(() => {
        // Method 1: Try Web Speech API first
        if (tryWebSpeech(kode, ruangan)) {
            return;
        }
        
        // Method 2: Fallback to Google Translate TTS
        playGoogleTTS(kode, ruangan);
    }, 800);
}

// Try using Web Speech API
function tryWebSpeech(kode, ruangan) {
    if (!('speechSynthesis' in window)) {
        console.log('[AUDIO] Web Speech API not available');
        return false;
    }
    
    try {
        window.speechSynthesis.cancel();
        
        // Split nomor untuk clarity: A001 jadi "A 0 0 1"
        let kodeSplit = '';
        for (let i = 0; i < kode.length; i++) {
            if (i > 0) kodeSplit += ' ';
            kodeSplit += kode[i];
        }
        
        // First message: nomor antrian
        const pesan1 = `Nomor antrian ${kodeSplit}`;
        const utterance1 = new SpeechSynthesisUtterance(pesan1);
        utterance1.lang = 'id-ID';
        utterance1.rate = 0.8;
        utterance1.pitch = 1.0;
        utterance1.volume = 1.0;
        
        // Second message: ruangan
        const pesan2 = `Dimohon menuju ${ruangan}`;
        const utterance2 = new SpeechSynthesisUtterance(pesan2);
        utterance2.lang = 'id-ID';
        utterance2.rate = 0.8;
        utterance2.pitch = 0.95;
        utterance2.volume = 1.0;
        
        // Speak first
        window.speechSynthesis.speak(utterance1);
        
        // Queue second after first finishes
        utterance1.onend = () => {
            setTimeout(() => {
                window.speechSynthesis.speak(utterance2);
            }, 300);
        };
        
        console.log('[AUDIO] Web Speech API used');
        return true;
    } catch (e) {
        console.error('[AUDIO] Web Speech error:', e);
        return false;
    }
}

// Play using Google Translate TTS (more reliable)
function playGoogleTTS(kode, ruangan) {
    try {
        // Format pesan
        let kodeSplit = '';
        for (let i = 0; i < kode.length; i++) {
            if (i > 0) kodeSplit += ' ';
            kodeSplit += kode[i];
        }
        
        // Message 1: nomor antrian
        const text1 = `Nomor antrian ${kodeSplit}`;
        const url1 = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text1)}&tl=id&client=tw-ob`;
        
        // Message 2: ruangan
        const text2 = `Dimohon menuju ${ruangan}`;
        const url2 = `https://translate.google.com/translate_tts?ie=UTF-8&q=${encodeURIComponent(text2)}&tl=id&client=tw-ob`;
        
        console.log('[AUDIO] Playing TTS message 1:', text1);
        
        const audio1 = new Audio(url1);
        audio1.volume = 1.0;
        
        // Play first message
        audio1.play().then(() => {
            console.log('[AUDIO] Message 1 playing');
        }).catch(e => {
            console.error('[AUDIO] Play error:', e);
        });
        
        // After first audio ends, play second
        audio1.onended = () => {
            console.log('[AUDIO] Message 1 ended, playing message 2:', text2);
            setTimeout(() => {
                const audio2 = new Audio(url2);
                audio2.volume = 1.0;
                audio2.play().catch(e => {
                    console.error('[AUDIO] Message 2 play error:', e);
                });
            }, 300);
        };
        
        console.log('[AUDIO] Google Translate TTS used');
    } catch (err) {
        console.error('[AUDIO] Google TTS error:', err);
    }
}

// Notification sound using Web Audio API with multiple beeps
function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // Play 3 beeps in sequence
        const playBeep = (delay, frequency, duration) => {
            setTimeout(() => {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = frequency;
                oscillator.type = 'sine';
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + duration);
            }, delay);
        };
        
        // Beep pattern: low, high, low (to catch attention)
        playBeep(0, 600, 0.2);      // First beep: 600Hz
        playBeep(300, 800, 0.2);    // Second beep: 800Hz
        playBeep(600, 600, 0.3);    // Third beep: 600Hz (longer)
        
        console.log('[AUDIO] Notification beeps played');
    } catch (e) {
        console.error('[AUDIO] Beep error:', e);
    }
}

// Show alert
function showAlert(title, message, type = 'info') {
    console.log('[ALERT]', type.toUpperCase(), '-', message);
    
    const alertDiv = document.createElement('div');
    let color = '#3498db';
    let icon = 'info-circle';
    
    if (type === 'success') {
        color = '#27ae60';
        icon = 'check-circle';
    } else if (type === 'error') {
        color = '#e74c3c';
        icon = 'exclamation-circle';
    }
    
    alertDiv.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 10000;
        min-width: 350px;
        text-align: center;
        animation: slideUp 0.3s ease;
    `;
    
    alertDiv.innerHTML = `
        <i class="fas fa-${icon}" style="font-size: 40px; color: ${color}; margin-bottom: 15px; display: block;"></i>
        <h3 style="color: #2c3e50; margin: 10px 0; font-size: 20px;">${title}</h3>
        <p style="color: #666; margin: 15px 0; font-size: 14px;">${message}</p>
        <button onclick="this.parentElement.remove()" style="background: ${color}; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: 600;">OK</button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentElement) alertDiv.remove();
    }, 3000);
}

// Add animation CSS
if (!document.getElementById('loketStyles')) {
    const style = document.createElement('style');
    style.id = 'loketStyles';
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
    `;
    document.head.appendChild(style);
}

// Initialize on page load
console.log('[INIT] Petugas Loket page loaded');
loadAntrianList();

// Auto-refresh setiap 5 detik
const refreshInterval = setInterval(loadAntrianList, 5000);

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(refreshInterval);
    if (window.speechSynthesis) {
        window.speechSynthesis.cancel();
    }
});

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

console.log('[INIT] All handlers registered');
</script>
@endsection
