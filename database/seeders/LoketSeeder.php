<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoketSeeder extends Seeder
{
    public function run(): void
    {
        $lokets = [
            [
                'nama_loket' => 'Ruang 1',
                'layanan_id' => 1, // Pemeriksaan Umum
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Ruang 2',
                'layanan_id' => 2, // Kesehatan Ibu dan Anak
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Ruang 3',
                'layanan_id' => 3, // Keluarga Berencana
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Ruang 4',
                'layanan_id' => 4, // Kesehatan Gigi dan Mulut
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lokets')->insert($lokets);
    }
}