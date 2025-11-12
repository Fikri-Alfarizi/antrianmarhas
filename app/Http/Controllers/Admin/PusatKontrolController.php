<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Events\AntrianDipanggil; // Penting untuk broadcast
use App\Events\LoketStatusUpdated; // Penting untuk broadcast
use App\Services\LogActivityService; // Penting untuk log
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PusatKontrolController extends Controller
{
    /**
     * Menampilkan halaman utama Pusat Kontrol.
     */
    public function index()
    {
        // Tampilan awal hanya memuat layout
        // Data akan dimuat via AJAX
        return view('admin.pusat-kontrol.index');
    }

    /**
     * API Internal (AJAX) untuk mengambil data SEMUA loket.
     * Ini yang akan di-refresh oleh JavaScript.
     */
    public function getData(Request $request)
    {
        $today = Carbon::today();
        
        // 1. Ambil semua loket dengan eager load layanan dan users
        $lokets = Loket::with('layanan', 'users')->orderBy('nama_loket', 'asc')->get();

        // 2. Ambil semua antrian yang sedang aktif HARI INI
        $activeAntrians = Antrian::whereIn('status', ['dipanggil', 'dilayani'])
                                ->whereDate('waktu_ambil', $today)
                                ->get()
                                ->keyBy('loket_id'); // Jadikan loket_id sebagai key

        // 3. Ambil jumlah antrian menunggu untuk SETIAP LAYANAN
        $waitingCounts = Layanan::withCount(['antrians' => function ($query) use ($today) {
            $query->where('status', 'menunggu')->whereDate('waktu_ambil', $today);
        }])->get()->pluck('antrians_count', 'id'); // key by layanan_id
        
        // 4. Gabungkan data
        $data = $lokets->map(function ($loket) use ($activeAntrians, $waitingCounts) {
            $currentAntrian = $activeAntrians->get($loket->id);
            $waitingCount = $waitingCounts->get($loket->layanan_id) ?? 0;

            return [
                'id' => $loket->id,
                'nama_loket' => $loket->nama_loket,
                'status' => $loket->status, // 'aktif' atau 'tutup'
                'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
                'layanan_id' => $loket->layanan_id,
                'operator' => $loket->users->first()->name ?? '(Kosong)', // Ambil nama operator
                'antrian' => $currentAntrian ? [
                    'kode_antrian' => $currentAntrian->kode_antrian,
                    'status' => $currentAntrian->status, // 'dipanggil' atau 'dilayani'
                ] : null,
                'waiting_count' => $waitingCount,
            ];
        });

        return response()->json(['success' => true, 'lokets' => $data]);
    }

    /**
     * Aksi: Panggil Antrian (oleh Admin)
     */
    public function panggil(Request $request, Loket $loket)
    {
        $today = Carbon::today();

        // 1. Cek apakah loket sedang melayani
        $sedangDilayani = Antrian::where('loket_id', $loket->id)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->whereDate('waktu_ambil', $today)
            ->exists();
            
        if ($sedangDilayani) {
            return response()->json(['success' => false, 'message' => 'Loket sedang melayani. Selesaikan dulu.']);
        }

        // 2. Ambil antrian menunggu berikutnya untuk layanan loket ini
        $antrian = Antrian::where('layanan_id', $loket->layanan_id)
            ->where('status', 'menunggu')
            ->whereDate('waktu_ambil', $today)
            ->orderBy('waktu_ambil', 'asc')
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian menunggu.']);
        }

        // 3. Update antrian
        $antrian->update([
            'status' => 'dipanggil',
            'waktu_panggil' => now(),
            'loket_id' => $loket->id,
        ]);
        
        LogActivityService::log("ADMIN memanggil antrian {$antrian->kode_antrian} ke {$loket->nama_loket}");

        // 4. Broadcast event ke Display (dan ke Petugas jika dia sedang buka)
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true, 'message' => 'Antrian ' . $antrian->kode_antrian . ' dipanggil.']);
    }

    /**
     * Aksi: Selesaikan Antrian (oleh Admin)
     */
    public function selesai(Request $request, Loket $loket)
    {
        $antrian = Antrian::where('loket_id', $loket->id)
                         ->whereIn('status', ['dipanggil', 'dilayani'])
                         ->whereDate('waktu_ambil', Carbon::today())
                         ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dilayani di loket ini.']);
        }

        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        LogActivityService::log("ADMIN menyelesaikan antrian {$antrian->kode_antrian} di {$loket->nama_loket}");

        // Broadcast untuk membersihkan display
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true]);
    }
}