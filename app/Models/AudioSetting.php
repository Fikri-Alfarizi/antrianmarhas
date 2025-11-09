<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioSetting extends Model
{
    protected $table = 'audio_settings';
    
    protected $fillable = [
        'tipe',
        'bahasa',
        'volume',
        'aktif',
        'format_pesan',
        'voice_url',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
