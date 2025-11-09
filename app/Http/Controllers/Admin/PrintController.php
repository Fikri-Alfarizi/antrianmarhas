<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\PrintHistory;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PrintController extends Controller
{
    public function index()
    {
        $printHistories = PrintHistory::with('antrian')
            ->orderBy('last_printed_at', 'desc')
            ->paginate(20);
        
        return view('admin.print.index', compact('printHistories'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json(['error' => 'Minimal 2 karakter'], 400);
        }

        $antrians = Antrian::where('kode_antrian', 'like', "%$query%")
            ->orWhere('id', $query)
            ->limit(10)
            ->get()
            ->map(function($antrian) {
                return [
                    'id' => $antrian->id,
                    'kode_antrian' => $antrian->kode_antrian,
                    'layanan' => $antrian->layanan->nama_layanan ?? 'N/A',
                    'status' => $antrian->status,
                    'waktu_ambil' => $antrian->waktu_ambil->format('d-m-Y H:i'),
                ];
            });

        return response()->json($antrians);
    }

    public function reprint(Request $request)
    {
        $validated = $request->validate([
            'antrian_id' => 'required|exists:antrians,id',
        ]);

        $antrian = Antrian::findOrFail($validated['antrian_id']);
        $pengaturan = Pengaturan::first();

        // Generate QR code link
        $qrCode = '';
        try {
            $statusUrl = route('status.index') . '?q=' . $antrian->kode_antrian;
            $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($statusUrl));
        } catch (\Exception $e) {
            // Jika gagal, kosongkan saja
        }

        // Update atau create print history
        $printHistory = PrintHistory::where('antrian_id', $antrian->id)->first();
        
        if ($printHistory) {
            $printHistory->update([
                'print_count' => $printHistory->print_count + 1,
                'last_printed_at' => now(),
                'printed_by' => auth()->user()->name ?? 'System',
            ]);
        } else {
            PrintHistory::create([
                'antrian_id' => $antrian->id,
                'kode_antrian' => $antrian->kode_antrian,
                'print_count' => 1,
                'last_printed_at' => now(),
                'printed_by' => auth()->user()->name ?? 'System',
            ]);
        }

        return response()->json([
            'success' => true,
            'antrian' => $antrian,
            'layanan' => $antrian->layanan->nama_layanan,
            'pengaturan' => $pengaturan,
            'qr_code' => $qrCode ? 'data:image/png;base64,' . $qrCode : '',
            'print_count' => $printHistory ? $printHistory->print_count + 1 : 1,
        ]);
    }

    public function getHistory(Request $request)
    {
        $antrianId = $request->input('antrian_id');

        $history = PrintHistory::where('antrian_id', $antrianId)
            ->with('antrian')
            ->first();

        if (!$history) {
            return response()->json(['error' => 'Riwayat cetak tidak ditemukan'], 404);
        }

        return response()->json([
            'print_count' => $history->print_count,
            'last_printed_at' => $history->last_printed_at->format('d-m-Y H:i:s'),
            'first_printed_at' => $history->created_at->format('d-m-Y H:i:s'),
            'printed_by' => $history->printed_by,
        ]);
    }
}
