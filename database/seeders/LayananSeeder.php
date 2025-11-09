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
                'nama_layanan' => 'Pemeriksaan Umum',
                'prefix' => 'A',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Kesehatan Ibu dan Anak',
                'prefix' => 'B',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Keluarga Berencana',
                'prefix' => 'C',
                'digit' => 3,
                'status' => 'aktif'
            ],
            [
                'nama_layanan' => 'Kesehatan Gigi dan Mulut',
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
