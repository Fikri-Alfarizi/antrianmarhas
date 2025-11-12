<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Loket;
use App\Models\Layanan;
use App\Models\Antrian;
use Carbon\Carbon;

class DiagnosticController extends Controller
{
    /**
     * Diagnostic endpoint untuk debug antrian
     */
    public function diagnostics()
    {
        $user = Auth::user();
        
        // 1. Check user
        $userInfo = [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'loket_id' => $user->loket_id,
            'has_loket' => !is_null($user->loket_id),
        ];

        // 2. Check loket
        $loketInfo = null;
        $loket = null;
        if ($user->loket_id) {
            $loket = $user->loket;
            $loketInfo = [
                'id' => $loket->id ?? null,
                'nama_loket' => $loket->nama_loket ?? null,
                'layanan_id' => $loket->layanan_id ?? null,
                'layanan_name' => $loket->layanan->nama_layanan ?? 'N/A',
                'status' => $loket->status ?? null,
            ];
        }

        // 3. Check antrian menunggu
        $today = Carbon::today();
        $allWaiting = [];
        $matchingWaiting = [];

        if ($loket && $loket->layanan_id) {
            // Ambil semua antrian menunggu hari ini
            $allWaiting = Antrian::where('status', 'menunggu')
                ->whereDate('waktu_ambil', $today)
                ->get()
                ->toArray();

            // Ambil antrian menunggu yang match dengan layanan loket
            $matchingWaiting = Antrian::where('layanan_id', $loket->layanan_id)
                ->where('status', 'menunggu')
                ->whereDate('waktu_ambil', $today)
                ->get()
                ->toArray();
        }

        // 4. Check layanan
        $layanans = Layanan::where('status', 'aktif')->get()->toArray();

        // 5. Check lokets
        $lokets = Loket::with('layanan')->get()->toArray();

        return response()->json([
            'timestamp' => now(),
            'user' => $userInfo,
            'loket' => $loketInfo,
            'all_waiting_antrian_today' => count($allWaiting),
            'all_waiting_antrian_list' => $allWaiting,
            'matching_waiting_antrian' => count($matchingWaiting),
            'matching_waiting_antrian_list' => $matchingWaiting,
            'active_layanans' => count($layanans),
            'layanans_list' => $layanans,
            'all_lokets' => count($lokets),
            'lokets_list' => $lokets,
        ]);
    }
}
