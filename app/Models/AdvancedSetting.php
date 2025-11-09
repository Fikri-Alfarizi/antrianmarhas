<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedSetting extends Model
{
    protected $table = 'advanced_settings';

    protected $fillable = [
        'queue_timeout_minutes',
        'auto_cancel_timeout',
        'theme_color',
        'secondary_color',
        'display_refresh_seconds',
        'email_notification_enabled',
        'email_notification_recipient',
        'sms_notification_enabled',
        'sms_notification_number',
        'working_hours_start',
        'working_hours_end',
        'closed_days',
        'auto_assign_loket',
        'max_queue_per_loket',
        'enable_customer_feedback',
        'maintenance_mode',
    ];

    protected $casts = [
        'auto_cancel_timeout' => 'boolean',
        'email_notification_enabled' => 'boolean',
        'sms_notification_enabled' => 'boolean',
        'auto_assign_loket' => 'boolean',
        'enable_customer_feedback' => 'boolean',
        'closed_days' => 'array',
        'working_hours_start' => 'datetime:H:i',
        'working_hours_end' => 'datetime:H:i',
    ];
}
