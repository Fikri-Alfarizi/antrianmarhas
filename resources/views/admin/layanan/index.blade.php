@extends('layouts.app')
@section('title', 'Manajemen Layanan')

@section('styles')
<style>
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.modal-content { background: white; max-width: 600px; width: 90%; padding: 30px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h3 { margin: 0; color: #2c3e50; }
.close-btn { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; transition: color 0.2s; }
.close-btn:hover { color: #2c3e50; }
.header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.table-responsive { overflow-x: auto; }
@media (max-width: 768px) {
    .header-actions { flex-direction: column; align-items: stretch; }
    .header-actions .btn { width: 100%; }
    table { font-size: 13px; }
    table th, table td { padding: 8px; }
}
</style>
@endsection

@section('content')
<div class="card">
    <div class="header-actions">
        <h2 style="margin: 0;"><i class="fas fa-list-ul"></i> Manajemen Layanan</h2>
        <button onclick="showModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Layanan
        </button>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Layanan</th>
                    <th>Prefix</th>
                    <th>Digit</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($layanans as $key => $layanan)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $layanan->nama_layanan }}</td>
                    <td><span class="badge badge-primary">{{ $layanan->prefix }}</span></td>
                    <td>{{ $layanan->digit }} digit</td>
                    <td>
                        <span class="badge badge-{{ $layanan->status === 'aktif' ? 'success' : 'danger' }}">
                            <i class="fas fa-{{ $layanan->status === 'aktif' ? 'check-circle' : 'times-circle' }}"></i> {{ ucfirst($layanan->status) }}
                        </span>
                    </td>
                    <td>
                        <button onclick="editLayanan({{ $layanan->id }}, '{{ addslashes($layanan->nama_layanan) }}', '{{ $layanan->prefix }}', {{ $layanan->digit }}, '{{ $layanan->status }}')" 
                                class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.layanan.destroy', $layanan->id) }}" method="POST" style="display: inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus layanan ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px 20px;">
                        <i class="fas fa-inbox" style="font-size: 40px; color: #ddd; margin-bottom: 10px; display: block;"></i>
                        <p style="color: #999;">Belum ada data layanan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-plus"></i> Tambah Layanan</h3>
            <button onclick="hideModal()" class="close-btn" title="Tutup"><i class="fas fa-times"></i></button>
        </div>
        <form id="formLayanan" method="POST" action="{{ route('admin.layanan.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="layanan_id">

            <div class="form-group">
                <label><i class="fas fa-heading"></i> Nama Layanan</label>
                <input type="text" name="nama_layanan" id="nama_layanan" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-font"></i> Prefix (Huruf Awal Kode)</label>
                <input type="text" name="prefix" id="prefix" maxlength="5" required placeholder="Contoh: A, B, C">
                <small style="color: #999;">Format: Satu huruf kapital untuk identifikasi layanan</small>
            </div>
            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Jumlah Digit Angka</label>
                <input type="number" name="digit" id="digit" min="1" max="5" value="3" required>
                <small style="color: #999;">Contoh: 3 digit = A001, A002; 2 digit = A01, A02</small>
            </div>
            <div class="form-group">
                <label><i class="fas fa-toggle-on"></i> Status</label>
                <select name="status" id="status" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
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
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus"></i> Tambah Layanan';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formLayanan').action = '{{ route("admin.layanan.store") }}';
    document.getElementById('formLayanan').reset();
    document.getElementById('nama_layanan').focus();
}

function hideModal() {
    document.getElementById('modal').style.display = 'none';
}

function editLayanan(id, nama, prefix, digit, status) {
    document.getElementById('modal').style.display = 'flex';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Layanan';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formLayanan').action = '/admin/layanan/' + id;
    document.getElementById('layanan_id').value = id;
    document.getElementById('nama_layanan').value = nama;
    document.getElementById('prefix').value = prefix;
    document.getElementById('digit').value = digit;
    document.getElementById('status').value = status;
    document.getElementById('nama_layanan').focus();
}

window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        hideModal();
    }
}
@endsection

