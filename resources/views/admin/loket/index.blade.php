@extends('layouts.app')
@section('title', 'Manajemen Loket')

@section('styles')
<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.modal-content { background: white; max-width: 600px; width: 90%; padding: 30px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h3 { margin: 0; color: #2c3e50; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; transition: color 0.2s; }
.close-btn:hover { color: #2c3e50; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.status-toggle { cursor: pointer; }
.btn-sm { padding: 6px 12px; font-size: 12px; gap: 6px; }
.btn-group { display: flex; gap: 8px; }
@media (max-width: 768px) {
    .header-actions { flex-direction: column; align-items: stretch; }
    .header-actions .btn { width: 100%; }
    .btn-group { flex-direction: column; }
    .btn-group .btn { width: 100%; }
}
</style>
@endsection

@section('content')
<div class="card">
    <div class="header-actions">
        <h2 style="margin: 0;"><i class="fas fa-door-open"></i> Manajemen Loket</h2>
        <button onclick="showModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Loket
        </button>
    </div>
    
    <table style="margin-top: 20px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Loket</th>
                <th>Layanan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lokets as $key => $loket)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td><strong>{{ $loket->nama_loket }}</strong></td>
                <td>{{ $loket->layanan->nama_layanan }}</td>
                <td>
                    <span class="badge badge-{{ $loket->status === 'aktif' ? 'success' : 'danger' }} status-toggle" onclick="toggleStatus({{ $loket->id }})">
                        <i class="fas fa-{{ $loket->status === 'aktif' ? 'check-circle' : 'times-circle' }}"></i> 
                        {{ ucfirst($loket->status) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button onclick="editLoket({{ $loket->id }}, '{{ addslashes($loket->nama_loket) }}', {{ $loket->layanan_id }}, '{{ $loket->status }}')" 
                                class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form action="{{ route('admin.loket.destroy', $loket->id) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus loket ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px 20px;">
                    <i class="fas fa-inbox" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                    <p style="color: #999;">Belum ada data loket</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-plus"></i> Tambah Loket</h3>
            <button onclick="hideModal()" class="close-btn" title="Tutup"><i class="fas fa-times"></i></button>
        </div>
        <form id="formLoket" method="POST" action="{{ route('admin.loket.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="loket_id">
            
            <div class="form-group">
                <label><i class="fas fa-heading"></i> Nama Loket</label>
                <input type="text" name="nama_loket" id="nama_loket" required autofocus placeholder="Contoh: Ruang 1, Loket A">
            </div>
            <div class="form-group">
                <label><i class="fas fa-list-ul"></i> Layanan</label>
                <select name="layanan_id" id="layanan_id" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($layanans as $layanan)
                    <option value="{{ $layanan->id }}">{{ $layanan->nama_layanan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label><i class="fas fa-toggle-on"></i> Status</label>
                <select name="status" id="status" required>
                    <option value="aktif">Aktif</option>
                    <option value="tutup">Tutup</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" onclick="hideModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
function showModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Tambah Loket';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formLoket').action = '{{ route("admin.loket.store") }}';
    document.getElementById('formLoket').reset();
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('nama_loket').focus();
}

function hideModal() {
    document.getElementById('modal').style.display = 'none';
}

function editLoket(id, nama, layananId, status) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Loket';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formLoket').action = '/admin/loket/' + id;
    document.getElementById('loket_id').value = id;
    document.getElementById('nama_loket').value = nama;
    document.getElementById('layanan_id').value = layananId;
    document.getElementById('status').value = status;
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('nama_loket').focus();
}

function toggleStatus(id) {
    fetch(`/admin/loket/${id}/toggle`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengubah status');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan');
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        hideModal();
    }
}
@endsection
