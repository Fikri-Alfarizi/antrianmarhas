<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioSetting extends Model
{
    use HasFactory;
    
    // Gunakan nama tabel yang ada di migrasi Anda
    protected $table = 'audio_settings';

    // Izinkan pengisian massal untuk kolom-kolom ini
    protected $fillable = [
        'tipe',
        'bahasa',
        'volume',
        'aktif',
        'format_pesan',
        'voice_url', // Kolom ini ada di migrasi Anda
    ];

    /**
     * Cast (Konversi tipe data)
     */
    protected $casts = [
        'aktif' => 'boolean',
        'volume' => 'integer',
    ];
}