<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisplayLog extends Model
{
    use HasFactory;

    protected $table = 'display_logs';

    protected $fillable = [
        'antrian_id',
        'loket_id',
        'pesan_display',
        'status_warna',
        'waktu_tampil'
    ];

    protected $casts = [
        'waktu_tampil' => 'datetime'
    ];

    public function antrian(): BelongsTo
    {
        return $this->belongsTo(Antrian::class, 'antrian_id');
    }

    public function loket(): BelongsTo
    {
        return $this->belongsTo(Loket::class, 'loket_id');
    }
}
