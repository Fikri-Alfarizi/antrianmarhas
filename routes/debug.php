<?php

// Debug route - untuk test ImgBB dan storage
Route::prefix('debug')->middleware('auth')->group(function () {
    
    // Test storage symlink
    Route::get('/storage', function () {
        $file = 'c:\laragon\www\antrianmarhas_v4\storage\app\public\logos\logo-1762968556.png';
        $url = asset('storage/logos/logo-1762968556.png');
        
        return response()->json([
            'file_exists' => file_exists($file),
            'url' => $url,
            'public_storage_exists' => is_dir('c:\laragon\www\antrianmarhas_v4\public\storage'),
            'test_url' => 'Akses di browser: ' . $url
        ]);
    });
    
    // Test ImgBB API
    Route::get('/imgbb-test', function () {
        try {
            $service = new \App\Services\ImgbbService();
            
            return response()->json([
                'status' => 'ImgbbService loaded successfully',
                'api_key_exists' => !empty(env('IMGBB_API_KEY'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // Test database
    Route::get('/db', function () {
        try {
            $pengaturan = \App\Models\Pengaturan::first();
            
            return response()->json([
                'pengaturan' => $pengaturan,
                'logo_url' => $pengaturan ? $pengaturan->logo : 'No pengaturan found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    });
    
    // View logs
    Route::get('/logs', function () {
        $logFile = 'c:\laragon\www\antrianmarhas_v4\storage\logs\laravel.log';
        
        if (!file_exists($logFile)) {
            return 'No logs found';
        }
        
        $logs = file_get_contents($logFile);
        $lines = array_slice(explode("\n", $logs), -50);
        
        return '<pre>' . htmlspecialchars(implode("\n", $lines)) . '</pre>';
    });
    
});
