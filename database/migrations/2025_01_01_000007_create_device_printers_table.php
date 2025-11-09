<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_printers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_device');
            $table->string('mac_address')->unique();
            $table->enum('status', ['tersambung', 'putus'])->default('putus');
            $table->datetime('last_connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_printers');
    }
};
