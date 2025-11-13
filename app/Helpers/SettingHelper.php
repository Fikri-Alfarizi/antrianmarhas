<?php

namespace App\Helpers;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    /**
     * Get setting value dari cache atau database
     */
    public static function get($key = null, $default = null)
    {
        $setting = Cache::remember('app_setting', 3600, function () {
            return Pengaturan::first();
        });

        if ($key) {
            return $setting?->{$key} ?? $default;
        }

        return $setting;
    }

    /**
     * Get nama instansi
     */
    public static function getNamaInstansi()
    {
        return self::get('nama_instansi', 'Antrian Ruang Coding');
    }

    /**
     * Get logo path
     */
    public static function getLogo()
    {
        return self::get('logo');
    }

    /**
     * Flush cache setting
     */
    public static function flush()
    {
        Cache::forget('app_setting');
    }
}
