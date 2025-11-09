<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    protected $fillable = [
        'antrian_id',
        'kode_antrian',
        'print_count',
        'last_printed_at',
        'printed_by',
    ];

    protected $casts = [
        'last_printed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }
}
