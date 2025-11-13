@extends('layouts.app')
@section('title', 'Manajemen Layanan')

@section('styles')
<style>
/* ========================================================= */
/* --- 1. Base Styling (Diambil dari file Anda) --- */
/* ========================================================= */
.card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); padding: 24px;
    transition: all 0.2s; margin-bottom: 24px;
}
.table-header-card {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px; padding: 15px 24px; background: #ffffff;
    border: 1px solid #e2e8f0; border-radius: 16px;
}
.table-header-card h2 { margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; }

/* Tombol Tambah (menggunakan style btn-submit dari file Anda) */
.btn-add-new {
    padding: 10px 18px; border: none; border-radius: 12px; font-size: 14px; 
    font-weight: 700; cursor: pointer; transition: all 0.25s;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white; display: flex; align-items: center; gap: 10px;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    text-decoration: none; /* Penting untuk tag <a> */
}
.btn-add-new:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}

/* ========================================================= */
/* --- 2. Table & Action Styling (Diambil dari file Anda) --- */
/* ========================================================= */
.styled-table {
    width: 100%; border-collapse: collapse; margin: 0; font-size: 0.9em;
    min-width: 800px; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 12px;
}
.styled-table thead tr {
    background-color: #f8fafc; color: #64748b; text-align: left;
    font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
}
.styled-table th,
.styled-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
.styled-table tbody tr:hover { background-color: #f8fafc; }
.styled-table tbody tr:last-child td { border-bottom: none; }

.action-buttons { display: flex; gap: 8px; }
.btn { 
    padding: 8px 12px; border: none; border-radius: 8px; cursor: pointer; 
    font-weight: 600; font-size: 13px; display: inline-flex;
    align-items: center; gap: 5px; transition: all 0.2s;
    text-decoration: none; /* Penting untuk tag <a> */
}
.btn-edit { background: #f59e0b; color: white; }
.btn-edit:hover { background: #d97706; }
.btn-danger { background: #ef4444; color: white; }
.btn-danger:hover { background: #dc2626; }

.status-badge {
    padding: 5px 10px; border-radius: 15px; font-size: 12px;
    font-weight: 600; text-transform: uppercase;
}
.status-aktif { background-color: #dcfce7; color: #16a34a; }
.status-nonaktif { background-color: #fef3c7; color: #f59e0b; }

/* Alerts */
.alert {
    padding: 15px; margin-bottom: 24px; border-radius: 12px; display: flex; 
    align-items: flex-start; gap: 10px; font-weight: 500; 
}
.alert-success { color: #065f46; background-color: #d1fae5; border-color: #a7f3d0; }
.alert-danger { color: #991b1b; background-color: #fee2e2; border-color: #fca5a5; }

@media (max-width: 768px) {
    .styled-table { min-width: 600px; }
    .action-buttons { flex-direction: column; gap: 5px; }
}
</style>
@endsection

@section('content')

<div class="table-header-card">
    <h2><i class="fas fa-server"></i> Daftar Layanan</h2>
    <a href="{{ route('admin.layanan.create') }}" class="btn-add-new">
        <i class="fas fa-plus"></i> Tambah Layanan Baru
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle" style="font-size: 20px;"></i> <div>{{ session('success') }}</div>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i> <div>{{ session('error') }}</div>
    </div>
@endif

<div class="card" style="padding: 0;">
    <div style="overflow-x: auto;">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Nama Layanan</th>
                    <th>Prefix</th>
                    <th>Digit</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($layanans as $layanan)
                <tr>
                    <td><strong>{{ $layanan->nama_layanan }}</strong></td>
                    <td>{{ $layanan->prefix }}</td>
                    <td>{{ $layanan->digit }}</td>
                    <td>
                        @if($layanan->status == 'aktif')
                            <span class="status-badge status-aktif">Aktif</span>
                        @else
                            <span class="status-badge status-nonaktif">Nonaktif</span>
                        @endif
                    </td>
                    <td class="action-buttons">
                        <!-- Tombol Edit mengarah ke halaman edit -->
                        <a href="{{ route('admin.layanan.edit', $layanan->id) }}" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <!-- Tombol Hapus (Konfirmasi langsung dengan JS browser) -->
                        <form action="{{ route('admin.layanan.destroy', $layanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan {{ $layanan->nama_layanan }}?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        Belum ada data layanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
{{-- Tidak ada script modal --}}
@endsection