<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use Carbon\Carbon;

class WaitTimeService
{
    /**
     * Hitung rata-rata durasi pelayanan per loket hari ini
     */
    public static function getAverageServiceTime(Loket $loket)
    {
        $today = Carbon::today();
        
        $completed = Antrian::where('loket_id', $loket->id)
            ->whereDate('waktu_ambil', $today)
            ->whereIn('status', ['selesai', 'batal'])
            ->whereNotNull('waktu_mulai_dilayani')
            ->whereNotNull('waktu_selesai')
            ->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalDuration = 0;
        $count = 0;

        foreach ($completed as $antrian) {
            if ($antrian->waktu_mulai_dilayani && $antrian->waktu_selesai) {
                $duration = $antrian->waktu_selesai->diffInMinutes($antrian->waktu_mulai_dilayani);
                $totalDuration += $duration;
                $count++;
            }
        }

        return $count > 0 ? round($totalDuration / $count, 1) : 0;
    }

    /**
     * Hitung estimasi waktu tunggu untuk loket tertentu
     */
    public static function getEstimatedWaitTime(Loket $loket)
    {
        $today = Carbon::today();
        
        // Hitung jumlah antrian yang sedang menunggu
        $waiting = Antrian::where('loket_id', $loket->id)
            ->whereDate('waktu_ambil', $today)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->count();

        // Ambil rata-rata waktu pelayanan
        $avgTime = self::getAverageServiceTime($loket);

        // Estimasi = jumlah menunggu * rata-rata waktu pelayanan
        return max(0, round($waiting * $avgTime));
    }

    /**
     * Get estimated wait time untuk semua loket
     */
    public static function getAllEstimatedWaitTimes()
    {
        $lokets = Loket::with('layanan')->get();
        
        return $lokets->map(function ($loket) {
            return [
                'loket_id' => $loket->id,
                'nama_loket' => $loket->nama_loket,
                'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
                'estimated_minutes' => self::getEstimatedWaitTime($loket),
                'average_service_time' => self::getAverageServiceTime($loket),
            ];
        });
    }

    /**
     * Format waktu tunggu menjadi readable text
     */
    public static function formatWaitTime($minutes)
    {
        if ($minutes <= 0) {
            return "Segera";
        } elseif ($minutes < 1) {
            return "< 1 menit";
        } elseif ($minutes == 1) {
            return "1 menit";
        } elseif ($minutes < 60) {
            return "{$minutes} menit";
        } else {
            $hours = intval($minutes / 60);
            $mins = $minutes % 60;
            return "{$hours}jam {$mins}menit";
        }
    }
}
