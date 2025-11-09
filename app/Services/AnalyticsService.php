<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Layanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get daily queue statistics
     */
    public static function getDailyStats($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $stats = Antrian::whereDate('waktu_ambil', $date)->get();
        
        return [
            'date' => $date->format('Y-m-d'),
            'total_antrian' => $stats->count(),
            'selesai' => $stats->where('status', 'selesai')->count(),
            'batal' => $stats->where('status', 'batal')->count(),
            'menunggu' => $stats->where('status', 'menunggu')->count(),
            'completion_rate' => $stats->count() > 0 
                ? round(($stats->where('status', 'selesai')->count() / $stats->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get hourly distribution (peak hours)
     */
    public static function getHourlyDistribution($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $data = Antrian::whereDate('waktu_ambil', $date)
            ->selectRaw('HOUR(waktu_ambil) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Fill empty hours with 0
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = 0;
        }
        
        foreach ($data as $item) {
            $hours[$item->hour] = $item->count;
        }
        
        return array_map(function($count) {
            return $count;
        }, $hours);
    }

    /**
     * Get service performance statistics
     */
    public static function getServiceStats($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $layanans = Layanan::all();
        
        return $layanans->map(function($layanan) use ($date) {
            $antrians = Antrian::where('layanan_id', $layanan->id)
                ->whereDate('waktu_ambil', $date)
                ->get();
            
            $selesai = $antrians->where('status', 'selesai');
            $avgTime = self::calculateAverageServiceTime($selesai);
            
            return [
                'layanan_id' => $layanan->id,
                'nama_layanan' => $layanan->nama_layanan,
                'total' => $antrians->count(),
                'selesai' => $selesai->count(),
                'batal' => $antrians->where('status', 'batal')->count(),
                'menunggu' => $antrians->where('status', 'menunggu')->count(),
                'avg_service_time' => $avgTime,
            ];
        });
    }

    /**
     * Get loket performance statistics
     */
    public static function getLoketStats($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $lokets = Loket::with('layanan')->get();
        
        return $lokets->map(function($loket) use ($date) {
            $antrians = Antrian::where('loket_id', $loket->id)
                ->whereDate('waktu_ambil', $date)
                ->get();
            
            $selesai = $antrians->where('status', 'selesai');
            $avgTime = self::calculateAverageServiceTime($selesai);
            
            return [
                'loket_id' => $loket->id,
                'nama_loket' => $loket->nama_loket,
                'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
                'total' => $antrians->count(),
                'selesai' => $selesai->count(),
                'avg_service_time' => $avgTime,
                'efficiency' => $antrians->count() > 0 
                    ? round(($selesai->count() / $antrians->count()) * 100, 2)
                    : 0,
            ];
        });
    }

    /**
     * Get weekly comparison data
     */
    public static function getWeeklyComparison()
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Antrian::whereDate('waktu_ambil', $date)->count();
            
            $data[] = [
                'date' => $date->format('D'),
                'count' => $count,
            ];
        }
        
        return $data;
    }

    /**
     * Get top services (most visited)
     */
    public static function getTopServices($date = null, $limit = 5)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        return Antrian::where('status', 'selesai')
            ->whereDate('waktu_ambil', $date)
            ->selectRaw('layanan_id, COUNT(*) as count')
            ->with('layanan')
            ->groupBy('layanan_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'layanan' => $item->layanan->nama_layanan ?? 'Unknown',
                    'count' => $item->count,
                ];
            });
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
     * Get date range stats (for custom reports)
     */
    public static function getDateRangeStats($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $antrians = Antrian::whereBetween('waktu_ambil', [$startDate, $endDate])->get();
        
        return [
            'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
            'total_days' => $startDate->diffInDays($endDate) + 1,
            'total_antrian' => $antrians->count(),
            'total_selesai' => $antrians->where('status', 'selesai')->count(),
            'total_batal' => $antrians->where('status', 'batal')->count(),
            'avg_per_day' => round($antrians->count() / ($startDate->diffInDays($endDate) + 1), 2),
            'completion_rate' => $antrians->count() > 0
                ? round(($antrians->where('status', 'selesai')->count() / $antrians->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get current month stats summary
     */
    public static function getMonthStats($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();
        
        $antrians = Antrian::whereBetween('waktu_ambil', [$startDate, $endDate])->get();
        
        return [
            'month' => $startDate->format('F Y'),
            'total_antrian' => $antrians->count(),
            'total_selesai' => $antrians->where('status', 'selesai')->count(),
            'total_batal' => $antrians->where('status', 'batal')->count(),
            'completion_rate' => $antrians->count() > 0
                ? round(($antrians->where('status', 'selesai')->count() / $antrians->count()) * 100, 2)
                : 0,
            'avg_per_day' => round($antrians->count() / $startDate->diffInDays($endDate, false) + 1, 2),
        ];
    }
}
