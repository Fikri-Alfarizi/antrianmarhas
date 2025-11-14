<?php

namespace Database\Seeders;

use App\Models\AudioSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AudioSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AudioSetting::firstOrCreate(
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
    }
}
