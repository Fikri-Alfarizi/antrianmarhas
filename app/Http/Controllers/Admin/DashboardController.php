<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Antrian;
use App\Models\Loket;
use App\Services\AnalyticsService; // Pastikan Anda memiliki service ini
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index()
    {
        // 1. Ambil Statistik Harian
        // Kita gunakan AnalyticsService yang sudah Anda buat
        $stats = AnalyticsService::getDailyStats(Carbon::today());
        
        // 2. Ambil Status Loket
        $lokets = Loket::with('layanan')->orderBy('nama_loket', 'asc')->get();
        
        // 3. Ambil Antrian Menunggu Teratas
        $antrianMenunggu = Antrian::where('status', 'menunggu')
                            ->whereDate('waktu_ambil', Carbon::today())
                            ->with('layanan')
                            ->orderBy('waktu_ambil', 'asc')
                            ->limit(10)
                            ->get();
                            
        // 4. Ambil Aktivitas Terbaru
        $activities = ActivityLog::with('user')
                            ->orderBy('waktu', 'desc')
                            ->limit(10)
                            ->get();

        return view('admin.dashboard', compact('stats', 'lokets', 'antrianMenunggu', 'activities'));
    }
}