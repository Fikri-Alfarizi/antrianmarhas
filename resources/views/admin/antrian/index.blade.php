@extends('layouts.app')
@section('title', 'Daftar Antrian')

@section('styles')
<style>
.filter-form { margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap; }
.filter-form select, .filter-form input { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
.stats-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="card">
    <h2><i class="fas fa-clipboard-list"></i> Daftar Antrian</h2>
    
    <form method="GET" class="filter-form">
        <select name="layanan" onchange="this.form.submit()">
            <option value="">Semua Layanan</option>
            @foreach($layanans as $layanan)
            <option value="{{ $layanan->id }}" {{ request('layanan') == $layanan->id ? 'selected' : '' }}>
                {{ $layanan->nama_layanan }}
            </option>
            @endforeach
        </select>
        
        <select name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
            <option value="dipanggil" {{ request('status') == 'dipanggil' ? 'selected' : '' }}>Dipanggil</option>
            <option value="dilayani" {{ request('status') == 'dilayani' ? 'selected' : '' }}>Dilayani</option>
            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
        </select>
        
        <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" onchange="this.form.submit()">
    </form>
    
    <div class="stats-info">
        <i class="fas fa-info-circle"></i> Total Antrian: <strong>{{ $totalHariIni }}</strong>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Antrian</th>
                <th>Layanan</th>
                <th>Loket</th>
                <th>Status</th>
                <th>Waktu Ambil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($antrians as $key => $antrian)
            <tr>
                <td>{{ $antrians->firstItem() + $key }}</td>
                <td><strong>{{ $antrian->kode_antrian }}</strong></td>
                <td>{{ $antrian->layanan->nama_layanan }}</td>
                <td>{{ $antrian->loket->nama_loket ?? '-' }}</td>
                <td>
                    @if($antrian->status === 'menunggu')
                        <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu</span>
                    @elseif($antrian->status === 'dipanggil')
                        <span class="badge badge-info"><i class="fas fa-phone"></i> Dipanggil</span>
                    @elseif($antrian->status === 'dilayani')
                        <span class="badge badge-info"><i class="fas fa-user-nurse"></i> Dilayani</span>
                    @elseif($antrian->status === 'selesai')
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>
                    @else
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Batal</span>
                    @endif
                </td>
                <td>{{ $antrian->waktu_ambil->format('H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada data antrian</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $antrians->links() }}
    </div>
</div>
@endsection
