@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
/* Grid untuk Stat Card dan Konten */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.main-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr; /* 2/3 untuk tabel, 1/3 untuk aktivitas */
    gap: 20px;
    margin-top: 20px;
}

/* Stat Card */
.stat-card {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    border-left: 5px solid #3498db;
}
.stat-card i {
    font-size: 36px;
    color: #3498db;
    width: 60px;
    height: 60px;
    display: grid;
    place-items: center;
    background-color: #f0f6fa;
    border-radius: 50%;
}
.stat-card .info h3 {
    font-size: 32px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}
.stat-card .info p {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}
/* Warna Card */
.stat-card.green { border-color: #27ae60; }
.stat-card.green i { color: #27ae60; background-color: #eafaf1; }
.stat-card.orange { border-color: #f39c12; }
.stat-card.orange i { color: #f39c12; background-color: #fef8e7; }
.stat-card.red { border-color: #e74c3c; }
.stat-card.red i { color: #e74c3c; background-color: #fdedeb; }

/* Tabel */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.9em;
    min-width: 400px;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.styled-table thead tr {
    background-color: #2c3e50;
    color: #ffffff;
    text-align: left;
}
.styled-table th,
.styled-table td {
    padding: 12px 15px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    background: #fff;
}
.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #2c3e50;
}
.styled-table tbody tr:hover {
    background-color: #f8f9fa;
}
.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-aktif {
    background-color: #eafaf1;
    color: #27ae60;
}
.status-tutup {
    background-color: #fdedeb;
    color: #e74c3c;
}

/* Activity Feed */
.activity-feed {
    list-style: none;
    padding: 0;
}
.activity-feed .feed-item {
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}
.activity-feed .feed-item:last-child {
    border-bottom: none;
}
.activity-feed .feed-icon {
    font-size: 16px;
    color: #95a5a6;
    padding-top: 4px;
}
.activity-feed .feed-content p {
    margin: 0;
    font-size: 14px;
    color: #333;
    line-height: 1.4;
}
.activity-feed .feed-content p strong {
    color: #2c3e50;
}
.activity-feed .feed-content span {
    font-size: 12px;
    color: #95a5a6;
}

/* Responsif */
@media (max-width: 1200px) {
    .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
    .main-content-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .stat-card { padding: 15px; }
    .stat-card .info h3 { font-size: 28px; }
}
</style>
@endsection

@section('content')

<div class="dashboard-grid">
    <div class="stat-card">
        <i class="fas fa-ticket-alt"></i>
        <div class="info">
            <h3>{{ $stats['total_antrian'] ?? 0 }}</h3>
            <p>Total Antrian Hari Ini</p>
        </div>
    </div>
    <div class="stat-card green">
        <i class="fas fa-check-circle"></i>
        <div class="info">
            <h3>{{ $stats['selesai'] ?? 0 }}</h3>
            <p>Antrian Selesai</p>
        </div>
    </div>
    <div class="stat-card orange">
        <i class="fas fa-clock"></i>
        <div class="info">
            <h3>{{ $stats['menunggu'] ?? 0 }}</h3>
            <p>Antrian Menunggu</p>
        </div>
    </div>
    <div class="stat-card red">
        <i class="fas fa-times-circle"></i>
        <div class="info">
            <h3>{{ $stats['batal'] ?? 0 }}</h3>
            <p>Antrian Batal</p>
        </div>
    </div>
</div>

<div class="main-content-grid">

    <div class="card">
        <h3 style="margin-top:0; margin-bottom: 15px;"><i class="fas fa-door-open"></i> Status Loket Saat Ini</h3>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Nama Loket</th>
                    <th>Layanan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lokets as $loket)
                <tr>
                    <td><strong>{{ $loket->nama_loket }}</strong></td>
                    <td>{{ $loket->layanan->nama_layanan ?? 'N/A' }}</td>
                    <td>
                        @if($loket->status == 'aktif')
                            <span class="status-badge status-aktif"><i class="fas fa-check"></i> Aktif</span>
                        @else
                            <span class="status-badge status-tutup"><i class="fas fa-lock"></i> Tutup</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">Belum ada loket dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <h3 style="margin-top: 30px; margin-bottom: 15px;"><i class="fas fa-users"></i> Daftar Menunggu (10 Teratas)</h3>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Kode Antrian</th>
                    <th>Layanan</th>
                    <th>Waktu Ambil</th>
                </tr>
            </thead>
            <tbody>
                @forelse($antrianMenunggu as $antrian)
                <tr>
                    <td><strong>{{ $antrian->kode_antrian }}</strong></td>
                    <td>{{ $antrian->layanan->nama_layanan ?? 'N/A' }}</td>
                    <td>{{ $antrian->waktu_ambil->format('H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">Tidak ada antrian menunggu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0; margin-bottom: 15px;"><i class="fas fa-history"></i> Aktivitas Terbaru</h3>
        <ul class="activity-feed">
            @forelse($activities as $activity)
            <li class="feed-item">
                <i class="fas fa-info-circle feed-icon"></i>
                <div class="feed-content">
                    <p><strong>{{ $activity->user->name ?? 'Sistem' }}</strong> {{ $activity->aktivitas }}</p>
                    <span>
                        @if(is_object($activity->waktu) && method_exists($activity->waktu, 'diffForHumans'))
                            {{ $activity->waktu->diffForHumans() }}
                        @else
                            {{ $activity->waktu }}
                        @endif
                    </span>
                </div>
            </li>
            @empty
            <li class="feed-item">
                <i class="fas fa-info-circle feed-icon"></i>
                <div class="feed-content">
                    <p>Belum ada aktivitas.</p>
                </div>
            </li>
            @endforelse
        </ul>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Dashboard tidak memerlukan JavaScript khusus saat ini
// Anda bisa menambahkan logika refresh otomatis di sini jika perlu
</script>
@endsection