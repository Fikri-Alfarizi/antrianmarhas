<?php

use Illuminate\Support\Facades\Route;
use App\Models\Antrian;
use Carbon\Carbon;

Route::get('/display/riwayat-antrian', function() {
    $today = Carbon::today();
    $riwayat = Antrian::whereDate('waktu_ambil', $today)
        ->orderByDesc('waktu_ambil')
        ->limit(30)
        ->get()
        ->map(function($a) {
            return [
                'kode_antrian' => $a->kode_antrian,
                'layanan' => $a->layanan_nama ?? ($a->layanan->nama_layanan ?? '-'),
                'waktu' => $a->waktu_ambil ? $a->waktu_ambil->format('H:i:s') : '',
            ];
        });
    return response()->json(['riwayat' => $riwayat]);
});
