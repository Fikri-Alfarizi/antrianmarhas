<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Antrian;
use App\Models\Pengaturan;
use App\Models\Loket;
use App\Services\LogActivityService;
use App\Services\WaitTimeService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KiosController extends Controller
{
    public function index()
    {
        $layanans = Layanan::where('status', 'aktif')->get();
        $pengaturan = Pengaturan::first();
        return view('kios.index', compact('layanans', 'pengaturan'));
    }

    public function cetak(Request $request)
    {
        try {
            $validated = $request->validate([
                'layanan_id' => 'required|exists:layanans,id',
            ]);

            $layanan = Layanan::findOrFail($validated['layanan_id']);

            // Cari nomor antrian terakhir hari ini
            $lastAntrian = Antrian::where('layanan_id', $layanan->id)
                ->whereDate('waktu_ambil', Carbon::today())
                ->orderBy('id', 'desc')
                ->first();

            // Generate nomor antrian baru
            if ($lastAntrian) {
                $lastNumber = (int) substr($lastAntrian->kode_antrian, strlen($layanan->prefix));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $kodeAntrian = $layanan->prefix . str_pad($newNumber, $layanan->digit, '0', STR_PAD_LEFT);

            // Generate QR code link to status page (skip jika imagick tidak tersedia)
            $qrCode = '';
            try {
                $statusUrl = route('status.index') . '?q=' . $kodeAntrian;
                $qrCode = base64_encode(QrCode::format('png')->size(300)->generate($statusUrl));
            } catch (\Exception $e) {
                // Imagick tidak tersedia, skip QR generation
                $qrCode = '';
            }

            // Simpan antrian
            $antrian = Antrian::create([
                'kode_antrian' => $kodeAntrian,
                'layanan_id' => $layanan->id,
                'status' => 'menunggu',
                'waktu_ambil' => now(),
            ]);

            LogActivityService::antrianCreated($antrian);

            $pengaturan = Pengaturan::first() ?? new Pengaturan();

            return response()->json([
                'success' => true,
                'antrian' => $antrian,
                'layanan' => $layanan,
                'pengaturan' => $pengaturan,
                'qr_code' => $qrCode ? 'data:image/png;base64,' . $qrCode : '',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . json_encode($e->errors()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Kios cetak error: ' . $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getWaitTimes()
    {
        try {
            $layanans = Layanan::where('status', 'aktif')->get();
            
            $waitTimes = $layanans->map(function($layanan) {
                // Ambil semua loket yang melayani layanan ini
                $lokets = Loket::where('layanan_id', $layanan->id)
                    ->where('status', 'aktif')
                    ->get();
                
                // Hitung rata-rata wait time dari semua loket
                $totalWait = 0;
                $loketCount = 0;
                
                foreach ($lokets as $loket) {
                    $wait = WaitTimeService::getEstimatedWaitTime($loket);
                    $totalWait += $wait;
                    $loketCount++;
                }
                
                $avgWait = $loketCount > 0 ? round($totalWait / $loketCount) : 0;
                
                return [
                    'layanan_id' => $layanan->id,
                    'nama_layanan' => $layanan->nama_layanan,
                    'estimated_minutes' => $avgWait,
                    'formatted' => WaitTimeService::formatWaitTime($avgWait),
                ];
            });
            
            return response()->json($waitTimes);
        } catch (\Exception $e) {
            \Log::error('Get wait times error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}