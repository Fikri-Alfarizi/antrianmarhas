@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
/* ========================================================= */
/* --- 1. Base & Layout Styling (Modern) --- */
/* ========================================================= */
.page-header { margin-bottom: 32px; }
.page-title { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; }
.page-subtitle { font-size: 14px; color: #64748b; margin: 0; font-weight: 500; }

.card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 24px;
    transition: all 0.2s;
    margin-bottom: 24px;
}
.card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.card-title {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ========================================================= */
/* --- 2. Grid & Stat Card Styling (Sesuai Permintaan) --- */
/* ========================================================= */

/* Grid untuk 4 Card Statistik */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 24px;
}

/* Stat Card (UI Lama di-modernisasi) */
.stat-card {
    /* Menggunakan base .card style, tapi dimodifikasi */
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
}
.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}
.stat-card .info h3 {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 2px 0;
}
.stat-card .info p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    font-weight: 500;
}

/* Warna Card Modern (Primary, Success, Warning, Danger) */
.stat-card.primary {
    border-left: 5px solid #3b82f6;
}
.stat-card.primary .stat-icon {
    background: #dbeafe; /* blue-100 */
    color: #3b82f6; /* blue-500 */
}
.stat-card.success {
    border-left: 5px solid #10b981;
}
.stat-card.success .stat-icon {
    background: #dcfce7; /* green-100 */
    color: #10b981; /* green-500 */
}
.stat-card.warning {
    border-left: 5px solid #f59e0b;
}
.stat-card.warning .stat-icon {
    background: #fef3c7; /* amber-100 */
    color: #f59e0b; /* amber-500 */
}
.stat-card.danger {
    border-left: 5px solid #ef4444;
}
.stat-card.danger .stat-icon {
    background: #fee2e2; /* red-100 */
    color: #ef4444; /* red-500 */
}

/* ========================================================= */
/* --- 3. Main Content Grid & Components (Modern) --- */
/* ========================================================= */
.main-content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}

/* Tabel (Diambil dari Loket/Layanan) */
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.9em;
    min-width: 400px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
.styled-table thead tr {
    background-color: #f8fafc;
    color: #64748b;
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.styled-table th,
.styled-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e2e8f0;
}
.styled-table tbody tr:hover { background-color: #f8fafc; }
.styled-table tbody tr:last-child td { border-bottom: none; }

/* Status Badges (Modern) */
.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-aktif {
    background-color: #dcfce7;
    color: #16a34a;
}
.status-tutup {
    background-color: #fee2e2;
    color: #ef4444;
}

/* Activity Feed (Modern) */
.activity-feed {
    list-style: none;
    padding: 0;
}
.activity-feed .feed-item {
    display: flex;
    gap: 15px;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}
.activity-feed .feed-item:last-child {
    border-bottom: none;
}
.activity-feed .feed-icon {
    font-size: 14px;
    color: #64748b;
    padding-top: 4px;
}
.activity-feed .feed-content p {
    margin: 0;
    font-size: 14px;
    color: #333;
    line-height: 1.4;
}
.activity-feed .feed-content p strong {
    color: #0f172a;
}
.activity-feed .feed-content span {
    font-size: 12px;
    color: #94a3b8;
}

/* Responsive */
@media (max-width: 1200px) {
    .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
    .main-content-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .dashboard-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">‎ </p>
</div>

<div class="dashboard-grid">
    <div class="card stat-card primary">
        <div class="stat-icon">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="info">
            <h3>{{ $stats['total_antrian'] ?? 0 }}</h3>
            <p>Total Antrian Hari Ini</p>
        </div>
    </div>
    <div class="card stat-card success">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="info">
            <h3>{{ $stats['selesai'] ?? 0 }}</h3>
            <p>Antrian Selesai</p>
        </div>
    </div>
    <div class="card stat-card warning">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="info">
            <h3>{{ $stats['menunggu'] ?? 0 }}</h3>
            <p>Antrian Menunggu</p>
        </div>
    </div>
    <div class="card stat-card danger">
        <div class="stat-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="info">
            <h3>{{ $stats['batal'] ?? 0 }}</h3>
            <p>Antrian Batal</p>
        </div>
    </div>
</div>

<div class="main-content-grid">

    <div class="card">
        <h3 class="card-title"><i class="fas fa-door-open"></i> Status Loket Saat Ini</h3>
        <div style="overflow-x: auto;">
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
                        <td colspan="3" style="text-align: center; padding: 20px; color: #64748b;">Belum ada loket dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <h3 class="card-title" style="margin-top: 30px;"><i class="fas fa-users"></i> Daftar Menunggu (10 Teratas)</h3>
        <div style="overflow-x: auto;">
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
                        <td colspan="3" style="text-align: center; padding: 20px; color: #64748b;">Tidak ada antrian menunggu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title"><i class="fas fa-history"></i> Aktivitas Terbaru</h3>
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
</script>
@endsection