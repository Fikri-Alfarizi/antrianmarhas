<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Antrian;
use App\Models\Loket;
use Carbon\Carbon;
use App\Events\AntrianDipanggil;
use App\Events\LoketStatusUpdated;
use App\Services\LogActivityService; // Pastikan service log Anda ada

class LoketPetugasController extends Controller
{
    /**
     * Menampilkan halaman utama loket petugas.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role != 'operator' || !$user->loket_id) {
            // Jika bukan operator atau tidak punya loket, tendang
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda tidak ditugaskan di loket manapun.');
        }

        $loket = Loket::with('layanan')->find($user->loket_id);
        
        if (!$loket) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Loket tidak ditemukan.');
        }

        // Buka paksa loket jika statusnya 'tutup' saat operator login
        if ($loket->status == 'tutup') {
            $loket->status = 'aktif';
            $loket->save();
        }

        return view('petugas.loket', compact('loket'));
    }

    /**
     * API Internal (AJAX) untuk refresh data loket
     */
    public function getAntrianList(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;
        
        if (!$loket || !$loket->layanan_id) {
            return response()->json([
                'error' => 'Loket tidak ditemukan atau tidak memiliki layanan yang ditugaskan',
                'current' => null,
                'waiting' => [],
                'history' => [],
                'stats' => ['total' => 0, 'selesai' => 0, 'batal' => 0, 'menunggu_total' => 0],
                'loket_status' => null,
            ], 400);
        }
        
        $today = Carbon::today();

        // 1. Antrian saat ini (yang sedang dipanggil / dilayani)
        $current = Antrian::where('loket_id', $loket->id)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->whereDate('waktu_ambil', $today)
            ->with('layanan')
            ->first();

        // 2. Daftar Antrian Menunggu (milik layanan loket ini)
        $waiting = Antrian::where('layanan_id', $loket->layanan_id)
            ->where('status', 'menunggu')
            ->whereDate('waktu_ambil', $today)
            ->orderBy('waktu_ambil', 'asc')
            ->limit(10)
            ->with('layanan')
            ->get();

        // 3. Daftar Riwayat Selesai/Batal (di loket ini)
        $history = Antrian::where('loket_id', $loket->id)
            ->whereIn('status', ['selesai', 'batal'])
            ->whereDate('waktu_ambil', $today)
            ->orderBy('waktu_selesai', 'desc')
            ->limit(10)
            ->with('layanan')
            ->get();

        // 4. Statistik Loket Hari Ini
        $stats = [
            'total' => Antrian::where('loket_id', $loket->id)->whereDate('waktu_ambil', $today)->count(),
            'selesai' => Antrian::where('loket_id', $loket->id)->where('status', 'selesai')->whereDate('waktu_ambil', $today)->count(),
            'batal' => Antrian::where('loket_id', $loket->id)->where('status', 'batal')->whereDate('waktu_ambil', $today)->count(),
            'menunggu_total' => Antrian::where('layanan_id', $loket->layanan_id)->where('status', 'menunggu')->whereDate('waktu_ambil', $today)->count(),
        ];
        
        // 5. Status Loket terbaru
        $loketStatus = $loket->fresh()->status; // Ambil status terbaru dari DB

        return response()->json([
            'current' => $current,
            'waiting' => $waiting,
            'history' => $history,
            'stats' => $stats,
            'loket_status' => $loketStatus,
        ]);
    }

    /**
     * Aksi: Panggil Antrian Berikutnya
     */
    public function panggil(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;
        $today = Carbon::today();

        // 1. Cek apakah loket sedang melayani
        $sedangDilayani = Antrian::where('loket_id', $loket->id)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->whereDate('waktu_ambil', $today)
            ->exists();
            
        if ($sedangDilayani) {
            return response()->json(['success' => false, 'message' => 'Selesaikan antrian saat ini terlebih dahulu.']);
        }

        // 2. Ambil antrian menunggu berikutnya untuk layanan ini
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
        
        LogActivityService::log("Memanggil antrian {$antrian->kode_antrian}");

        // 4. Broadcast event ke Display
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true, 'message' => 'Antrian ' . $antrian->kode_antrian . ' dipanggil.']);
    }

    /**
     * Aksi: Layani (Menandakan pasien sudah di loket)
     */
    public function layani(Request $request)
    {
        $user = Auth::user();
        $antrian = Antrian::where('loket_id', $user->loket_id)
                         ->where('status', 'dipanggil')
                         ->whereDate('waktu_ambil', Carbon::today())
                         ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dipanggil.']);
        }

        $antrian->update([
            'status' => 'dilayani',
            'waktu_mulai_dilayani' => now(), // (Asumsi kolom ini ada dari migrasi)
        ]);

        LogActivityService::log("Mulai melayani antrian {$antrian->kode_antrian}");
        
        // Broadcast lagi untuk update status di display (misal jadi hijau)
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();
        
        return response()->json(['success' => true]);
    }

    /**
     * Aksi: Selesai
     */
    public function selesai(Request $request)
    {
        $user = Auth::user();
        $antrian = Antrian::where('loket_id', $user->loket_id)
                         ->whereIn('status', ['dipanggil', 'dilayani'])
                         ->whereDate('waktu_ambil', Carbon::today())
                         ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dilayani.']);
        }

        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        LogActivityService::log("Menyelesaikan antrian {$antrian->kode_antrian}");

        // Broadcast untuk membersihkan display dari antrian ini
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Aksi: Batalkan
     */
    public function batalkan(Request $request)
    {
        $user = Auth::user();
        $antrian = Antrian::where('loket_id', $user->loket_id)
                         ->whereIn('status', ['dipanggil', 'dilayani'])
                         ->whereDate('waktu_ambil', Carbon::today())
                         ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dilayani.']);
        }

        $antrian->update([
            'status' => 'batal',
            'waktu_selesai' => now(), // Waktu selesai tetap dicatat
        ]);

        LogActivityService::log("Membatalkan antrian {$antrian->kode_antrian}");

        // Broadcast untuk membersihkan display
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true]);
    }
    
    /**
     * Aksi: Buka/Tutup Loket
     */
    public function tutupLoket(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        $newStatus = ($loket->status == 'aktif') ? 'tutup' : 'aktif';
        $loket->update(['status' => $newStatus]);
        
        LogActivityService::log("Mengubah status loket menjadi {$newStatus}");

        // Broadcast status loket ke display
        broadcast(new LoketStatusUpdated($loket))->toOthers();

        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }
}