<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffActivityLog extends Model
{
    use HasFactory;

    protected $table = 'staff_activity_logs';

    protected $fillable = [
        'user_id',
        'activity',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function logActivity($userId, $activity, $status = 'active')
    {
        return self::updateOrCreate(
            ['user_id' => $userId],
            [
                'activity' => $activity,
                'status' => $status,
                'last_activity_at' => now(),
            ]
        );
    }
}
