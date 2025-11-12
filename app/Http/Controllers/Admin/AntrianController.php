<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AntrianController extends Controller
{
    /**
     * Menampilkan daftar riwayat antrian dengan filter.
     */
    public function index(Request $request)
    {
        // Ambil data untuk filter dropdown
        $layanans = Layanan::orderBy('nama_layanan', 'asc')->get();
        $statuses = ['menunggu', 'dipanggil', 'dilayani', 'selesai', 'batal'];

        // Mulai query
        $query = Antrian::with(['layanan', 'loket'])
                        ->orderBy('waktu_ambil', 'desc');

        // Filter
        $filters = [
            // Default filter tanggal adalah HARI INI
            'tanggal' => $request->input('tanggal', Carbon::today()->format('Y-m-d')),
            'layanan_id' => $request->input('layanan_id'),
            'status' => $request->input('status'),
        ];

        // Terapkan filter jika diisi
        if ($filters['tanggal']) {
            $query->whereDate('waktu_ambil', $filters['tanggal']);
        }
        if ($filters['layanan_id']) {
            $query->where('layanan_id', $filters['layanan_id']);
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Ambil data dengan pagination (50 data per halaman)
        $antrians = $query->paginate(50)->appends($filters);

        return view('admin.antrian.index', compact('antrians', 'layanans', 'statuses', 'filters'));
    }
}