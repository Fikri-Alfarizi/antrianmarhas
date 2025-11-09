<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\Pengaturan;
use App\Models\AudioSetting;
use App\Services\AudioService;
use App\Services\WaitTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index()
    {
        $lokets = Loket::with('layanan')->where('status', 'aktif')->get();
        $pengaturan = Pengaturan::first();
        $audioSetting = AudioSetting::first() ?? new AudioSetting();
        return view('display.index', compact('lokets', 'pengaturan', 'audioSetting'));
    }

    public function getData()
    {
        try {
            $lokets = Loket::with(['layanan', 'antrians' => function($query) {
                $query->whereDate('waktu_ambil', Carbon::today())
                      ->whereIn('status', ['dipanggil', 'dilayani', 'menunggu'])
                      ->latest('waktu_panggil');
            }])->get();

            $data = $lokets->map(function($loket) {
                $antrianDipanggil = $loket->antrians->where('status', 'dipanggil')->first();
                $antrianDilayani = $loket->antrians->where('status', 'dilayani')->first();
                
                // Tentukan antrian yang ditampilkan (dipanggil prioritas)
                $displayAntrian = $antrianDipanggil ?? $antrianDilayani;
                
                return [
                    'id' => $loket->id,
                    'nama_loket' => $loket->nama_loket,
                    'layanan' => $loket->layanan ? $loket->layanan->nama_layanan : 'N/A',
                    'status' => $loket->status ?? 'aktif',
                    'antrian' => $displayAntrian ? [
                        'id' => $displayAntrian->id,
                        'kode_antrian' => $displayAntrian->kode_antrian,
                        'status' => $displayAntrian->status,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'lokets' => $data,
            ]);
        } catch (\Exception $e) {
            \Log::error('Display getData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'lokets' => [],
            ], 500);
        }
    }
}
