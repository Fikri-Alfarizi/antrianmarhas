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
use App\Http\Controllers\Admin\AudioSettingController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\AdvancedSettingController;
use App\Http\Controllers\Petugas\LoketPetugasController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
Route::get('/display/data', [DisplayController::class, 'getData'])->name('display.data');
Route::get('/kios', [KiosController::class, 'index'])->name('kios.index');
Route::get('/kios/wait-times', [KiosController::class, 'getWaitTimes'])->name('kios.wait-times');
Route::post('/kios/store', [KiosController::class, 'cetak'])->name('kios.store');
Route::post('/kios/print', [KiosController::class, 'cetak'])->name('kios.print');
Route::get('/waiting', function () {
    $pengaturan = \App\Models\Pengaturan::first();
    return view('waiting.index', compact('pengaturan'));
})->name('waiting.index');
Route::get('/antrian/status', [StatusController::class, 'waitingStatus'])->name('antrian.status');
Route::withoutMiddleware('Illuminate\Foundation\Http\Middleware\VerifyCsrfToken')->group(function () {
    Route::post('/kios/cetak', [KiosController::class, 'cetak'])->name('kios.cetak');
    Route::post('/kios/store', [KiosController::class, 'cetak'])->name('kios.store');
    Route::post('/status/check', [StatusController::class, 'check'])->name('status.check');
    Route::get('/display/data', [DisplayController::class, 'getData'])->name('display.data');
    Route::get('/antrian/status', [StatusController::class, 'waitingStatus'])->name('antrian.status');
});
Route::get('/status', [StatusController::class, 'index'])->name('status.index');
Route::get('/status/show', [StatusController::class, 'show'])->name('status.show');

// Testing Routes (Development Only)
if (env('APP_DEBUG', false)) {
    Route::get('/test/broadcast', function () {
        return view('test.broadcast');
    })->name('test.broadcast');
    
    Route::get('/test/audio', function () {
        return view('test-audio');
    })->name('test.audio');
    
    Route::post('/test/broadcast/send', function () {
        $service = new \App\Services\BroadcastTestService();
        return response()->json($service->testBroadcast());
    })->name('test.broadcast.send');
}

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('layanan', LayananController::class);
    Route::resource('loket', LoketController::class);
    Route::post('loket/{loket}/toggle', [LoketController::class, 'toggleStatus'])->name('loket.toggle');
    Route::resource('pengguna', PenggunaController::class);
    
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    
    Route::get('/audio-settings', [AudioSettingController::class, 'index'])->name('audio-settings.index');
    Route::post('/audio-settings', [AudioSettingController::class, 'update'])->name('audio-settings.update');
    Route::get('/audio-settings/test', [AudioSettingController::class, 'testAudio'])->name('audio-settings.test');
    
    Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/chart-data', [AnalyticsController::class, 'getChartData'])->name('analytics.chart-data');
    Route::get('/analytics/detail-stats', [AnalyticsController::class, 'getDetailStats'])->name('analytics.detail-stats');
    Route::get('/analytics/export', [AnalyticsController::class, 'exportReport'])->name('analytics.export');
    
    Route::get('/print', [PrintController::class, 'index'])->name('print.index');
    Route::get('/print/search', [PrintController::class, 'search'])->name('print.search');
    Route::post('/print/reprint', [PrintController::class, 'reprint'])->name('print.reprint');
    Route::get('/print/history', [PrintController::class, 'getHistory'])->name('print.history');
    
    Route::get('/advanced-settings', [AdvancedSettingController::class, 'index'])->name('advanced-settings.index');
    Route::post('/advanced-settings', [AdvancedSettingController::class, 'update'])->name('advanced-settings.update');
});

// Petugas Routes
Route::middleware(['auth'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/loket', [LoketPetugasController::class, 'index'])->name('loket.index');
    Route::get('/loket/list', [LoketPetugasController::class, 'getAntrianList'])->name('loket.list');
    Route::post('/loket/panggil', [LoketPetugasController::class, 'panggil'])->name('loket.panggil');
    Route::post('/loket/layani', [LoketPetugasController::class, 'layani'])->name('loket.layani');
    Route::post('/loket/selesai', [LoketPetugasController::class, 'selesai'])->name('loket.selesai');
    Route::post('/loket/batalkan', [LoketPetugasController::class, 'batalkan'])->name('loket.batalkan');
    Route::post('/loket/tutup', [LoketPetugasController::class, 'tutupLoket'])->name('loket.tutup');
    Route::get('/diagnostics', [\App\Http\Controllers\Petugas\DiagnosticController::class, 'diagnostics'])->name('diagnostics');
});