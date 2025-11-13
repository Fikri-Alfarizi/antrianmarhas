@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('styles')
<style>
/* CSS ini diambil dari Loket/Layanan untuk konsistensi */
.card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); padding: 24px;
    margin-bottom: 24px; max-width: 600px; /* Batasi lebar form */
}
.card-title {
    font-size: 20px; font-weight: 700; color: #0f172a;
    margin: 0 0 20px 0; padding-bottom: 15px; 
    border-bottom: 1px solid #e2e8f0;
}
.form-group { margin-bottom: 15px; }
.form-group label {
    display: block; font-weight: 600; font-size: 12px; 
    color: #475569; margin-bottom: 6px; 
}
.form-group input, .form-group select {
    width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; 
    border-radius: 10px; font-size: 14px; color: #1e293b; 
    background: #ffffff; font-family: 'Inter', sans-serif; transition: all 0.3s;
}
.form-group input:focus, .form-group select:focus {
    outline: none; border-color: #3b82f6; background: white;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}
.action-buttons {
    display: flex; gap: 8px; margin-top: 20px; justify-content: flex-end;
}
.btn { 
    padding: 10px 18px; border: none; border-radius: 12px; cursor: pointer; 
    font-weight: 600; font-size: 14px; display: inline-flex;
    align-items: center; gap: 5px; transition: all 0.2s;
    text-decoration: none;
}
.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.alert-danger {
    padding: 15px; margin-bottom: 24px; border-radius: 12px; 
    color: #991b1b; background-color: #fee2e2; border: 1px solid #fca5a5;
    display: flex; align-items: flex-start; gap: 10px; font-weight: 500;
}
</style>
@endsection

@section('content')

<!-- Notifikasi Error Validasi -->
@if ($errors->any())
    <div class="alert alert-danger" style="max-width: 600px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i> 
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
    <h3 class="card-title"><i class="fas fa-edit"></i> Edit Pengguna: {{ $pengguna->name }}</h3>
    
    <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name', $pengguna->name) }}" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $pengguna->username) }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $pengguna->email) }}" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password Baru</label>
            <input type="password" id="password" name="password">
            <small style="color: #64748b; font-size: 11px;">Kosongkan jika tidak ingin mengubah password.</small>
        </div>
        
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required onchange="toggleLoket()">
                <option value="operator" {{ old('role', $pengguna->role) == 'operator' ? 'selected' : '' }}>Operator Loket</option>
                <option value="admin" {{ old('role', $pengguna->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
            </select>
        </div>
        
        <div class="form-group" id="loket_group">
            <label for="loket_id">Tugaskan ke Loket</label>
            <select id="loket_id" name="loket_id">
                <option value="" {{ old('loket_id', $pengguna->loket_id) ? '' : 'selected' }}>-- Pilih Loket --</option>
                @foreach($lokets as $loket)
                    <option value="{{ $loket->id }}" {{ old('loket_id', $pengguna->loket_id) == $loket->id ? 'selected' : '' }}>
                        {{ $loket->nama_loket }}
                        {{-- Cek apakah loket sudah terisi oleh ORANG LAIN --}}
                        @if($loket->users->count() > 0 && $loket->users->first()->id != $pengguna->id)
                            (Ditugaskan ke: {{ $loket->users->first()->name }})
                        @endif
                    </option>
                @endforeach
            </select>
            <small style="color: #94a3b8; font-size: 11px;">Hanya operator yang dapat ditugaskan ke loket.</small>
        </div>

        <div class="action-buttons">
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Jalankan sekali saat dimuat untuk mengatur tampilan awal
    toggleLoket();
});

function toggleLoket() {
    const roleSelect = document.getElementById('role');
    const loketGroup = document.getElementById('loket_group');
    const loketSelect = document.getElementById('loket_id');
    
    if (roleSelect && loketGroup && loketSelect) {
        if (roleSelect.value === 'operator') {
            loketGroup.style.display = 'block';
            loketSelect.required = true; 
        } else {
            loketGroup.style.display = 'none';
            loketSelect.required = false; 
        }
    }
}
</script>
@endsection