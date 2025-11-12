<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Pengaturan;
use App\Models\AudioSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    /**
     * Menampilkan halaman utama display.
     */
    public function index()
    {
        $pengaturan = Pengaturan::first();
        // Ambil pengaturan audio, atau buat default jika tidak ada
        $audioSetting = AudioSetting::firstOrCreate(
            ['id' => 1],
            ['format_pesan' => 'Nomor {nomor} silakan menuju ke {lokasi}', 'volume' => 80]
        );
        
        return view('display.index', compact('pengaturan', 'audioSetting'));
    }

    /**
     * API Internal (JSON) untuk mengambil data loket dan antrian terbaru.
     * Ini digunakan untuk polling fallback jika WebSocket gagal.
     */
    public function getData(Request $request)
    {
        try {
            $today = Carbon::today();

            // 1. Ambil semua loket yang tidak di-nonaktifkan
            $lokets = Loket::with('layanan')
                           ->orderBy('nama_loket', 'asc')
                           ->get(); // Ambil semua, termasuk yang 'tutup'

            // 2. Ambil semua antrian yang sedang aktif HARI INI
            $activeAntrians = Antrian::whereIn('status', ['dipanggil', 'dilayani'])
                                    ->whereDate('waktu_ambil', $today)
                                    ->get()
                                    ->keyBy('loket_id'); // Jadikan loket_id sebagai key

            // 3. Gabungkan data
            $data = $lokets->map(function ($loket) use ($activeAntrians) {
                // Cek apakah ada antrian aktif untuk loket ini
                $currentAntrian = $activeAntrians->get($loket->id);

                return [
                    'id' => $loket->id,
                    'nama_loket' => $loket->nama_loket,
                    'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
                    'status' => $loket->status, // 'aktif' atau 'tutup'
                    'antrian' => $currentAntrian ? [
                        'kode_antrian' => $currentAntrian->kode_antrian,
                        'status' => $currentAntrian->status, // 'dipanggil' atau 'dilayani'
                    ] : null, // null jika tidak ada antrian
                ];
            });

            return response()->json(['success' => true, 'lokets' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}