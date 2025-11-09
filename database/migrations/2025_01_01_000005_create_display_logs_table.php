<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('display_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->constrained('antrians')->onDelete('cascade');
            $table->foreignId('loket_id')->constrained('lokets')->onDelete('cascade');
            $table->string('pesan_display');
            $table->enum('status_warna', ['biru', 'hijau', 'abu', 'merah'])->default('biru');
            $table->datetime('waktu_tampil');
            $table->timestamps();
            
            $table->index('waktu_tampil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('display_logs');
    }
};