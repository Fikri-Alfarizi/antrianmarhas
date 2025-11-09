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
        Schema::table('antrians', function (Blueprint $table) {
            $table->foreignId('transfer_from_antrian_id')->nullable()->constrained('antrians')->onDelete('set null');
            $table->boolean('is_transferred')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrians', function (Blueprint $table) {
            $table->dropForeignIdFor('antrians', 'transfer_from_antrian_id');
            $table->dropColumn(['transfer_from_antrian_id', 'is_transferred']);
        });
    }
};
