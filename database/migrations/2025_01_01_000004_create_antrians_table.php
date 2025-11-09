<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->string('kode_antrian');
            $table->foreignId('layanan_id')->constrained('layanans')->onDelete('cascade');
            $table->foreignId('loket_id')->nullable()->constrained('lokets')->onDelete('set null');
            $table->enum('status', ['menunggu', 'dipanggil', 'dilayani', 'selesai', 'batal'])->default('menunggu');
            $table->datetime('waktu_ambil');
            $table->datetime('waktu_panggil')->nullable();
            $table->datetime('waktu_selesai')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
            
            $table->index('kode_antrian');
            $table->index('status');
            $table->index('waktu_ambil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};