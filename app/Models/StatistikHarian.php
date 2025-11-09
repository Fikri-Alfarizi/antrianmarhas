<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatistikHarian extends Model
{
    use HasFactory;

    protected $table = 'statistik_harian';

    protected $fillable = [
        'tanggal',
        'layanan_id',
        'total_menunggu',
        'total_dilayani',
        'total_selesai',
        'total_batal'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}
