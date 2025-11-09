<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanans';

    protected $fillable = [
        'nama_layanan',
        'prefix',
        'digit',
        'status'
    ];

    public function lokets(): HasMany
    {
        return $this->hasMany(Loket::class, 'layanan_id');
    }

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class, 'layanan_id');
    }

    public function statistikHarians(): HasMany
    {
        return $this->hasMany(StatistikHarian::class, 'layanan_id');
    }
}
