<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate untuk preserve logo jika sudah ada
        Pengaturan::firstOrCreate(
            ['id' => 1],
            [
                'logo' => 'storage/logos/logo-1762967705.png', // Default logo lokal
                'nama_instansi' => 'RSUD Marhas Medika',
                'alamat' => 'Jl. Kesehatan No. 123, Jakarta Selatan',
                'telepon' => '021-12345678',
                'deskripsi' => 'Rumah Sakit Umum Daerah yang melayani dengan sepenuh hati'
            ]
        );
    }
}
