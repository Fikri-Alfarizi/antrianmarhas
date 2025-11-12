<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loket;
use App\Models\Antrian;
use App\Models\Layanan;
use App\Models\AntrianTracking;
use App\Models\AdminMessage;
use App\Models\StaffActivityLog;
use App\Events\AntrianDipanggil; // Penting untuk broadcast
use App\Events\LoketStatusUpdated; // Penting untuk broadcast
use App\Events\LoketToggleStatus;
use App\Events\AntrianTrackingUpdated;
use App\Events\AdminMessageSent;
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

        // Log tracking
        AntrianTracking::create([
            'antrian_id' => $antrian->id,
            'loket_id' => $loket->id,
            'user_id' => Auth::id(),
            'action' => 'called',
            'admin_name' => Auth::user()->name,
            'timestamp' => now(),
        ]);
        
        LogActivityService::log("ADMIN memanggil antrian {$antrian->kode_antrian} ke {$loket->nama_loket}");

        // 4. Broadcast event ke Display (dan ke Petugas jika dia sedang buka)
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();
        broadcast(new AntrianTrackingUpdated($antrian, 'called', Auth::user()))->toOthers();

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

        // Log tracking
        AntrianTracking::create([
            'antrian_id' => $antrian->id,
            'loket_id' => $loket->id,
            'user_id' => Auth::id(),
            'action' => 'finished',
            'admin_name' => Auth::user()->name,
            'timestamp' => now(),
        ]);

        LogActivityService::log("ADMIN menyelesaikan antrian {$antrian->kode_antrian} di {$loket->nama_loket}");

        // Broadcast untuk membersihkan display
        broadcast(new AntrianDipanggil($antrian->load('loket')))->toOthers();
        broadcast(new AntrianTrackingUpdated($antrian, 'finished', Auth::user()))->toOthers();

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

        // Broadcast ke semua admin untuk update real-time
        broadcast(new LoketToggleStatus($loket, $newStatus))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Status loket berhasil diubah menjadi ' . $newStatus,
            'status' => $newStatus,
        ]);
    }

    /**
     * API: Dapatkan tracking history panggilan
     */
    public function getTrackingHistory(Request $request)
    {
        $limit = $request->query('limit', 50);
        
        $tracking = AntrianTracking::with(['antrian', 'loket', 'user'])
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'antrian_kode' => $item->antrian->kode_antrian,
                    'loket_nama' => $item->loket->nama_loket,
                    'action' => $item->action,
                    'admin_name' => $item->admin_name,
                    'timestamp' => $item->timestamp->format('Y-m-d H:i:s'),
                    'waktu_relatif' => $item->timestamp->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }

    /**
     * API: Kirim pesan dari admin ke petugas
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'message_type' => 'nullable|in:info,warning,error,success',
        ]);

        $message = AdminMessage::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'info',
        ]);

        $toUser = \App\Models\User::findOrFail($request->to_user_id);

        // Broadcast pesan ke petugas
        broadcast(new AdminMessageSent(
            $request->message,
            Auth::user(),
            $toUser,
            $message->id
        ))->toOthers();

        LogActivityService::log("ADMIN mengirim pesan ke {$toUser->name}");

        return response()->json([
            'success' => true,
            'message_id' => $message->id,
            'message' => 'Pesan berhasil dikirim',
        ]);
    }

    /**
     * API: Dapatkan daftar staff untuk dropdown
     */
    public function getStaffList(Request $request)
    {
        $staff = \App\Models\User::where('role', '!=', 'admin')
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $staff,
        ]);
    }

    /**
     * API: Dapatkan daftar staff activity untuk tracking
     */
    public function getStaffActivity(Request $request)
    {
        $activity = StaffActivityLog::with('user')
            ->where('status', '!=', 'offline')
            ->orderBy('last_activity_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name,
                    'activity' => $item->activity,
                    'status' => $item->status,
                    'last_activity' => $item->last_activity_at->diffForHumans(),
                    'last_activity_timestamp' => $item->last_activity_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $activity,
        ]);
    }

    /**
     * API: Dapatkan unread messages untuk admin
     */
    public function getUnreadMessages(Request $request)
    {
        // Catatan: Untuk response dari petugas ke admin
        // Tapi bisa juga untuk tracking messages yang dikirim
        $messages = AdminMessage::where('from_user_id', '!=', Auth::id())
            ->where('read', false)
            ->with(['fromUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }
}