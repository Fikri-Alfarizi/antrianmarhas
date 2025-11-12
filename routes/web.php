<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\KiosController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\LoketController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\AntrianController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\PusatKontrolController;
use App\Http\Controllers\Admin\AdvancedSettingController;
use App\Http\Controllers\Petugas\LoketPetugasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Routes (Kios, Display, Status Check)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

// Display Ruang Tunggu (Real-time)
Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
Route::get('/display/data', [DisplayController::class, 'getData'])->name('display.data');

// Kios Cetak Antrian (Halaman untuk pengunjung)
Route::get('/kios', [KiosController::class, 'index'])->name('kios.index');
Route::get('/kios/wait-times', [KiosController::class, 'getWaitTimes'])->name('kios.wait-times');

// Status Antrian Pengunjung (QR Code Link)
Route::get('/status', [StatusController::class, 'index'])->name('status.index');
Route::get('/status/show', [StatusController::class, 'show'])->name('status.show');
// Route::get('/antrian/status', [StatusController::class, 'waitingStatus'])->name('antrian.status'); // Dihapus, menggunakan /status/show atau /status

// Rute yang TIDAK memerlukan CSRF Token (biasanya untuk API POST publik atau Kios)
Route::withoutMiddleware('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken')->group(function () {
    // KIOS - Membuat antrian baru
    Route::post('/kios/cetak', [KiosController::class, 'cetak'])->name('kios.cetak');
    Route::post('/kios/store', [KiosController::class, 'cetak'])->name('kios.store'); // Alias
    
    // Status Check API
    Route::post('/status/check', [StatusController::class, 'check'])->name('status.check');
});


/*
|--------------------------------------------------------------------------
| Admin Routes (Middleware 'auth' required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Core CRUD
    Route::resource('layanan', LayananController::class);
    Route::resource('loket', LoketController::class);
    Route::resource('pengguna', PenggunaController::class);
    
    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    Route::get('/advanced-settings', [AdvancedSettingController::class, 'index'])->name('advanced-settings.index');
    Route::post('/advanced-settings', [AdvancedSettingController::class, 'update'])->name('advanced-settings.update');

    // Laporan & Monitoring
    Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/chart-data', [AnalyticsController::class, 'getChartData'])->name('analytics.chart-data');
    Route::get('/analytics/detail-stats', [AnalyticsController::class, 'getDetailStats'])->name('analytics.detail-stats');
    Route::get('/analytics/export', [AnalyticsController::class, 'exportReport'])->name('analytics.export');
    
    // Print / History
    Route::get('/print', [PrintController::class, 'index'])->name('print.index');
    Route::get('/print/search', [PrintController::class, 'search'])->name('print.search');
    Route::post('/print/reprint', [PrintController::class, 'reprint'])->name('print.reprint');
    Route::get('/print/history', [PrintController::class, 'getHistory'])->name('print.history');

    Route::get('/pusat-kontrol', [PusatKontrolController::class, 'index'])->name('pusat-kontrol.index');
    Route::get('/pusat-kontrol/data', [PusatKontrolController::class, 'getData'])->name('pusat-kontrol.data');
    Route::post('/pusat-kontrol/{loket}/panggil', [PusatKontrolController::class, 'panggil'])->name('pusat-kontrol.panggil');
    Route::post('/pusat-kontrol/{loket}/selesai', [PusatKontrolController::class, 'selesai'])->name('pusat-kontrol.selesai');
    Route::post('/pusat-kontrol/{loket}/toggle-status', [PusatKontrolController::class, 'toggleStatus'])->name('pusat-kontrol.toggle-status');
    Route::get('/pusat-kontrol/tracking/history', [PusatKontrolController::class, 'getTrackingHistory'])->name('pusat-kontrol.tracking-history');
    Route::get('/pusat-kontrol/staff/list', [PusatKontrolController::class, 'getStaffList'])->name('pusat-kontrol.staff-list');
    Route::post('/pusat-kontrol/message/send', [PusatKontrolController::class, 'sendMessage'])->name('pusat-kontrol.message-send');
    Route::get('/pusat-kontrol/staff/activity', [PusatKontrolController::class, 'getStaffActivity'])->name('pusat-kontrol.staff-activity');
    Route::get('/pusat-kontrol/messages/unread', [PusatKontrolController::class, 'getUnreadMessages'])->name('pusat-kontrol.unread-messages');
    
    // Aksi Tambahan Loket (Jika masih dibutuhkan)
    Route::post('loket/{loket}/toggle', [LoketController::class, 'toggleStatus'])->name('loket.toggle');
});


/*
|--------------------------------------------------------------------------
| Petugas (Operator) Routes (Middleware 'auth' required)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/loket', [LoketPetugasController::class, 'index'])->name('loket.index');
    Route::get('/loket/list', [LoketPetugasController::class, 'getAntrianList'])->name('loket.list');
    Route::post('/loket/panggil', [LoketPetugasController::class, 'panggil'])->name('loket.panggil');
    Route::post('/loket/layani', [LoketPetugasController::class, 'layani'])->name('loket.layani');
    Route::post('/loket/selesai', [LoketPetugasController::class, 'selesai'])->name('loket.selesai');
    Route::post('/loket/batalkan', [LoketPetugasController::class, 'batalkan'])->name('loket.batalkan');
    Route::post('/loket/tutup', [LoketPetugasController::class, 'tutupLoket'])->name('loket.tutup');
    
    // Rute Diagnostik (Jika DiagnosticController dibuat)
    // Route::get('/diagnostics', [\App\Http\Controllers\Petugas\DiagnosticController::class, 'diagnostics'])->name('diagnostics'); 
});

// Testing Routes (Dibiarkan di luar group besar untuk kemudahan)
if (env('APP_DEBUG', false)) {
    Route::prefix('test')->name('test.')->group(function () {
        Route::get('/broadcast', function () {
            return view('test.broadcast');
        })->name('broadcast');
        
        Route::get('/audio', function () {
            return view('test-audio');
        })->name('audio');
        
        Route::post('/broadcast/send', function () {
            $service = new \App\Services\BroadcastTestService();
            return response()->json($service->testBroadcast());
        })->name('broadcast.send');

        // Debug: Show current pengaturan logo value (only in debug mode)
        Route::get('/setting-logo', function () {
            $s = \App\Models\Pengaturan::first();
            return response()->json([ 
                'logo' => $s?->logo, 
                'nama_instansi' => $s?->nama_instansi,
                'app_timezone' => config('app.timezone'),
                'current_time' => now()->format('Y-m-d H:i:s T'),
            ]);
        })->name('test.setting-logo');

        // Test Cloudinary credentials
        Route::get('/cloudinary-test', function () {
            return response()->json([
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET') ? '***' : 'NOT SET',
            ]);
        })->name('test.cloudinary');

        // Test Cloudinary connectivity
        Route::get('/cloudinary-ping', function () {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://api.cloudinary.com/v1_1/' . env('CLOUDINARY_CLOUD_NAME') . '/resource_types');
                return response()->json([
                    'status' => 'Connected',
                    'response' => $response->json(),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'Failed',
                    'error' => $e->getMessage(),
                ], 500);
            }
        })->name('test.cloudinary-ping');
    });
}