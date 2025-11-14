<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Antrian;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Diperlukan untuk str_pad

class KiosController extends Controller
{
    /**
     * Menampilkan halaman kios.
     */
    public function index()
    {
        $pengaturan = Pengaturan::first();
        // Hanya ambil layanan yang statusnya 'aktif'
        $layanans = Layanan::where('status', 'aktif')->orderBy('nama_layanan', 'asc')->get();
        $advancedSetting = \App\Models\AdvancedSetting::first();
        $themeColor = $advancedSetting->theme_color ?? '#3b82f6';
        return view('kios.index', compact('pengaturan', 'layanans', 'themeColor'));
    }

    /**
     * Membuat antrian baru (dipanggil via AJAX/Fetch).
     */
    public function cetak(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|integer|exists:layanans,id',
            'printer' => 'nullable|string',
        ]);

        $layanan = Layanan::find($request->layanan_id);

        // Cek sekali lagi jika layanan aktif (keamanan)
        if ($layanan->status == 'nonaktif') {
            return response()->json([
                'success' => false, 
                'message' => 'Layanan ini sedang tidak aktif.'
            ], 400);
        }

        // --- Logika Pembuatan Nomor Antrian ---

        // 1. Ambil antrian terakhir untuk layanan ini HARI INI
        $lastAntrian = Antrian::where('layanan_id', $layanan->id)
                            ->whereDate('waktu_ambil', Carbon::today())
                            ->orderBy('id', 'desc')
                            ->first();

        $newNumber = 1;
        if ($lastAntrian) {
            // 2. Ekstrak nomor dari kode, cth: "A005" -> "005"
            $lastNumberStr = preg_replace('/[^0-9]/', '', $lastAntrian->kode_antrian);
            
            if (is_numeric($lastNumberStr)) {
                $newNumber = (int)$lastNumberStr + 1;
            }
        }

        // 3. Format nomor baru
        $kodeAntrian = $layanan->prefix . str_pad($newNumber, $layanan->digit, '0', STR_PAD_LEFT);

        // 4. Simpan ke database
        $antrian = Antrian::create([
            'layanan_id' => $layanan->id,
            'kode_antrian' => $kodeAntrian,
            'status' => 'menunggu',
            'waktu_ambil' => now(),
            // 'qr_code' -> Logika QR Code bisa ditambahkan di sini
        ]);
        
        $pengaturan = Pengaturan::first();

        // 5. Kembalikan data untuk dicetak
        return response()->json([
            'success' => true,
            'antrian' => [
                'id' => $antrian->id,
                'kode_antrian' => $antrian->kode_antrian,
                'nama_layanan' => $layanan->nama_layanan,
                'waktu_ambil' => $antrian->waktu_ambil->format('d-m-Y H:i:s'),
                'waktu_ambil_jam' => $antrian->waktu_ambil->format('H:i:s'),
            ],
            'instansi' => [
                'nama' => $pengaturan ? ($pengaturan->nama_instansi ?? 'Instansi Anda') : 'Instansi Anda',
                'alamat' => $pengaturan ? ($pengaturan->alamat ?? '') : '',
                'telepon' => $pengaturan ? ($pengaturan->telepon ?? '') : '',
                'logo' => $pengaturan && $pengaturan->logo ? asset('logo/' . $pengaturan->logo) : null,
            ],
            'printer' => $request->printer,
        ]);
    }
}