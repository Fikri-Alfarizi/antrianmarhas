@extends('layouts.app')
@section('title', 'Loket Pemanggilan')

@section('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0; }
.stat-box { padding: 15px; border-radius: 5px; text-align: center; color: white; }
.stat-box h3 { font-size: 36px; margin: 10px 0; }
.stat-box p { font-size: 14px; }
.action-buttons { text-align: center; margin: 30px 0; }
.btn-lg { font-size: 20px; padding: 20px 40px; }
.btn-sm { padding: 5px 10px; font-size: 12px; }
.performance-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
.performance-card { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #3498db; }
.performance-card h4 { margin: 0 0 10px 0; color: #333; }
.performance-card .metric { display: flex; justify-content: space-between; margin: 8px 0; font-size: 13px; }
.progress-bar { width: 100%; height: 20px; background: #ecf0f1; border-radius: 3px; margin: 8px 0; overflow: hidden; }
.progress-fill { height: 100%; background: #27ae60; text-align: center; color: white; font-size: 11px; line-height: 20px; }
</style>
@endsection

@section('content')
<div class="card">
    <h2>
        <i class="fas fa-door-open"></i> {{ $loket->nama_loket }} - {{ $loket->layanan->nama_layanan }}
    </h2>
    
    <div class="stats-grid">
        <div class="stat-box" style="background: #3498db;">
            <h3>{{ $statistik['total'] }}</h3>
            <p><i class="fas fa-clipboard-list"></i> Total Hari Ini</p>
        </div>
        <div class="stat-box" style="background: #f39c12;">
            <h3>{{ $statistik['menunggu'] }}</h3>
            <p><i class="fas fa-clock"></i> Menunggu</p>
        </div>
        <div class="stat-box" style="background: #27ae60;">
            <h3>{{ $statistik['selesai'] }}</h3>
            <p><i class="fas fa-check-circle"></i> Selesai</p>
        </div>
    </div>
    
    <!-- Performance Dashboard -->
    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ecf0f1;">
        <h3><i class="fas fa-chart-bar"></i> Performance Hari Ini</h3>
        
        <div class="performance-grid">
            <!-- Goal Progress -->
            <div class="performance-card" style="border-left-color: #9b59b6;">
                <h4><i class="fas fa-target"></i> Target Harian</h4>
                <div class="metric">
                    <span>Target:</span>
                    <strong>{{ $goalProgress['target'] ?? 50 }} pelanggan</strong>
                </div>
                <div class="metric">
                    <span>Tercapai:</span>
                    <strong>{{ $goalProgress['completed'] ?? 0 }} pelanggan</strong>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, $goalProgress['progress_percentage'] ?? 0) }}%;">
                        {{ min(100, $goalProgress['progress_percentage'] ?? 0) }}%
                    </div>
                </div>
                <div class="metric" style="margin-top: 10px;">
                    <span style="color: #555;">Status:</span>
                    <strong style="color: {{ $goalProgress['status'] === 'achieved' ? '#27ae60' : ($goalProgress['status'] === 'on_track' ? '#f39c12' : '#e74c3c') }};">
                        {{ ucfirst(str_replace('_', ' ', $goalProgress['status'] ?? 'behind')) }}
                    </strong>
                </div>
            </div>
            
            <!-- Service Time Stats -->
            <div class="performance-card" style="border-left-color: #3498db;">
                <h4><i class="fas fa-hourglass-half"></i> Waktu Pelayanan</h4>
                <div class="metric">
                    <span>Rata-rata:</span>
                    <strong>{{ $personalStats['avg_service_time'] ?? 0 }} menit</strong>
                </div>
                <div class="metric">
                    <span>Total:</span>
                    <strong>{{ $personalStats['total_service_time'] ?? 0 }} menit</strong>
                </div>
                <div class="metric">
                    <span>Efficiency:</span>
                    <strong style="color: #27ae60;">{{ $personalStats['efficiency'] ?? 0 }}%</strong>
                </div>
            </div>
            
            <!-- Monthly Summary -->
            <div class="performance-card" style="border-left-color: #27ae60;">
                <h4><i class="fas fa-calendar"></i> Bulan Ini</h4>
                <div class="metric">
                    <span>Total Pelanggan:</span>
                    <strong>{{ $monthlyPerformance['total_antrian'] ?? 0 }}</strong>
                </div>
                <div class="metric">
                    <span>Selesai:</span>
                    <strong style="color: #27ae60;">{{ $monthlyPerformance['total_selesai'] ?? 0 }}</strong>
                </div>
                <div class="metric">
                    <span>Efisiensi:</span>
                    <strong style="color: #3498db;">{{ $monthlyPerformance['efficiency'] ?? 0 }}%</strong>
                </div>
            </div>
        </div>
    </div>
    
    <div class="action-buttons">
        <button onclick="panggilAntrian()" class="btn btn-primary btn-lg">
            <i class="fas fa-phone"></i> PANGGIL ANTRIAN
        </button>
        <button onclick="tutupLoket()" class="btn btn-danger" style="padding: 15px 30px; margin-left: 10px;">
            <i class="fas fa-door-closed"></i> TUTUP LOKET
        </button>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-list"></i> Antrian Aktif</h3>
    <table>
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Status</th>
                <th>Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="antrianList">
            @forelse($antrianAktif as $antrian)
            <tr id="antrian-{{ $antrian->id }}">
                <td><strong>{{ $antrian->kode_antrian }}</strong></td>
                <td>
                    @if($antrian->status === 'menunggu')
                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu</span>
                    @elseif($antrian->status === 'dipanggil')
                        <span class="badge badge-info"><i class="fas fa-phone"></i> Dipanggil</span>
                    @else
                        <span class="badge badge-success"><i class="fas fa-user-nurse"></i> Dilayani</span>
                    @endif
                </td>
                <td>{{ $antrian->waktu_ambil->format('H:i') }}</td>
                <td>
                    @if($antrian->status === 'dipanggil')
                    <button onclick="layaniAntrian({{ $antrian->id }})" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Layani
                    </button>
                    @elseif($antrian->status === 'dilayani')
                    <button onclick="selesaiAntrian({{ $antrian->id }})" class="btn btn-primary btn-sm">
                        <i class="fas fa-check-double"></i> Selesai
                    </button>
                    @endif
                    <button onclick="batalAntrian({{ $antrian->id }})" class="btn btn-danger btn-sm">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center;">Belum ada antrian</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
function panggilAntrian() {
    fetch('{{ route("petugas.loket.panggil") }}', { 
        method: 'POST', 
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
            'Content-Type': 'application/json' 
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Antrian ' + data.antrian.kode_antrian + ' dipanggil!');
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function layaniAntrian(id) {
    fetch('{{ route("petugas.loket.layani") }}', { 
        method: 'POST', 
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
            'Content-Type': 'application/json' 
        }, 
        body: JSON.stringify({ antrian_id: id })
    })
    .then(() => location.reload());
}

function selesaiAntrian(id) {
    fetch('{{ route("petugas.loket.selesai") }}', { 
        method: 'POST', 
        headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}', 
            'Content-Type': 'application/json' 
        }, 
        body: JSON.stringify({ antrian_id: id })
    })
    .then(() => location.reload());
}

function batalAntrian(id) {
    if (confirm('Yakin batalkan antrian ini?')) {
        fetch('{{ route("petugas.loket.batal") }}', { 
            method: 'POST', 
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json' 
            }, 
            body: JSON.stringify({ antrian_id: id })
        })
        .then(() => location.reload());
    }
}

function tutupLoket() {
    if (confirm('Yakin ingin menutup loket?')) {
        fetch('{{ route("petugas.loket.tutup") }}', { 
            method: 'POST', 
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(() => {
            alert('Loket telah ditutup');
            location.reload();
        });
    }
}
</script>
@endsection
