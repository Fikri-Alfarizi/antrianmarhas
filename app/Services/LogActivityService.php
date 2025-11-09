<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Antrian;
use Illuminate\Support\Facades\Auth;

class LogActivityService
{
    public static function log($aktivitas)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'aktivitas' => $aktivitas,
                'waktu' => now(),
                'ip_address' => request()->ip(),
            ]);
        }
    }

    public static function antrianCreated(Antrian $antrian)
    {
        self::log("Antrian {$antrian->kode_antrian} dibuat");
    }

    public static function antrianCalled(Antrian $antrian)
    {
        self::log("Memanggil antrian {$antrian->kode_antrian}");
    }

    public static function antrianServed(Antrian $antrian)
    {
        self::log("Melayani antrian {$antrian->kode_antrian}");
    }

    public static function antrianCompleted(Antrian $antrian)
    {
        self::log("Menyelesaikan antrian {$antrian->kode_antrian}");
    }

    public static function antrianCancelled(Antrian $antrian)
    {
        self::log("Membatalkan antrian {$antrian->kode_antrian}");
    }
}