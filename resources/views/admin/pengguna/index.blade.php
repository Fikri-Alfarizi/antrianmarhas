@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('styles')
<style>
/* ========================================================= */
/* --- 1. Base Styling (Konsisten dengan App Layout) --- */
/* ========================================================= */
.card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); padding: 24px;
    margin-bottom: 24px;
}
.table-header-card {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px; padding: 15px 24px; background: #ffffff;
    border: 1px solid #e2e8f0; border-radius: 16px;
}
.table-header-card h2 { margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; }
.btn-submit-header { /* Style untuk Tombol Tambah Pengguna Baru */
    padding: 10px 18px; border: none; border-radius: 12px; font-size: 14px; 
    font-weight: 700; cursor: pointer; transition: all 0.25s;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white; display: flex; align-items: center; gap: 8px;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    text-decoration: none; /* Penting untuk tag <a> */
}
.btn-submit-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
}


/* ========================================================= */
/* --- 2. Table & User Info Styling (Dari UI Desired) --- */
/* ========================================================= */

.table-wrapper { overflow-x: auto; }
.styled-table {
    width: 100%; min-width: 800px; border-collapse: collapse; margin: 0; font-size: 0.9em;
    overflow: hidden; border: 1px solid #e2e8f0; border-radius: 12px;
}
.styled-table thead tr {
    background-color: #f8fafc; color: #64748b; text-align: left;
    font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
}
.styled-table th, .styled-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
.styled-table tbody tr:hover { background-color: #f8fafc; }
.styled-table tbody tr:last-child td { border-bottom: none; }

/* User Info Styling */
.user-info { display: flex; align-items: center; gap: 12px; }
.user-avatar { 
    width: 36px; height: 36px; border-radius: 50%; background: #3b82f6; 
    display: flex; align-items: center; justify-content: center; color: white; 
    font-weight: 600; font-size: 14px; flex-shrink: 0; 
}
.user-details { display: flex; flex-direction: column; }
.user-name { font-weight: 600; color: #0f172a; }
.user-username { font-size: 12px; color: #64748b; }

/* Badge Styles */
.status-badge {
    padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; 
    text-transform: uppercase;
}
.status-admin { background-color: #fecaca; color: #991b1b; }
.status-operator { background-color: #dbeafe; color: #1e40af; }


/* Tombol Aksi */
.action-buttons { display: flex; gap: 8px; }
.btn { 
    padding: 8px 12px; border: none; border-radius: 8px; cursor: pointer; 
    font-weight: 600; font-size: 13px; display: inline-flex; align-items: center;
    gap: 5px; transition: all 0.2s; color: white; text-decoration: none;
}
.btn-edit { background: #f59e0b; }
.btn-edit:hover { background: #d97706; }
.btn-danger { background: #ef4444; }
.btn-danger:hover { background: #dc2626; }


/* Alerts */
.alert { padding: 15px; margin-bottom: 24px; border-radius: 12px; display: flex; align-items: flex-start; gap: 10px; font-weight: 500; }
.alert-success { color: #065f46; background-color: #d1fae5; border-color: #a7f3d0; }
.alert-danger { color: #991b1b; background-color: #fee2e2; border-color: #fca5a5; }

</style>
@endsection

@section('content')

<div class="table-header-card">
    <h2><i class="fas fa-users-cog"></i> Daftar Pengguna</h2>
    <!-- LINK ke halaman CREATE -->
    <a href="{{ route('admin.pengguna.create') }}" class="btn-submit-header">
        <i class="fas fa-plus"></i> Tambah Pengguna Baru
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
<!-- Error validasi tidak akan muncul di sini -->

<div class="card" style="padding: 0;">
    <div class="table-wrapper">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Loket Ditugaskan</th>
                    <th>Status Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-info">
                            {{-- Mendapatkan inisial --}}
                            @php
                                $initials = implode('', array_map(function($word) {
                                    return strtoupper(substr($word, 0, 1));
                                }, explode(' ', $user->name)));
                                $bgColor = $user->role == 'admin' ? '#ef4444' : '#3b82f6';
                                $initials = substr($initials, 0, 2);
                            @endphp
                            <div class="user-avatar" style="background: {{ $bgColor }};">{{ $initials }}</div>
                            <div class="user-details">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-username">{{ $user->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="status-badge status-{{ $user->role }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->loket->nama_loket ?? 'N/A' }}</td>
                    <td style="text-align:center;">
                        @if($user->id != 1)
                        <input type="checkbox" class="toggle-aktif-user" data-user-id="{{ $user->id }}" {{ $user->aktif ? 'checked' : '' }}>
                        @else
                        <span style="color:#888;">-</span>
                        @endif
                    </td>
                    <td class="action-buttons">
                        <!-- LINK ke halaman EDIT -->
                        <a href="{{ route('admin.pengguna.edit', $user->id) }}" class="btn btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        @if($user->id != 1) 
                        <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-user-slash" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        Belum ada data pengguna.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.querySelectorAll('.toggle-aktif-user').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const userId = this.getAttribute('data-user-id');
        const aktif = this.checked ? 1 : 0;
        fetch(`/admin/pengguna/aktif-toggle/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ aktif })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Gagal update status aktif!');
                this.checked = !aktif;
            }
        })
        .catch(() => {
            alert('Gagal update status aktif!');
            this.checked = !aktif;
        });
    });
});
</script>
@endsection

@section('scripts')
@endsection