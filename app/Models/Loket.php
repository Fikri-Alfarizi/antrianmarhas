<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loket extends Model
{
    use HasFactory;

    protected $table = 'lokets';

    protected $fillable = [
        'nama_loket',
        'layanan_id',
        'status'
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class, 'loket_id');
    }

    public function displayLogs(): HasMany
    {
        return $this->hasMany(DisplayLog::class, 'loket_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'loket_id');
    }
}
