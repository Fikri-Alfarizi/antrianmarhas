<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicePrinter extends Model
{
    use HasFactory;

    protected $table = 'device_printers';

    protected $fillable = [
        'nama_device',
        'mac_address',
        'status',
        'last_connected_at'
    ];

    protected $casts = [
        'last_connected_at' => 'datetime'
    ];
}
