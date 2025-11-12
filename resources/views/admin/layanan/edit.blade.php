@extends('layouts.app')
@section('title', 'Edit Layanan')

@section('styles')
<style>
/* CSS Konsisten */
.card {
    background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); padding: 24px;
    margin-bottom: 24px; max-width: 700px;
}
.card-title {
    font-size: 20px; font-weight: 700; color: #0f172a;
    margin: 0 0 20px 0; padding-bottom: 15px; 
    border-bottom: 1px solid #e2e8f0;
}
.form-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;
}
.form-group { margin-bottom: 12px; }
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
@media (max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')

<!-- Notifikasi Error Validasi -->
@if ($errors->any())
    <div class="alert alert-danger" style="max-width: 700px;">
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
    <h3 class="card-title"><i class="fas fa-edit"></i> Edit Layanan: {{ $layanan->nama_layanan }}</h3>

    <form action="{{ route('admin.layanan.update', $layanan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group" style="grid-column: span 2;">
                <label for="nama_layanan">Nama Layanan</label>
                <input type="text" id="nama_layanan" name="nama_layanan" value="{{ old('nama_layanan', $layanan->nama_layanan) }}" required>
            </div>
            
            <div class="form-group">
                <label for="prefix">Prefix (Kode Awal)</label>
                <input type="text" id="prefix" name="prefix" value="{{ old('prefix', $layanan->prefix) }}" required maxlength="5">
            </div>

            <div class="form-group">
                <label for="digit">Jumlah Digit Angka</label>
                <input type="number" id="digit" name="digit" value="{{ old('digit', $layanan->digit) }}" min="1" max="5" required>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="aktif" {{ old('status', $layanan->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status', $layanan->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.layanan.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
        </div>
    </form>
</div>

@endsection