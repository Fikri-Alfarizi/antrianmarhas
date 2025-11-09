<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrians';

    protected $fillable = [
        'kode_antrian',
        'layanan_id',
        'loket_id',
        'status',
        'waktu_ambil',
        'waktu_panggil',
        'waktu_selesai',
        'qr_code'
    ];

    protected $casts = [
        'waktu_ambil' => 'datetime',
        'waktu_panggil' => 'datetime',
        'waktu_selesai' => 'datetime'
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function loket(): BelongsTo
    {
        return $this->belongsTo(Loket::class, 'loket_id');
    }

    public function displayLogs(): HasMany
    {
        return $this->hasMany(DisplayLog::class, 'antrian_id');
    }
}
