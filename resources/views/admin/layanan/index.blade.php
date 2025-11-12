@extends('layouts.app')
@section('title', 'Manajemen Layanan')

@section('styles')
<style>
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

/* Tombol Aksi */
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

/* Status Badge */
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
.status-nonaktif {
    background-color: #fdf2e9;
    color: #f39c12;
}

/* Modal */
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
    margin: 10% auto;
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

/* Form Group */
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

/* Notifikasi */
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
    <h2 style="margin: 0;"><i class="fas fa-concierge-bell"></i> Daftar Layanan</h2>
    <button class="btn btn-primary" onclick="openModal('createModal')">
        <i class="fas fa-plus"></i> Tambah Layanan Baru
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
                    <button class="btn btn-edit" onclick="openModal('editModal-{{ $layanan->id }}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('admin.layanan.destroy', $layanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan ini?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Belum ada data layanan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="createModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus-circle"></i> Tambah Layanan Baru</h3>
            <span class="close-btn" onclick="closeModal('createModal')">&times;</span>
        </div>
        <form action="{{ route('admin.layanan.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="nama_layanan">Nama Layanan</label>
                <input type="text" id="nama_layanan" name="nama_layanan" placeholder="Contoh: Pemeriksaan Umum" required>
            </div>
            <div class="form-group">
                <label for="prefix">Prefix (Kode Awal)</label>
                <input type="text" id="prefix" name="prefix" placeholder="Contoh: A (Harus Unik)" required maxlength="5">
            </div>
            <div class="form-group">
                <label for="digit">Jumlah Digit Angka</label>
                <input type="number" id="digit" name="digit" value="3" min="1" max="5" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="aktif" selected>Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="action-buttons" style="margin-top: 20px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

@foreach($layanans as $layanan)
<div id="editModal-{{ $layanan->id }}" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Layanan</h3>
            <span class="close-btn" onclick="closeModal('editModal-{{ $layanan->id }}')">&times;</span>
        </div>
        <form action="{{ route('admin.layanan.update', $layanan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_layanan-{{ $layanan->id }}">Nama Layanan</label>
                <input type="text" id="nama_layanan-{{ $layanan->id }}" name="nama_layanan" value="{{ $layanan->nama_layanan }}" required>
            </div>
            <div class="form-group">
                <label for="prefix-{{ $layanan->id }}">Prefix (Kode Awal)</label>
                <input type="text" id="prefix-{{ $layanan->id }}" name="prefix" value="{{ $layanan->prefix }}" required maxlength="5">
            </div>
            <div class="form-group">
                <label for="digit-{{ $layanan->id }}">Jumlah Digit Angka</label>
                <input type="number" id="digit-{{ $layanan->id }}" name="digit" value="{{ $layanan->digit }}" min="1" max="5" required>
            </div>
            <div class="form-group">
                <label for="status-{{ $layanan->id }}">Status</label>
                <select id="status-{{ $layanan->id }}" name="status" required>
                    <option value="aktif" {{ $layanan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $layanan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="action-buttons" style="margin-top: 20px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal-{{ $layanan->id }}')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// JavaScript untuk membuka dan menutup modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Menutup modal jika user klik di luar area modal
window.onclick = function(event) {
    // Cari semua modal yang sedang terbuka
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}

// Menangani error validasi dari Laravel
// Jika ada error, modal yang relevan (create atau edit) akan otomatis terbuka
@if ($errors->any())
    @if (old('_method') === 'PUT')
        // Jika ini adalah error dari UPDATE, cari modal edit yang mana
        // Ini asumsi, perlu cara lebih baik untuk tahu ID mana yang error
        // Untuk sekarang, kita buka modal edit pertama jika ada error update
        @php
            $errorId = null;
            if (session()->has('_old_input')) {
                // Cara kasar untuk mendapatkan ID dari URL form action sebelumnya
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
        // Jika ini adalah error dari CREATE
        openModal('createModal');
    @endif
@endif
</script>
@endsection