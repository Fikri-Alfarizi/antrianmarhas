<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Loket;
use App\Services\LogActivityService;
use App\Services\StaffPerformanceService;
use App\Events\AntrianDipanggil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoketPetugasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $loket = $user->loket;

        if (!$loket) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki loket yang ditugaskan');
        }

        $today = Carbon::today();
        
        // Statistik berdasarkan layanan (bukan loket)
        // Karena antrian baru belum punya loket_id sampai dipanggil
        $statistik = [
            'total' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)->count(),
            'menunggu' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)
                ->where('status', 'menunggu')->count(),
            'dipanggil' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)
                ->where('status', 'dipanggil')->count(),
            'dilayani' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)
                ->where('status', 'dilayani')->count(),
            'selesai' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)
                ->where('status', 'selesai')->count(),
            'batal' => Antrian::where('layanan_id', $loket->layanan_id)
                ->whereDate('waktu_ambil', $today)
                ->where('status', 'batal')->count(),
        ];

        // Antrian yang sedang aktif untuk layanan ini
        $antrianAktif = Antrian::where('layanan_id', $loket->layanan_id)
            ->whereDate('waktu_ambil', $today)
            ->whereIn('status', ['menunggu', 'dipanggil', 'dilayani'])
            ->orderBy('waktu_ambil', 'asc')
            ->get();

        // Performance stats
        $personalStats = StaffPerformanceService::getPersonalStats($user);
        $goalProgress = StaffPerformanceService::getGoalProgress($user);
        $weeklyPerformance = StaffPerformanceService::getWeeklyPerformance($user);
        $monthlyPerformance = StaffPerformanceService::getMonthlyPerformance($user);

        return view('petugas.loket', compact('loket', 'statistik', 'antrianAktif', 'personalStats', 'goalProgress', 'weeklyPerformance', 'monthlyPerformance'));
    }

    /**
     * Get antrian list untuk ajax display
     */
    public function getAntrianList()
    {
        $user = Auth::user();
        $loket = $user->loket;

        $today = Carbon::today();

        // Antrian menunggu dan dipanggil
        $antrians = Antrian::where('layanan_id', $loket->layanan_id)
            ->whereDate('waktu_ambil', $today)
            ->whereIn('status', ['menunggu', 'dipanggil', 'dilayani'])
            ->with('layanan')
            ->orderBy('waktu_ambil', 'asc')
            ->get();

        // Antrian terakhir yang dipanggil
        $lastCalled = Antrian::where('loket_id', $loket->id)
            ->whereDate('waktu_ambil', $today)
            ->where('status', 'dipanggil')
            ->orderBy('waktu_panggil', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'antrians' => $antrians,
            'last_called' => $lastCalled,
        ]);
    }

    /**
     * Panggil antrian berikutnya
     */
    public function panggil(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        if (!$loket) {
            return response()->json(['success' => false, 'message' => 'Loket tidak ditemukan']);
        }

        // Ambil antrian pertama yang menunggu
        $antrian = Antrian::where('layanan_id', $loket->layanan_id)
            ->where('status', 'menunggu')
            ->whereDate('waktu_ambil', Carbon::today())
            ->orderBy('waktu_ambil', 'asc')
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian untuk dipanggil']);
        }

        $antrian->update([
            'status' => 'dipanggil',
            'loket_id' => $loket->id,
            'waktu_panggil' => now(),
        ]);

        LogActivityService::antrianCalled($antrian);

        // Broadcast event ke semua display
        broadcast(new AntrianDipanggil(
            $antrian,
            $loket->nama_loket,
            $loket->layanan->nama_layanan ?? 'Loket'
        ))->toOthers();

        return response()->json(['success' => true, 'antrian' => $antrian]);
    }

    /**
     * Layani antrian yang sedang dipanggil
     */
    public function layani(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        // Ambil antrian yang sedang dipanggil di loket ini
        $antrian = Antrian::where('loket_id', $loket->id)
            ->where('status', 'dipanggil')
            ->whereDate('waktu_ambil', Carbon::today())
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang dipanggil']);
        }
        
        $antrian->update([
            'status' => 'dilayani',
            'waktu_mulai_dilayani' => now(),
        ]);

        LogActivityService::antrianServed($antrian);

        return response()->json(['success' => true, 'antrian' => $antrian]);
    }

    /**
     * Selesaikan pelayanan antrian
     */
    public function selesai(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        // Ambil antrian yang sedang dilayani di loket ini
        $antrian = Antrian::where('loket_id', $loket->id)
            ->where('status', 'dilayani')
            ->whereDate('waktu_ambil', Carbon::today())
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dilayani']);
        }
        
        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        LogActivityService::antrianCompleted($antrian);

        return response()->json(['success' => true, 'antrian' => $antrian]);
    }

    /**
     * Batalkan antrian yang sedang dipanggil
     */
    public function batalkan(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        // Ambil antrian yang sedang dipanggil di loket ini
        $antrian = Antrian::where('loket_id', $loket->id)
            ->where('status', 'dipanggil')
            ->whereDate('waktu_ambil', Carbon::today())
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang dipanggil']);
        }
        
        $antrian->update([
            'status' => 'batal',
        ]);

        LogActivityService::antrianCancelled($antrian);

        return response()->json(['success' => true, 'antrian' => $antrian]);
    }

    public function tutupLoket(Request $request)
    {
        $user = Auth::user();
        $loket = $user->loket;

        $loket->update([
            'status' => $loket->status === 'aktif' ? 'tutup' : 'aktif',
        ]);

        return response()->json(['success' => true, 'status' => $loket->status]);
    }
}