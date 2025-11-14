<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanans = [
            [
                'nama_layanan' => 'Administrasi Siswa',
                'prefix' => 'A',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Pembayaran SPP',
                'prefix' => 'B',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Layanan Konseling',
                'prefix' => 'C',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Pengambilan Ijazah',
                'prefix' => 'D',
                'digit' => 3,
                'status' => 'aktif'
            ]
        ];

        foreach ($layanans as $layanan) {
            Layanan::create($layanan);
        }
    }
}
