<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AntrianController extends Controller
{
    public function index(Request $request)
    {
        $query = Antrian::with(['layanan', 'loket']);

        // Filter berdasarkan layanan
        if ($request->filled('layanan')) {
            $query->where('layanan_id', $request->layanan);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_ambil', $request->tanggal);
        } else {
            // Default hari ini
            $query->whereDate('waktu_ambil', Carbon::today());
        }

        $antrians = $query->latest('waktu_ambil')->paginate(20);
        $layanans = Layanan::all();
        
        // Total antrian hari ini
        $totalHariIni = Antrian::whereDate('waktu_ambil', Carbon::today())->count();

        return view('admin.antrian.index', compact('antrians', 'layanans', 'totalHariIni'));
    }
}