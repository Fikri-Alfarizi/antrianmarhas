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
                'logo' => 'SMKMarhas.png', // Default logo di public/logo/SMKMarhas.png
                'nama_instansi' => 'SMK Marhas Margahayu',
                'alamat' => 'Jl. Terusan Kopo No.385/299 ',
                'telepon' => '(022) 5410926',
                'deskripsi' => 'Sekolah SMK Pusat Keunggulan'
            ]
        );
    }
}
