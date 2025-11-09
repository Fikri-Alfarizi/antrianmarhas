<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\Loket;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $statistik = [
            'total_antrian' => Antrian::whereDate('waktu_ambil', $today)->count(),
            'menunggu' => Antrian::whereDate('waktu_ambil', $today)->where('status', 'menunggu')->count(),
            'dipanggil' => Antrian::whereDate('waktu_ambil', $today)->where('status', 'dipanggil')->count(),
            'dilayani' => Antrian::whereDate('waktu_ambil', $today)->where('status', 'dilayani')->count(),
            'selesai' => Antrian::whereDate('waktu_ambil', $today)->where('status', 'selesai')->count(),
            'batal' => Antrian::whereDate('waktu_ambil', $today)->where('status', 'batal')->count(),
        ];

        $statistikLayanan = Layanan::select('layanans.id', 'layanans.nama_layanan')
            ->selectRaw('COUNT(antrians.id) as total')
            ->selectRaw('SUM(CASE WHEN antrians.status = "menunggu" THEN 1 ELSE 0 END) as menunggu')
            ->selectRaw('SUM(CASE WHEN antrians.status = "dilayani" THEN 1 ELSE 0 END) as dilayani')
            ->selectRaw('SUM(CASE WHEN antrians.status = "selesai" THEN 1 ELSE 0 END) as selesai')
            ->leftJoin('antrians', function($join) use ($today) {
                $join->on('layanans.id', '=', 'antrians.layanan_id')
                     ->whereDate('antrians.waktu_ambil', $today);
            })
            ->groupBy('layanans.id', 'layanans.nama_layanan')
            ->get();

        $total_layanan = Layanan::count();
        $total_loket = Loket::count();
        $total_pengguna = User::count();

        return view('admin.dashboard', compact('statistik', 'statistikLayanan', 'total_layanan', 'total_loket', 'total_pengguna'));
    }
}