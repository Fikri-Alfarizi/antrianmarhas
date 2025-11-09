<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistik_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('layanan_id')->constrained('layanans')->onDelete('cascade');
            $table->integer('total_menunggu')->default(0);
            $table->integer('total_dilayani')->default(0);
            $table->integer('total_selesai')->default(0);
            $table->integer('total_batal')->default(0);
            $table->timestamps();
            
            $table->unique(['tanggal', 'layanan_id']);
            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistik_harian');
    }
};