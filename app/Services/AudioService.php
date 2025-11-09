<?php

namespace App\Services;

use App\Models\AudioSetting;
use App\Models\Antrian;
use App\Models\Loket;

class AudioService
{
    /**
     * Generate audio text untuk panggilan antrian
     */
    public static function generateAudioText(Antrian $antrian)
    {
        $setting = AudioSetting::first() ?? new AudioSetting();
        
        $loket = $antrian->loket ? $antrian->loket->nama_loket : 'Loket';
        
        $pesan = str_replace(
            ['{nomor}', '{lokasi}'],
            [$antrian->kode_antrian, $loket],
            $setting->format_pesan
        );
        
        return $pesan;
    }

    /**
     * Generate URL TTS menggunakan Google Translate atau service lain
     */
    public static function generateTTSUrl(string $text, string $bahasa = 'id')
    {
        // Menggunakan Google Text-to-Speech API atau free service
        // Format: https://translate.google.com/translate_tts?ie=UTF-8&q={text}&tl={lang}
        
        $text = urlencode($text);
        return "https://translate.google.com/translate_tts?ie=UTF-8&q={$text}&tl={$bahasa}&client=tw-ob";
    }

    /**
     * Generate HTML audio tag untuk embed di view
     */
    public static function generateAudioTag(Antrian $antrian, string $id = 'audioNotifikasi')
    {
        $setting = AudioSetting::first();
        
        if (!$setting || !$setting->aktif) {
            return '';
        }

        $audioText = self::generateAudioText($antrian);
        $audioUrl = self::generateTTSUrl($audioText, $setting->bahasa);
        $volume = ($setting->volume / 100); // convert 0-100 to 0-1

        return <<<HTML
        <audio id="{$id}" preload="auto" style="display:none;">
            <source src="{$audioUrl}" type="audio/mpeg">
        </audio>
        <script>
            const audio = document.getElementById('{$id}');
            audio.volume = {$volume};
            audio.play().catch(err => console.log('Audio play error:', err));
        </script>
        HTML;
    }
}
