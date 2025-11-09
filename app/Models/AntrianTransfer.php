<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AntrianTransfer extends Model
{
    protected $table = 'antrian_transfers';

    protected $fillable = [
        'antrian_id',
        'from_layanan_id',
        'to_layanan_id',
        'from_loket_id',
        'to_loket_id',
        'kode_antrian_baru',
        'alasan_transfer',
        'status',
        'transferred_by',
        'transferred_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
    ];

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    public function fromLayanan()
    {
        return $this->belongsTo(Layanan::class, 'from_layanan_id');
    }

    public function toLayanan()
    {
        return $this->belongsTo(Layanan::class, 'to_layanan_id');
    }

    public function fromLoket()
    {
        return $this->belongsTo(Loket::class, 'from_loket_id');
    }

    public function toLoket()
    {
        return $this->belongsTo(Loket::class, 'to_loket_id');
    }
}
