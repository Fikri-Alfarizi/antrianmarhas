<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('advanced_settings', function (Blueprint $table) {
            $table->string('display_clock_mode')->default('now')->after('working_hours_end');
        });
    }

    public function down(): void
    {
        Schema::table('advanced_settings', function (Blueprint $table) {
            $table->dropColumn('display_clock_mode');
        });
    }
};
