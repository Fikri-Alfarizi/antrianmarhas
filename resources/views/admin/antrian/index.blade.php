@extends('layouts.app')
@section('title', 'Daftar Riwayat Antrian')

@section('styles')
<style>
/* --- Page Header & Card Base (Konsisten dengan UI Modern) --- */
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

/* --- Filter Card --- */
.filter-card {
    padding: 24px; /* Konsisten dengan .card */
}
.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px; /* Konsisten */
    align-items: flex-end;
}
.form-group { margin: 0; }
.form-group label { 
    display: block; 
    margin-bottom: 6px; 
    font-weight: 600; 
    font-size: 12px; /* Konsisten */
    color: #475569;
}
.form-group input, 
.form-group select {
    width: 100%;
    padding: 10px 14px; /* Konsisten */
    border: 1px solid #e2e8f0; /* Konsisten */
    border-radius: 10px; /* Konsisten */
    font-size: 14px;
    background: #ffffff;
    font-family: 'Inter', sans-serif;
}
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #3b82f6; 
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

/* --- Tombol Filter & Reset (Gaya Konsisten) --- */
.btn { 
    padding: 10px 15px; 
    border: none; 
    border-radius: 12px; /* Konsisten */
    cursor: pointer; 
    font-weight: 700; 
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 44px; /* Samakan tinggi dengan input */
    transition: all 0.2s;
}
.btn-primary { 
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); 
    color: white; 
}
.btn-primary:hover { 
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}
.btn-secondary { 
    background: #e2e8f0; 
    color: #475569; 
    text-decoration: none; 
}
.btn-secondary:hover { 
    background: #cbd5e1; 
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* --- Table Styles (Diadaptasi dari Dashboard) --- */
.table-wrapper {
    overflow-x: auto;
}
.styled-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    font-size: 0.9em;
    min-width: 800px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
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
.styled-table tbody tr:hover {
    background-color: #f8fafc;
}
.styled-table tbody tr:last-child td {
    border-bottom: none;
}

/* --- Status Badges (Gaya Modern) --- */
.status-badge {
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize; /* Diubah dari uppercase agar lebih enak dilihat */
}
.status-menunggu { background-color: #fef3c7; color: #b45309; }
.status-dipanggil { background-color: #dbeafe; color: #2563eb; }
.status-dilayani { background-color: #dcfce7; color: #166534; }
.status-selesai { background-color: #f1f5f9; color: #64748b; }
.status-batal { background-color: #fee2e2; color: #ef4444; }

/* --- Pagination (Gaya Modern) --- */
.pagination-wrapper nav {
    display: flex;
    justify-content: flex-end;
    padding: 15px 0 5px 0;
}
.pagination-wrapper nav svg {
    height: 18px;
    width: 18px;
}
.pagination-wrapper nav > div:first-child {
    display: none; /* Sembunyikan info "Showing 1 to 10..." */
}
.pagination-wrapper nav > div:last-child {
    display: flex;
    gap: 5px;
}
.pagination-wrapper span, 
.pagination-wrapper a {
    min-width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
    transition: all 0.2s;
    text-decoration: none;
}
.pagination-wrapper a:hover {
    background: #f1f5f9;
}
.pagination-wrapper span.current {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    font-weight: 600;
}
.pagination-wrapper span.disabled {
    color: #cbd5e1;
}

/* Responsive Table */
@media (max-width: 768px) {
    .card { padding: 15px; }
    .table-wrapper {
        overflow-x: auto;
    }
    .styled-table {
        min-width: 700px; /* Minimal width untuk mobile scroll */
    }
}
</style>
@endsection

@section('content')

<div class="page-header">
    <h1 class="page-title">Daftar Riwayat Antrian</h1>
    <p class="page-subtitle">‎ </p>
</div>

<div class="card filter-card">
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
                    <option value="{{ $layanan->id }}" {{ ($filters['layanan_id'] ?? null) == $layanan->id ? 'selected' : '' }}>
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
                    <option value="{{ $status }}" {{ ($filters['status'] ?? null) == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <div style="display: flex; gap: 10px;">
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

<div class="card" style="padding: 0;">
    <div class="table-wrapper">
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
                            {{ ucfirst($antrian->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fa-regular fa-folder-open" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        Tidak ada data antrian untuk filter yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="pagination-wrapper" style="padding: 0 15px;">
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