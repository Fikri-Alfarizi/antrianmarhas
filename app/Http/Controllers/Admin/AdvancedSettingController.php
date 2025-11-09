<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedSetting;
use Illuminate\Http\Request;

class AdvancedSettingController extends Controller
{
    public function index()
    {
        $settings = AdvancedSetting::first() ?? new AdvancedSetting();
        
        $closedDays = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        return view('admin.advanced-settings.index', compact('settings', 'closedDays'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'queue_timeout_minutes' => 'required|integer|min:5|max:240',
            'auto_cancel_timeout' => 'boolean',
            'theme_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'display_refresh_seconds' => 'required|integer|min:1|max:60',
            'email_notification_enabled' => 'boolean',
            'email_notification_recipient' => 'nullable|email',
            'sms_notification_enabled' => 'boolean',
            'sms_notification_number' => 'nullable|regex:/^(\+62|0)[0-9]{9,12}$/',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i',
            'closed_days' => 'nullable|array',
            'auto_assign_loket' => 'boolean',
            'max_queue_per_loket' => 'required|integer|min:10|max:500',
            'enable_customer_feedback' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        $settings = AdvancedSetting::first() ?? new AdvancedSetting();
        
        $validated['closed_days'] = $request->input('closed_days', []);
        $validated['auto_cancel_timeout'] = $request->has('auto_cancel_timeout');
        $validated['email_notification_enabled'] = $request->has('email_notification_enabled');
        $validated['sms_notification_enabled'] = $request->has('sms_notification_enabled');
        $validated['auto_assign_loket'] = $request->has('auto_assign_loket');
        $validated['enable_customer_feedback'] = $request->has('enable_customer_feedback');
        $validated['maintenance_mode'] = $request->has('maintenance_mode') ? 1 : 0;
        
        $settings->fill($validated)->save();

        return redirect()->route('admin.advanced-settings.index')
            ->with('success', 'Pengaturan lanjutan berhasil diperbarui');
    }
}
