<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Events\AntrianDipanggil; // Penting untuk broadcast
use App\Events\LoketStatusUpdated; // Penting untuk broadcast
use App\Services\LogActivityService; // Penting untuk log
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PusatKontrolController extends Controller
{
    /**
     * Menampilkan halaman utama Pusat Kontrol.
     */
    public function index()
    {
        // Tampilan awal hanya memuat layout
        // Data akan dimuat via AJAX
        return view('admin.pusat-kontrol.index');
    }

    /**
     * API Internal (AJAX) untuk mengambil data SEMUA loket.
     * Ini yang akan di-refresh oleh JavaScript.
     */
    public function getData(Request $request)
    {
        $today = Carbon::today();
        
        // 1. Ambil semua loket dengan eager load layanan dan users
        $lokets = Loket::with('layanan', 'users')->orderBy('nama_loket', 'asc')->get();

        // 2. Ambil semua antrian yang sedang aktif HARI INI
        $activeAntrians = Antrian::whereIn('status', ['dipanggil', 'dilayani'])
                                ->whereDate('waktu_ambil', $today)
                                ->get()
                                ->keyBy('loket_id'); // Jadikan loket_id sebagai key

        // 3. Ambil jumlah antrian menunggu untuk SETIAP LAYANAN
        $waitingCounts = Layanan::withCount(['antrians' => function ($query) use ($today) {
            $query->where('status', 'menunggu')->whereDate('waktu_ambil', $today);
        }])->get()->pluck('antrians_count', 'id'); // key by layanan_id
        
        // 4. Gabungkan data
        $data = $lokets->map(function ($loket) use ($activeAntrians, $waitingCounts) {
            $currentAntrian = $activeAntrians->get($loket->id);
            $waitingCount = $waitingCounts->get($loket->layanan_id) ?? 0;
            
            // Hitung stats untuk hari ini
            $todayAntrians = Antrian::where('loket_id', $loket->id)
                                    ->whereDate('waktu_ambil', Carbon::today())
                                    ->get();
            $totalAntrian = $todayAntrians->count();
            $selesai = $todayAntrians->where('status', 'selesai')->count();
            $batal = $todayAntrians->where('status', 'batal')->count();

            return [
                'id' => $loket->id,
                'nama_loket' => $loket->nama_loket,
                'status' => $loket->status, // 'aktif' atau 'tutup'
                'layanan' => $loket->layanan->nama_layanan ?? 'N/A',
                'layanan_id' => $loket->layanan_id,
                'operator' => $loket->users->first()->name ?? '(Kosong)', // Ambil nama operator
                'antrian' => $currentAntrian ? [
                    'kode_antrian' => $currentAntrian->kode_antrian,
                    'status' => $currentAntrian->status, // 'dipanggil' atau 'dilayani'
                ] : null,
                'waiting_count' => $waitingCount,
                'stats' => [
                    'total_antrian' => $totalAntrian,
                    'selesai' => $selesai,
                    'batal' => $batal,
                ],
            ];
        });

        return response()->json(['success' => true, 'lokets' => $data]);
    }

    /**
     * Aksi: Panggil Antrian (oleh Admin)
     */
    public function panggil(Request $request, Loket $loket)
    {
        $today = Carbon::today();

        // 1. Cek apakah loket sedang melayani
        $sedangDilayani = Antrian::where('loket_id', $loket->id)
            ->whereIn('status', ['dipanggil', 'dilayani'])
            ->whereDate('waktu_ambil', $today)
            ->exists();
            
        if ($sedangDilayani) {
            return response()->json(['success' => false, 'message' => 'Loket sedang melayani. Selesaikan dulu.']);
        }

        // 2. Ambil antrian menunggu berikutnya untuk layanan loket ini
        $antrian = Antrian::where('layanan_id', $loket->layanan_id)
            ->where('status', 'menunggu')
            ->whereDate('waktu_ambil', $today)
            ->orderBy('waktu_ambil', 'asc')
            ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian menunggu.']);
        }

        // 3. Update antrian
        $antrian->update([
            'status' => 'dipanggil',
            'waktu_panggil' => now(),
            'loket_id' => $loket->id,
        ]);
        
        LogActivityService::log("ADMIN memanggil antrian {$antrian->kode_antrian} ke {$loket->nama_loket}");

        // 4. Broadcast event ke Display (dan ke Petugas jika dia sedang buka)
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true, 'message' => 'Antrian ' . $antrian->kode_antrian . ' dipanggil.']);
    }

    /**
     * Aksi: Selesaikan Antrian (oleh Admin)
     */
    public function selesai(Request $request, Loket $loket)
    {
        $antrian = Antrian::where('loket_id', $loket->id)
                         ->whereIn('status', ['dipanggil', 'dilayani'])
                         ->whereDate('waktu_ambil', Carbon::today())
                         ->first();

        if (!$antrian) {
            return response()->json(['success' => false, 'message' => 'Tidak ada antrian yang sedang dilayani di loket ini.']);
        }

        $antrian->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        LogActivityService::log("ADMIN menyelesaikan antrian {$antrian->kode_antrian} di {$loket->nama_loket}");

        // Broadcast untuk membersihkan display
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Aksi: Toggle Status Loket (Buka/Tutup)
     */
    public function toggleStatus(Request $request, Loket $loket)
    {
        $newStatus = $loket->status === 'aktif' ? 'tutup' : 'aktif';
        
        $loket->update(['status' => $newStatus]);
        
        LogActivityService::log("ADMIN mengubah status {$loket->nama_loket} menjadi {$newStatus}");

        // Broadcast event
        broadcast(new LoketStatusUpdated($loket))->toOthers();

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    /**
     * Aksi: Kirim Pesan ke Petugas (Pop-up di tengah layar)
     */
    public function messageSend(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string|min:1|max:500',
            'message_type' => 'nullable|in:info,warning,error,success',
        ]);

        // Get the petugas user
        $petugas = \App\Models\User::find($validated['to_user_id']);
        
        // Get the loket assigned to this petugas
        $loket = $petugas->loket;
        
        if (!$loket) {
            return response()->json(['success' => false, 'message' => 'Petugas tidak memiliki loket yang ditugaskan.'], 400);
        }

        // Log activity
        LogActivityService::log("ADMIN mengirim pesan ke {$petugas->name} di {$loket->nama_loket}: {$validated['message']}");

        // Broadcast pesan ke petugas di loket tersebut
        broadcast(new \App\Events\AdminMessageSent(
            $loket,
            $validated['message'],
            Auth::user()->name
        ))->toOthers();

        return response()->json(['success' => true, 'message' => 'Pesan terkirim.']);
    }

    /**
     * API: Dapatkan tracking history antrian
     */
    public function trackingHistory(Request $request)
    {
        $today = Carbon::today();
        
        // Ambil antrian yang telah selesai atau dibatalkan
        $antrians = Antrian::whereDate('waktu_ambil', $today)
            ->whereIn('status', ['selesai', 'batal', 'dipanggil', 'dilayani'])
            ->with('loket', 'layanan')
            ->orderBy('waktu_panggil', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($antrian) {
                return [
                    'kode_antrian' => $antrian->kode_antrian,
                    'layanan' => $antrian->layanan->nama_layanan ?? 'N/A',
                    'loket' => $antrian->loket?->nama_loket ?? '-',
                    'status' => $antrian->status,
                    'waktu_panggil' => $antrian->waktu_panggil?->format('H:i:s') ?? '-',
                    'waktu_selesai' => $antrian->waktu_selesai?->format('H:i:s') ?? '-',
                ];
            });

        return response()->json(['success' => true, 'data' => $antrians]);
    }

    /**
     * API: Dapatkan daftar staff/petugas
     */
    public function staffList(Request $request)
    {
        $staff = \App\Models\User::where('role', 'petugas')
            ->with('lokets')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($user) {
                // Tentukan status berdasarkan apakah ada antrian yang sedang dilayani
                $activeAntrian = Antrian::whereHas('loket.users', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                    ->whereIn('status', ['dipanggil', 'dilayani'])
                    ->whereDate('waktu_ambil', Carbon::today())
                    ->exists();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'loket' => $user->lokets->first()?->nama_loket ?? '-',
                    'status' => $activeAntrian ? 'aktif' : 'idle',
                    'avatar' => strtoupper(substr($user->name, 0, 1)),
                ];
            });

        return response()->json(['success' => true, 'data' => $staff]);
    }

    /**
     * API: Dapatkan aktivitas staff real-time
     */
    public function staffActivity(Request $request)
    {
        $today = Carbon::today();
        
        $activities = Antrian::whereDate('waktu_ambil', $today)
            ->whereNotNull('loket_id')
            ->with('loket.users', 'layanan')
            ->orderBy('waktu_panggil', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($antrian) {
                $operator = $antrian->loket?->users->first();
                $duration = null;
                
                if ($antrian->waktu_panggil && $antrian->waktu_selesai) {
                    $duration = $antrian->waktu_selesai->diffInSeconds($antrian->waktu_panggil);
                }

                return [
                    'operator_name' => $operator?->name ?? 'Unknown',
                    'loket_nama' => $antrian->loket?->nama_loket ?? '-',
                    'antrian' => $antrian->kode_antrian,
                    'layanan' => $antrian->layanan?->nama_layanan ?? '-',
                    'status' => $antrian->status,
                    'waktu_mulai' => $antrian->waktu_panggil?->format('H:i:s') ?? '-',
                    'waktu_selesai' => $antrian->waktu_selesai?->format('H:i:s') ?? '-',
                    'durasi_detik' => $duration,
                ];
            });

        return response()->json(['success' => true, 'data' => $activities]);
    }

    /**
     * API: Dapatkan daftar antrian yang menunggu untuk loket tertentu
     */
    public function waitingQueue(Request $request, Loket $loket)
    {
        $today = Carbon::today();
        
        // Ambil antrian menunggu untuk layanan loket ini
        $waitingAntrians = Antrian::where('layanan_id', $loket->layanan_id)
            ->where('status', 'menunggu')
            ->whereDate('waktu_ambil', $today)
            ->with('layanan')
            ->orderBy('waktu_ambil', 'asc')
            ->limit(10) // Tampilkan 10 antrian pertama yang menunggu
            ->get()
            ->map(function ($antrian) {
                return [
                    'kode_antrian' => $antrian->kode_antrian,
                    'layanan' => $antrian->layanan->nama_layanan ?? 'N/A',
                    'waktu_ambil' => $antrian->waktu_ambil?->format('H:i:s') ?? '-',
                ];
            });

        return response()->json(['success' => true, 'data' => $waitingAntrians]);
    }
}