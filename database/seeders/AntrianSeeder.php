<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\Loket;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AntrianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layanans = Layanan::all();
        $lokets = Loket::all();

        // Create test data for today
        $now = Carbon::now();

        // A001 - Menunggu
        Antrian::create([
            'layanan_id' => 1,
            'loket_id' => 1,
            'kode_antrian' => 'A001',
            'status' => 'menunggu',
            'waktu_ambil' => $now->copy()->subHours(2),
            'waktu_panggil' => null,
            'waktu_selesai' => null,
        ]);

        // A002 - Dipanggil
        Antrian::create([
            'layanan_id' => 1,
            'loket_id' => 1,
            'kode_antrian' => 'A002',
            'status' => 'dipanggil',
            'waktu_ambil' => $now->copy()->subHours(1)->subMinutes(30),
            'waktu_panggil' => $now->copy()->subMinutes(30),
            'waktu_selesai' => null,
        ]);

        // A003 - Dilayani
        Antrian::create([
            'layanan_id' => 1,
            'loket_id' => 1,
            'kode_antrian' => 'A003',
            'status' => 'dilayani',
            'waktu_ambil' => $now->copy()->subHours(1),
            'waktu_panggil' => $now->copy()->subMinutes(50),
            'waktu_selesai' => null,
        ]);

        // A004 - Selesai
        Antrian::create([
            'layanan_id' => 1,
            'loket_id' => 1,
            'kode_antrian' => 'A004',
            'status' => 'selesai',
            'waktu_ambil' => $now->copy()->subHours(1)->subMinutes(45),
            'waktu_panggil' => $now->copy()->subHours(1)->subMinutes(15),
            'waktu_selesai' => $now->copy()->subMinutes(20),
        ]);

        // B001 - Menunggu for layanan 2
        Antrian::create([
            'layanan_id' => 2,
            'loket_id' => 2,
            'kode_antrian' => 'B001',
            'status' => 'menunggu',
            'waktu_ambil' => $now->copy()->subMinutes(45),
            'waktu_panggil' => null,
            'waktu_selesai' => null,
        ]);

        // B002 - Selesai
        Antrian::create([
            'layanan_id' => 2,
            'loket_id' => 2,
            'kode_antrian' => 'B002',
            'status' => 'selesai',
            'waktu_ambil' => $now->copy()->subHours(1)->subMinutes(20),
            'waktu_panggil' => $now->copy()->subHours(1),
            'waktu_selesai' => $now->copy()->subMinutes(35),
        ]);

        // C001 - Menunggu for layanan 3
        Antrian::create([
            'layanan_id' => 3,
            'loket_id' => 3,
            'kode_antrian' => 'C001',
            'status' => 'menunggu',
            'waktu_ambil' => $now->copy()->subMinutes(25),
            'waktu_panggil' => null,
            'waktu_selesai' => null,
        ]);

        // C002 - Batal
        Antrian::create([
            'layanan_id' => 3,
            'loket_id' => 3,
            'kode_antrian' => 'C002',
            'status' => 'batal',
            'waktu_ambil' => $now->copy()->subMinutes(60),
            'waktu_panggil' => null,
            'waktu_selesai' => $now->copy()->subMinutes(50),
        ]);
    }
}
