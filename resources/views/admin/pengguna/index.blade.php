@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('styles')
<style>
/* CSS ini SAMA PERSIS dengan halaman Layanan/Loket */
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

.action-buttons {
    display: flex;
    gap: 8px;
}
.btn { 
    padding: 8px 12px; 
    border: none; 
    border-radius: 4px; 
    cursor: pointer; 
    font-weight: 600; 
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.btn-primary { background: #3498db; color: white; }
.btn-primary:hover { background: #2980b9; }
.btn-edit { background: #f39c12; color: white; }
.btn-edit:hover { background: #e67e22; }
.btn-danger { background: #e74c3c; color: white; }
.btn-danger:hover { background: #c0392b; }
.btn-secondary { background: #95a5a6; color: white; }
.btn-secondary:hover { background: #7f8c8d; }

.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-admin {
    background-color: #e8daef;
    color: #8e44ad;
}
.status-operator {
    background-color: #eafaf1;
    color: #27ae60;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s;
}
.modal-content {
    background-color: #fefefe;
    margin: 5% auto; /* Margin atas dikecilkan agar muat banyak form */
    padding: 25px;
    border: 1px solid #888;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    animation: slideIn 0.3s;
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.modal-header h3 {
    margin: 0;
    color: #2c3e50;
}
.close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
.close-btn:hover,
.close-btn:focus {
    color: #333;
}
@keyframes fadeIn { from {opacity: 0} to {opacity: 1} }
@keyframes slideIn { from {transform: translateY(-50px)} to {transform: translateY(0)} }

.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}
.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>
@endsection

@section('content')

<div class="card" style="margin-bottom: 20px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin: 0;"><i class="fas fa-users-cog"></i> Daftar Pengguna</h2>
    <button class="btn btn-primary" onclick="openModal('createModal')">
        <i class="fas fa-plus"></i> Tambah Pengguna Baru
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i> 
        <div>
            <strong>Gagal memproses data!</strong>
            <ul style="margin: 5px 0 0 20px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="card">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Role</th>
                <th>Loket Ditugaskan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->username }}</td>
                <td>
                    @if($user->role == 'admin')
                        <span class="status-badge status-admin">Admin</span>
                    @else
                        <span class="status-badge status-operator">Operator</span>
                    @endif
                </td>
                <td>{{ $user->loket->nama_loket ?? 'N/A' }}</td>
                <td class="action-buttons">
                    <button class="btn btn-edit" onclick="openModal('editModal-{{ $user->id }}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    @if($user->id != 1) {{-- Admin utama (ID 1) tidak bisa dihapus --}}
                    <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" style="display:inline;">
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
                <td colspan="5" style="text-align: center; padding: 20px;">Belum ada data pengguna.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Tambah Pengguna Baru</h3>
            <span class="close-btn" onclick="closeModal('createModal')">&times;</span>
        </div>
        <form action="{{ route('admin.pengguna.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name_create">Nama Lengkap</label>
                <input type="text" id="name_create" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="username_create">Username</label>
                <input type="text" id="username_create" name="username" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label for="email_create">Email</label>
                <input type="email" id="email_create" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password_create">Password</label>
                <input type="password" id="password_create" name="password" required>
            </div>
            <div class="form-group">
                <label for="role_create">Role</label>
                <select id="role_create" name="role" required onchange="toggleLoket('create')">
                    <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator Loket</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <div class="form-group" id="loket_group_create">
                <label for="loket_id_create">Tugaskan ke Loket</label>
                <select id="loket_id_create" name="loket_id">
                    <option value="" disabled selected>-- Pilih Loket --</option>
                    @foreach($lokets as $loket)
                        <option value="{{ $loket->id }}" {{ old('loket_id') == $loket->id ? 'selected' : '' }}>
                            {{ $loket->nama_loket }}
                            {{-- Cek apakah loket sudah terisi --}}
                            @if($loket->users->count() > 0)
                                (Ditugaskan ke: {{ $loket->users->first()->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="action-buttons" style="margin-top: 20px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

@foreach($users as $user)
<div id="editModal-{{ $user->id }}" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Pengguna</h3>
            <span class="close-btn" onclick="closeModal('editModal-{{ $user->id }}')">&times;</span>
        </div>
        <form action="{{ route('admin.pengguna.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name_edit-{{ $user->id }}">Nama Lengkap</label>
                <input type="text" id="name_edit-{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label for="username_edit-{{ $user->id }}">Username</label>
                <input type="text" id="username_edit-{{ $user->id }}" name="username" value="{{ old('username', $user->username) }}" required>
            </div>
             <div class="form-group">
                <label for="email_edit-{{ $user->id }}">Email</label>
                <input type="email" id="email_edit-{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group">
                <label for="password_edit-{{ $user->id }}">Password Baru</label>
                <input type="password" id="password_edit-{{ $user->id }}" name="password">
                <small style="color: #999;">Kosongkan jika tidak ingin mengubah password.</small>
            </div>
            <div class="form-group">
                <label for="role_edit-{{ $user->id }}">Role</label>
                <select id="role_edit-{{ $user->id }}" name="role" required onchange="toggleLoket('edit-{{ $user->id }}')">
                    <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator Loket</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <div class="form-group" id="loket_group_edit-{{ $user->id }}">
                <label for="loket_id_edit-{{ $user->id }}">Tugaskan ke Loket</label>
                <select id="loket_id_edit-{{ $user->id }}" name="loket_id">
                    <option value="" disabled>-- Pilih Loket --</option>
                    @foreach($lokets as $loket)
                        <option value="{{ $loket->id }}" {{ old('loket_id', $user->loket_id) == $loket->id ? 'selected' : '' }}>
                            {{ $loket->nama_loket }}
                            {{-- Cek apakah loket sudah terisi oleh ORANG LAIN --}}
                            @if($loket->users->count() > 0 && $loket->users->first()->id != $user->id)
                                (Ditugaskan ke: {{ $loket->users->first()->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="action-buttons" style="margin-top: 20px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal-{{ $user->id }}')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// JS untuk Modal (SAMA SEPERTI SEBELUMNYA)
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    
    // Panggil toggleLoket saat modal edit dibuka
    if(modalId.includes('edit')) {
        const id = modalId.split('-')[1];
        toggleLoket('edit-' + id);
    } else if(modalId === 'createModal') {
        toggleLoket('create');
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}

/**
 * JS untuk menampilkan/menyembunyikan dropdown loket
 * berdasarkan pilihan Role.
 * 'suffix' bisa berupa 'create' atau 'edit-1', 'edit-2', dst.
 */
function toggleLoket(suffix) {
    const roleSelect = document.getElementById('role_' + suffix);
    const loketGroup = document.getElementById('loket_group_' + suffix);
    
    if (roleSelect && loketGroup) {
        if (roleSelect.value === 'operator') {
            loketGroup.style.display = 'block';
        } else {
            loketGroup.style.display = 'none';
        }
    }
}

// Menangani error validasi dari Laravel
@if ($errors->any())
    @if (old('_method') === 'PUT')
        @php
            $errorId = null;
            if (session()->has('_old_input')) {
                $url = session()->get('_previous')['url'] ?? '';
                preg_match('/\/(\d+)$/', $url, $matches);
                if (isset($matches[1])) {
                    $errorId = $matches[1];
                }
            }
        @endphp
        
        @if ($errorId)
            openModal('editModal-{{ $errorId }}');
        @endif
    @else
        openModal('createModal');
    @endif
@endif

// Jalankan toggleLoket untuk semua modal edit saat halaman dimuat
// Ini untuk memastikan tampilan dropdown loket sesuai dengan data awal
document.addEventListener('DOMContentLoaded', function() {
    @foreach($users as $user)
        toggleLoket('edit-{{ $user->id }}');
    @endforeach
    
    // Jalankan juga untuk modal create
    toggleLoket('create');
});
</script>
@endsection