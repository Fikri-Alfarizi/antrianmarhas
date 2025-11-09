<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudioSetting;
use Illuminate\Http\Request;

class AudioSettingController extends Controller
{
    public function index()
    {
        $setting = AudioSetting::first() ?? new AudioSetting();
        return view('admin.audio-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:text-to-speech,audio-file',
            'bahasa' => 'required|string',
            'volume' => 'required|integer|min:0|max:100',
            'aktif' => 'boolean',
            'format_pesan' => 'required|string',
        ]);

        $setting = AudioSetting::first() ?? new AudioSetting();
        $setting->fill($validated);
        $setting->save();

        return redirect()->route('admin.audio-settings.index')
            ->with('success', 'Pengaturan audio berhasil diperbarui');
    }

    public function testAudio()
    {
        $setting = AudioSetting::first();
        if (!$setting) {
            return response()->json(['error' => 'Setting tidak ditemukan'], 404);
        }

        // Generate test audio
        $testText = "Nomor Anda adalah A 0 0 1. Silakan menuju ke Loket Satu.";
        $audioUrl = \App\Services\AudioService::generateTTSUrl($testText, $setting->bahasa);

        return response()->json(['audioUrl' => $audioUrl, 'text' => $testText]);
    }
}
