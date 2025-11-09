<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('print_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->constrained('antrians')->onDelete('cascade');
            $table->string('kode_antrian');
            $table->integer('print_count')->default(1);
            $table->timestamp('last_printed_at')->nullable();
            $table->string('printed_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_histories');
    }
};
