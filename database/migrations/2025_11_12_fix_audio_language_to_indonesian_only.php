<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set semua audio_settings bahasa ke 'id' (Indonesia) saja
        DB::table('audio_settings')->update(['bahasa' => 'id']);
        
        // Hapus duplicate records, keep only satu record
        $audioSettings = DB::table('audio_settings')->get();
        if ($audioSettings->count() > 1) {
            $firstId = $audioSettings->first()->id;
            DB::table('audio_settings')
                ->where('id', '!=', $firstId)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback, bahasa sudah di-lock ke ID
    }
};
