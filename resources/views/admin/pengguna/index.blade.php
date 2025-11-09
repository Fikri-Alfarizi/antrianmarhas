@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('styles')
<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
.modal-content { background: white; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 10px; max-height: 90vh; overflow-y: auto; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; }
.btn-sm { padding: 5px 10px; font-size: 12px; }
</style>
@endsection

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fas fa-users"></i> Manajemen Pengguna</h2>
        <button onclick="showModal()" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Tambah Pengguna
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Loket</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $key => $user)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td><i class="fas fa-user-circle"></i> {{ $user->name }}</td>
                <td>{{ $user->username }}</td>
                <td><span class="badge badge-info">{{ ucfirst($user->role) }}</span></td>
                <td>{{ $user->loket->nama_loket ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $user->status === 'aktif' ? 'success' : 'danger' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </td>
                <td>
                    @if($user->id != auth()->id())
                    <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" style="display: inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada data pengguna</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Tambah Pengguna</h3>
            <button onclick="hideModal()" class="close-btn"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.pengguna.store') }}">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nama Lengkap</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-at"></i> Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> Role</label>
                <select name="role" id="role" onchange="toggleLoket()">
                    <option value="admin">Admin</option>
                    <option value="operator">Operator</option>
                </select>
            </div>
            <div class="form-group" id="loketField" style="display: none;">
                <label><i class="fas fa-door-open"></i> Loket Tugasan</label>
                <select name="loket_id">
                    <option value="">- Pilih Loket -</option>
                    @foreach($lokets as $loket)
                    <option value="{{ $loket->id }}">{{ $loket->nama_loket }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-toggle-on"></i> Status</label>
                <select name="status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="hideModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showModal() { 
    document.getElementById('modal').style.display = 'block'; 
}

function hideModal() { 
    document.getElementById('modal').style.display = 'none'; 
}

function toggleLoket() {
    const role = document.getElementById('role').value;
    document.getElementById('loketField').style.display = role === 'operator' ? 'block' : 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('modal')) {
        hideModal();
    }
}
</script>
@endsection
