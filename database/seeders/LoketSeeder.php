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
                'nama_loket' => 'Loket Administrasi',
                'layanan_id' => 1, // Administrasi Siswa
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Loket SPP',
                'layanan_id' => 2, // Pembayaran SPP
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Loket Konseling',
                'layanan_id' => 3, // Layanan Konseling
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_loket' => 'Loket Ijazah',
                'layanan_id' => 4, // Pengambilan Ijazah
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lokets')->insert($lokets);
    }
}