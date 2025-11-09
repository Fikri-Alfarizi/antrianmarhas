@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-top: 4px solid; transition: all 0.2s; }
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
.stat-card i { font-size: 36px; margin-bottom: 12px; }
.stat-card h3 { font-size: 32px; margin: 10px 0; font-weight: 700; }
.stat-card p { color: #666; font-size: 13px; margin: 0; }
.stat-primary { color: #3498db; border-top-color: #3498db; }
.stat-success { color: #27ae60; border-top-color: #27ae60; }
.stat-warning { color: #f39c12; border-top-color: #f39c12; }
.stat-danger { color: #e74c3c; border-top-color: #e74c3c; }
</style>
@endsection

@section('content')
<div class="card">
    <h2><i class="fas fa-chart-line"></i> Dashboard Admin</h2>
    <p style="margin: 0; color: #666;">Selamat datang, <strong>{{ Auth::user()->name }}</strong></p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-clipboard-list stat-primary"></i>
        <h3>{{ $statistik['total_antrian'] }}</h3>
        <p>Total Antrian Hari Ini</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-clock stat-warning"></i>
        <h3>{{ $statistik['menunggu'] + $statistik['dipanggil'] }}</h3>
        <p>Sedang Menunggu/Dipanggil</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-check-circle stat-success"></i>
        <h3>{{ $statistik['selesai'] }}</h3>
        <p>Sudah Dilayani</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-times-circle stat-danger"></i>
        <h3>{{ $statistik['batal'] }}</h3>
        <p>Dibatalkan</p>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-chart-line"></i> Statistik Per Layanan</h3>
    <table>
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Total</th>
                <th>Menunggu</th>
                <th>Dilayani</th>
                <th>Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statistikLayanan as $stat)
            <tr>
                <td>{{ $stat->nama_layanan }}</td>
                <td>{{ $stat->total }}</td>
                <td><span class="badge badge-warning">{{ $stat->menunggu }}</span></td>
                <td><span class="badge badge-info">{{ $stat->dilayani }}</span></td>
                <td><span class="badge badge-success">{{ $stat->selesai }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
