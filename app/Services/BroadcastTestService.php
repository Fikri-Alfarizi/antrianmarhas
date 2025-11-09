<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Antrian;
use App\Events\AntrianDipanggil;

/**
 * BroadcastTestService
 * 
 * Utility untuk testing broadcast event lokal
 * Gunakan di Tinker atau manual testing
 */
class BroadcastTestService
{
    /**
     * Test broadcast dengan random antrian
     * 
     * Usage:
     * >>> php artisan tinker
     * >>> BroadcastTestService::testBroadcast()
     */
    public static function testBroadcast()
    {
        $antrian = Antrian::with(['layanan'])->first();
        
        if (!$antrian) {
            return [
                'success' => false,
                'message' => 'No antrian found to broadcast'
            ];
        }

        try {
            // Broadcast event
            event(new AntrianDipanggil(
                $antrian,
                'TEST LOKET',
                $antrian->layanan?->nama_layanan ?? 'Test Service'
            ));

            Log::info('[TEST] Broadcast sent:', [
                'kode_antrian' => $antrian->kode_antrian,
                'loket' => 'TEST LOKET',
                'timestamp' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Broadcast test sent',
                'data' => [
                    'kode_antrian' => $antrian->kode_antrian,
                    'loket' => 'TEST LOKET',
                ]
            ];
        } catch (\Exception $e) {
            Log::error('[TEST] Broadcast error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Test dengan antrian spesifik
     */
    public static function testBroadcastWithCode($kodeAntrian)
    {
        $antrian = Antrian::where('kode_antrian', $kodeAntrian)->first();
        
        if (!$antrian) {
            return [
                'success' => false,
                'message' => "Antrian $kodeAntrian not found"
            ];
        }

        try {
            event(new AntrianDipanggil(
                $antrian,
                'LOKET TEST',
                'Service Test'
            ));

            Log::info('[TEST] Broadcast sent for: ' . $kodeAntrian);

            return [
                'success' => true,
                'message' => 'Broadcast sent',
                'kode' => $kodeAntrian
            ];
        } catch (\Exception $e) {
            Log::error('[TEST] Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Test multiple broadcasts
     */
    public static function testMultipleBroadcasts($count = 3)
    {
        $results = [];
        
        $antrians = Antrian::take($count)->get();

        foreach ($antrians as $antrian) {
            try {
                event(new AntrianDipanggil(
                    $antrian,
                    'LOKET TEST ' . rand(1, 5),
                    'Service Test'
                ));

                $results[] = [
                    'success' => true,
                    'kode' => $antrian->kode_antrian
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'kode' => $antrian->kode_antrian,
                    'error' => $e->getMessage()
                ];
            }
        }

        Log::info('[TEST] Multiple broadcasts sent', ['count' => count($results)]);

        return [
            'total' => count($antrians),
            'results' => $results
        ];
    }
}
