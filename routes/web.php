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
Route::get('/display/audio-settings', [DisplayController::class, 'getAudioSettings'])->name('display.audio-settings');

// Kios Cetak Antrian (Halaman untuk pengunjung)
Route::get('/kios', [KiosController::class, 'index'])->name('kios.index');
Route::get('/kios/wait-times', [KiosController::class, 'getWaitTimes'])->name('kios.wait-times');

// Status Antrian Pengunjung (QR Code Link)
Route::get('/status', [StatusController::class, 'index'])->name('status.index');
Route::get('/status/show', [StatusController::class, 'show'])->name('status.show');

// Custom route untuk riwayat antrian display
require_once __DIR__.'/display_riwayat.php';

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
    
    // Audio Settings
    Route::get('/audio-setting', [\App\Http\Controllers\Admin\AudioSettingController::class, 'index'])->name('audio_setting.index');
    Route::post('/audio-setting', [\App\Http\Controllers\Admin\AudioSettingController::class, 'update'])->name('audio_setting.update');
    Route::get('/audio-setting/get', [\App\Http\Controllers\Admin\AudioSettingController::class, 'getSettings'])->name('audio_setting.get');
    Route::post('/audio-setting/test', [\App\Http\Controllers\Admin\AudioSettingController::class, 'testAudio'])->name('audio_setting.test');

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
    Route::post('/pusat-kontrol/message-send', [PusatKontrolController::class, 'messageSend'])->name('pusat-kontrol.message-send');
    Route::get('/pusat-kontrol/tracking-history', [PusatKontrolController::class, 'trackingHistory'])->name('pusat-kontrol.tracking-history');
    Route::get('/pusat-kontrol/staff-list', [PusatKontrolController::class, 'staffList'])->name('pusat-kontrol.staff-list');
    Route::get('/pusat-kontrol/staff-activity', [PusatKontrolController::class, 'staffActivity'])->name('pusat-kontrol.staff-activity');
    Route::get('/pusat-kontrol/{loket}/waiting-queue', [PusatKontrolController::class, 'waitingQueue'])->name('pusat-kontrol.waiting-queue');
    
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
        
        // Debug logo upload
        Route::get('/logo-debug', function () {
            $pengaturan = \App\Models\Pengaturan::first();
            $logFile = storage_path('logs/laravel.log');
            $logs = file_exists($logFile) ? array_slice(explode("\n", file_get_contents($logFile)), -20) : [];
            
            return response()->json([
                'pengaturan' => $pengaturan,
                'logo_url' => $pengaturan ? $pengaturan->logo : null,
                'storage_path' => asset('storage/logos/'),
                'recent_logs' => $logs,
                'symlink_exists' => is_link('public/storage'),
                'public_storage_exists' => is_dir('public/storage')
            ]);
        })->name('logo-debug');
    });
}