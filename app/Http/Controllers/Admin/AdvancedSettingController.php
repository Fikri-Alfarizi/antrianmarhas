<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedSetting;
use Illuminate\Http\Request;

class AdvancedSettingController extends Controller
{
    /**
     * Menampilkan form pengaturan lanjutan.
     */
    public function index()
    {
        // Ambil pengaturan, atau buat baru jika tabel masih kosong
        $setting = AdvancedSetting::firstOrCreate(['id' => 1]);
        
        return view('admin.advanced-settings.index', compact('setting'));
    }

    /**
     * Menyimpan pembaruan pengaturan lanjutan.
     */
    public function update(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'queue_timeout_minutes' => 'required|integer|min:0',
            'auto_cancel_timeout' => 'nullable', // Checkbox
            'theme_color' => 'required|string|max:7',
            'display_refresh_seconds' => 'required|integer|min:1',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i',
        ]);

        // Cari atau buat pengaturan
        $setting = AdvancedSetting::firstOrCreate(['id' => 1]);

        // Handle checkbox (jika tidak dicentang, request tidak mengirimkan nilainya)
        $validatedData['auto_cancel_timeout'] = $request->has('auto_cancel_timeout');

        // Update data
        $setting->update($validatedData);

        return redirect()->route('admin.advanced-settings.index')
                         ->with('success', 'Pengaturan lanjutan berhasil disimpan.');
    }
}