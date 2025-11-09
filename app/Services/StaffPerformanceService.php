<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\User;
use App\Models\Loket;
use Carbon\Carbon;

class StaffPerformanceService
{
    /**
     * Get personal performance stats untuk staff
     */
    public static function getPersonalStats(User $user, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $loket = $user->loket;
        if (!$loket) {
            return null;
        }

        $antrians = Antrian::where('loket_id', $loket->id)
            ->whereDate('waktu_ambil', $date)
            ->get();

        $selesai = $antrians->where('status', 'selesai');
        $batal = $antrians->where('status', 'batal');
        
        $avgServiceTime = self::calculateAverageServiceTime($selesai);
        $totalServiceTime = self::calculateTotalServiceTime($selesai);

        return [
            'nama_staff' => $user->name,
            'loket' => $loket->nama_loket,
            'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
            'total_antrian' => $antrians->count(),
            'selesai' => $selesai->count(),
            'batal' => $batal->count(),
            'menunggu' => $antrians->where('status', 'menunggu')->count(),
            'avg_service_time' => $avgServiceTime,
            'total_service_time' => $totalServiceTime,
            'efficiency' => $antrians->count() > 0 
                ? round(($selesai->count() / $antrians->count()) * 100, 2)
                : 0,
            'date' => $date->format('Y-m-d'),
        ];
    }

    /**
     * Get staff rankings
     */
    public static function getStaffRankings($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $users = User::where('role', 'petugas')
            ->with('loket')
            ->get();

        $rankings = $users->map(function($user) use ($date) {
            $stats = self::getPersonalStats($user, $date);
            if (!$stats) {
                return null;
            }
            return $stats;
        })->filter()
          ->sortByDesc('efficiency')
          ->values();

        return $rankings;
    }

    /**
     * Get top performers
     */
    public static function getTopPerformers($limit = 5, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        return self::getStaffRankings($date)
            ->take($limit);
    }

    /**
     * Get weekly performance trend
     */
    public static function getWeeklyPerformance(User $user)
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $stats = self::getPersonalStats($user, $date);
            
            if ($stats) {
                $data[] = [
                    'date' => $date->format('D'),
                    'completed' => $stats['selesai'],
                    'efficiency' => $stats['efficiency'],
                    'avg_time' => $stats['avg_service_time'],
                ];
            }
        }
        
        return $data;
    }

    /**
     * Get monthly performance
     */
    public static function getMonthlyPerformance(User $user, $year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $loket = $user->loket;
        if (!$loket) {
            return null;
        }

        $antrians = Antrian::where('loket_id', $loket->id)
            ->whereBetween('waktu_ambil', [$startDate, $endDate])
            ->get();

        $selesai = $antrians->where('status', 'selesai');
        
        return [
            'month' => $startDate->format('F Y'),
            'nama_staff' => $user->name,
            'loket' => $loket->nama_loket,
            'total_antrian' => $antrians->count(),
            'total_selesai' => $selesai->count(),
            'total_batal' => $antrians->where('status', 'batal')->count(),
            'avg_service_time' => self::calculateAverageServiceTime($selesai),
            'total_service_time' => self::calculateTotalServiceTime($selesai),
            'efficiency' => $antrians->count() > 0
                ? round(($selesai->count() / $antrians->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get team performance
     */
    public static function getTeamPerformance($layananId = null, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $lokets = Loket::when($layananId, function($query) use ($layananId) {
            $query->where('layanan_id', $layananId);
        })->with('layanan')->get();

        return $lokets->map(function($loket) use ($date) {
            $user = User::where('loket_id', $loket->id)->first();
            
            if (!$user) {
                return null;
            }

            return self::getPersonalStats($user, $date);
        })->filter();
    }

    /**
     * Calculate average service time
     */
    private static function calculateAverageServiceTime($antrians)
    {
        if ($antrians->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count = 0;

        foreach ($antrians as $antrian) {
            if ($antrian->waktu_mulai_dilayani && $antrian->waktu_selesai) {
                $duration = $antrian->waktu_selesai->diffInMinutes($antrian->waktu_mulai_dilayani);
                $totalTime += $duration;
                $count++;
            }
        }

        return $count > 0 ? round($totalTime / $count, 1) : 0;
    }

    /**
     * Calculate total service time in minutes
     */
    private static function calculateTotalServiceTime($antrians)
    {
        $totalTime = 0;

        foreach ($antrians as $antrian) {
            if ($antrian->waktu_mulai_dilayani && $antrian->waktu_selesai) {
                $duration = $antrian->waktu_selesai->diffInMinutes($antrian->waktu_mulai_dilayani);
                $totalTime += $duration;
            }
        }

        return $totalTime;
    }

    /**
     * Get staff goal progress
     */
    public static function getGoalProgress(User $user, $targetPerDay = 50)
    {
        $today = Carbon::today();
        
        $stats = self::getPersonalStats($user, $today);
        if (!$stats) {
            return null;
        }

        $progress = round(($stats['selesai'] / $targetPerDay) * 100, 1);
        $remaining = max(0, $targetPerDay - $stats['selesai']);

        return [
            'target' => $targetPerDay,
            'completed' => $stats['selesai'],
            'remaining' => $remaining,
            'progress_percentage' => min(100, $progress),
            'status' => $progress >= 100 ? 'achieved' : ($progress >= 75 ? 'on_track' : 'behind'),
        ];
    }
}
