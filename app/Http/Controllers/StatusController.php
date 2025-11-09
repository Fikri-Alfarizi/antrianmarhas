<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * Tampilkan halaman tracking status antrian
     */
    public function index()
    {
        $pengaturan = Pengaturan::first();
        return view('status.index', compact('pengaturan'));
    }

    /**
     * API untuk mendapatkan status antrian berdasarkan kode
     */
    public function check(Request $request)
    {
        $request->validate([
            'kode_antrian' => 'required|string|max:10',
        ]);

        $antrian = Antrian::where('kode_antrian', strtoupper($request->kode_antrian))
            ->with(['layanan', 'loket'])
            ->first();

        if (!$antrian) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor antrian tidak ditemukan',
            ], 404);
        }

        // Hitung posisi antrian
        $posisi = Antrian::where('layanan_id', $antrian->layanan_id)
            ->where('status', 'menunggu')
            ->where('id', '<', $antrian->id)
            ->count() + 1;

        // Status bahasa Indonesia
        $statusLabel = [
            'menunggu' => 'Menunggu Dipanggil',
            'dipanggil' => 'Sedang Dipanggil',
            'dilayani' => 'Sedang Dilayani',
            'selesai' => 'Selesai',
            'batal' => 'Dibatalkan',
        ][$antrian->status] ?? $antrian->status;

        $statusColor = [
            'menunggu' => 'warning',
            'dipanggil' => 'info',
            'dilayani' => 'primary',
            'selesai' => 'success',
            'batal' => 'danger',
        ][$antrian->status] ?? 'secondary';

        $estimasiWaktu = null;
        if ($antrian->status === 'menunggu') {
            // Estimasi: 5 menit per antrian sebelumnya
            $estimasiWaktu = ($posisi - 1) * 5;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'kode_antrian' => $antrian->kode_antrian,
                'layanan' => $antrian->layanan->nama_layanan,
                'status' => $antrian->status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'loket' => $antrian->loket ? $antrian->loket->nama_loket : null,
                'posisi' => $antrian->status === 'menunggu' ? $posisi : null,
                'waktu_ambil' => $antrian->waktu_ambil->format('H:i:s'),
                'waktu_panggil' => $antrian->waktu_panggil ? $antrian->waktu_panggil->format('H:i:s') : null,
                'waktu_selesai' => $antrian->waktu_selesai ? $antrian->waktu_selesai->format('H:i:s') : null,
                'estimasi_waktu' => $estimasiWaktu,
            ],
        ]);
    }

    /**
     * API untuk mendapatkan detail antrian berdasarkan ID
     */
    public function show(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:antrians,id',
        ]);

        $antrian = Antrian::with(['layanan', 'loket'])
            ->findOrFail($request->id);

        // Hitung posisi antrian
        $posisi = Antrian::where('layanan_id', $antrian->layanan_id)
            ->where('status', 'menunggu')
            ->where('id', '<', $antrian->id)
            ->count() + 1;

        return response()->json([
            'success' => true,
            'antrian' => [
                'id' => $antrian->id,
                'kode_antrian' => $antrian->kode_antrian,
                'layanan' => $antrian->layanan,
                'loket' => $antrian->loket,
                'status' => $antrian->status,
                'waktu_ambil' => $antrian->waktu_ambil,
                'waktu_panggil' => $antrian->waktu_panggil,
                'waktu_dilayani' => $antrian->waktu_panggil, // alias
                'waktu_selesai' => $antrian->waktu_selesai,
                'posisi' => $antrian->status === 'menunggu' ? $posisi : null,
            ]
        ]);
    }

    /**
     * API untuk halaman waiting - menampilkan status real-time antrian
     */
    public function waitingStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:antrians,id',
        ]);

        $antrian = Antrian::with(['layanan', 'loket'])
            ->findOrFail($request->id);

        $layananId = $antrian->layanan_id;

        // Hitung posisi dan antrian sebelumnya
        $antrianDisebelum = Antrian::where('layanan_id', $layananId)
            ->where('status', 'menunggu')
            ->where('id', '<', $antrian->id)
            ->count();

        // Dapatkan yang sedang dipanggil di loket manapun
        $currentCalling = Antrian::where('status', 'dipanggil')
            ->with('loket')
            ->first();

        // Hitung statistik
        $selesai = Antrian::where('layanan_id', $layananId)
            ->where('status', 'selesai')
            ->count();

        $menunggu = Antrian::where('layanan_id', $layananId)
            ->where('status', 'menunggu')
            ->count();

        $pengaturan = Pengaturan::first();

        return response()->json([
            'success' => true,
            'antrian' => $antrian,
            'current_calling' => $currentCalling,
            'antrian_disebelum' => $antrianDisebelum,
            'selesai' => $selesai,
            'menunggu' => $menunggu,
            'pengaturan' => $pengaturan,
        ]);
    }
}
