<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audio_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tipe')->default('text-to-speech'); // text-to-speech atau audio-file
            $table->string('bahasa')->default('id'); // id, en, jv, dst
            $table->integer('volume')->default(100); // 0-100
            $table->boolean('aktif')->default(true);
            $table->string('format_pesan')->default('Nomor {nomor} silakan menuju ke {lokasi}'); // template
            $table->text('voice_url')->nullable(); // URL file audio jika pakai pre-recorded
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audio_settings');
    }
};
