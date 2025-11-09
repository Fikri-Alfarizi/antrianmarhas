<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom waktu_mulai_dilayani ke antrians jika belum ada
        Schema::table('antrians', function (Blueprint $table) {
            if (!Schema::hasColumn('antrians', 'waktu_mulai_dilayani')) {
                $table->datetime('waktu_mulai_dilayani')->nullable()->after('waktu_panggil');
            }
        });
    }

    public function down(): void
    {
        Schema::table('antrians', function (Blueprint $table) {
            if (Schema::hasColumn('antrians', 'waktu_mulai_dilayani')) {
                $table->dropColumn('waktu_mulai_dilayani');
            }
        });
    }
};
