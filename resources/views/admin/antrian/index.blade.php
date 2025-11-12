@extends('layouts.app')
@section('title', 'Daftar Riwayat Antrian')

@section('styles')
<style>
/* Filter Card */
.filter-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: flex-end;
}
.form-group { margin: 0; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; }
.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.btn { 
    padding: 10px 15px; 
    border: none; 
    border-radius: 4px; 
    cursor: pointer; 
    font-weight: 600; 
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 40px; /* Samakan tinggi dengan input */
}
.btn-primary { background: #3498db; color: white; }
.btn-primary:hover { background: #2980b9; }
.btn-secondary { background: #95a5a6; color: white; text-decoration: none; }
.btn-secondary:hover { background: #7f8c8d; }

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

/* Status Badges */
.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-menunggu { background-color: #fdf2e9; color: #f39c12; }
.status-dipanggil { background-color: #eaf2f8; color: #3498db; }
.status-dilayani { background-color: #eafaf1; color: #27ae60; }
.status-selesai { background-color: #ecf0f1; color: #7f8c8d; }
.status-batal { background-color: #fdedeb; color: #e74c3c; }

/* Pagination */
.pagination {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
}
.pagination li {
    margin: 0 3px;
}
.pagination li a, .pagination li span {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #3498db;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: 0.2s;
}
.pagination li a:hover {
    background: #f4f4f4;
}
.pagination li.active span {
    background: #3498db;
    color: white;
    border-color: #3498db;
}
.pagination li.disabled span {
    color: #999;
    background: #f9f9f9;
}
</style>
@endsection

@section('content')

<div class="filter-card">
    <form action="{{ route('admin.antrian.index') }}" method="GET" class="filter-form">
        <div class="form-group">
            <label for="tanggal"><i class="fas fa-calendar-alt"></i> Filter Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}">
        </div>
        
        <div class="form-group">
            <label for="layanan_id"><i class="fas fa-concierge-bell"></i> Filter Layanan</label>
            <select id="layanan_id" name="layanan_id">
                <option value="">-- Semua Layanan --</option>
                @foreach($layanans as $layanan)
                    <option value="{{ $layanan->id }}" {{ $filters['layanan_id'] == $layanan->id ? 'selected' : '' }}>
                        {{ $layanan->nama_layanan }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="status"><i class="fas fa-info-circle"></i> Filter Status</label>
            <select id="status" name="status">
                <option value="">-- Semua Status --</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $filters['status'] == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>&nbsp;</label> <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.antrian.index') }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">
                    <i class="fas fa-sync-alt"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Layanan</th>
                <th>Loket</th>
                <th>Waktu Ambil</th>
                <th>Waktu Panggil</th>
                <th>Waktu Selesai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($antrians as $antrian)
            <tr>
                <td><strong>{{ $antrian->kode_antrian }}</strong></td>
                <td>{{ $antrian->layanan->nama_layanan ?? 'N/A' }}</td>
                <td>{{ $antrian->loket->nama_loket ?? 'N/A' }}</td>
                <td>{{ $antrian->waktu_ambil->format('d-m-Y H:i:s') }}</td>
                <td>{{ $antrian->waktu_panggil ? $antrian->waktu_panggil->format('H:i:s') : 'N/A' }}</td>
                <td>{{ $antrian->waktu_selesai ? $antrian->waktu_selesai->format('H:i:s') : 'N/A' }}</td>
                <td>
                    <span class="status-badge status-{{ $antrian->status }}">
                        {{ $antrian->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    Tidak ada data antrian untuk filter yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="padding-top: 20px;">
        {{ $antrians->links() }}
    </div>
</div>

@endsection

@section('scripts')
<script>
// Tidak ada JavaScript khusus yang diperlukan untuk halaman ini.
// Filter dan pagination ditangani oleh Laravel (server-side).
</script>
@endsection