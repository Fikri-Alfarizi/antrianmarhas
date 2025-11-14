<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AudioSetting;
use Illuminate\Http\Request;

class AudioSettingController extends Controller
{
    /**
     * Display the audio settings page
     */
    public function index()
    {
        $audioSetting = AudioSetting::firstOrCreate(
            ['id' => 1],
            [
                'tipe' => 'text-to-speech',
                'bahasa' => 'id',
                'volume' => 80,
                'aktif' => true,
                'format_pesan' => 'Nomor antrian {nomor} silakan menuju ke {lokasi} di SMK Marhas Margahayu',
                'voice_url' => null,
            ]
        );

        // List available languages (only Indonesian and English)
        $languages = [
            'id' => 'Bahasa Indonesia',
            'en' => 'English',
        ];

        // List available types
        $types = [
            'text-to-speech' => 'Text-to-Speech',
            'audio-file' => 'File Audio Custom',
        ];

        return view('admin.audio_setting.index', compact('audioSetting', 'languages', 'types'));
    }

    /**
     * Update audio settings
     */
    public function update(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'tipe' => 'required|in:text-to-speech,audio-file',
            'bahasa' => 'required|in:id,en',
            'volume' => 'required|integer|min:0|max:100',
            'aktif' => 'nullable|boolean',
            'format_pesan' => 'required|string|max:500',
            'voice_url' => 'nullable|url',
        ]);

        // Get or create audio setting
        $audioSetting = AudioSetting::firstOrCreate(['id' => 1]);

        // Update with validated data
        $audioSetting->update([
            'tipe' => $validated['tipe'],
            'bahasa' => $validated['bahasa'],
            'volume' => $validated['volume'],
            'aktif' => $request->has('aktif'),
            'format_pesan' => $validated['format_pesan'],
            'voice_url' => $validated['voice_url'] ?? $audioSetting->voice_url,
        ]);

        // Broadcast update event for real-time sync
        if (class_exists('App\Events\AudioSettingUpdated')) {
            broadcast(new \App\Events\AudioSettingUpdated($audioSetting));
        }

        return redirect()->route('admin.audio_setting.index')
            ->with('success', 'Pengaturan audio berhasil diperbarui. Perubahan akan langsung terlihat di semua layar.');
    }

    /**
     * Get audio settings as JSON (for AJAX/API)
     */
    public function getSettings()
    {
        $audioSetting = AudioSetting::firstOrCreate(
            ['id' => 1],
            [
                'tipe' => 'text-to-speech',
                'bahasa' => 'id',
                'volume' => 80,
                'aktif' => true,
                'format_pesan' => 'Nomor antrian {nomor} silakan menuju ke {lokasi} di SMK Marhas Margahayu',
                'voice_url' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $audioSetting,
        ]);
    }

    /**
     * Test audio announcement
     */
    public function testAudio(Request $request)
    {
        $request->validate([
            'nomor' => 'required|string',
            'lokasi' => 'required|string',
        ]);

        $audioSetting = AudioSetting::first();
        if (!$audioSetting) {
            return response()->json(['success' => false, 'message' => 'Audio setting tidak ditemukan'], 404);
        }

        // Format the message
        $pesan = str_replace(
            ['{nomor}', '{lokasi}'],
            [$request->nomor, $request->lokasi],
            $audioSetting->format_pesan
        );

        return response()->json([
            'success' => true,
            'message' => 'Test audio siap diputar',
            'pesan' => $pesan,
            'tipe' => $audioSetting->tipe,
            'bahasa' => $audioSetting->bahasa,
            'volume' => $audioSetting->volume,
        ]);
    }
}
