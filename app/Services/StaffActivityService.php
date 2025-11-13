<?php

namespace App\Services;

use App\Models\StaffActivityLog;
use Illuminate\Support\Facades\Auth;

class StaffActivityService
{
    /**
     * Log aktivitas staff
     */
    public static function log($activity, $status = 'active')
    {
        if (!Auth::check()) {
            return null;
        }

        return StaffActivityLog::logActivity(Auth::id(), $activity, $status);
    }

    /**
     * Mark staff as active
     */
    public static function markActive($activity = 'online')
    {
        if (!Auth::check()) {
            return null;
        }

        return StaffActivityLog::logActivity(Auth::id(), $activity, 'active');
    }

    /**
     * Mark staff as idle
     */
    public static function markIdle()
    {
        if (!Auth::check()) {
            return null;
        }

        return StaffActivityLog::logActivity(Auth::id(), 'idle', 'idle');
    }

    /**
     * Mark staff as offline
     */
    public static function markOffline()
    {
        if (!Auth::check()) {
            return null;
        }

        return StaffActivityLog::logActivity(Auth::id(), 'logout', 'offline');
    }

    /**
     * Get staff status
     */
    public static function getStatus($userId)
    {
        return StaffActivityLog::where('user_id', $userId)->first();
    }

    /**
     * Get all active staff
     */
    public static function getActiveStaff()
    {
        return StaffActivityLog::where('status', '!=', 'offline')
            ->with('user')
            ->get();
    }
}
