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
        Schema::create('advanced_settings', function (Blueprint $table) {
            $table->id();
            // Queue timeout settings
            $table->integer('queue_timeout_minutes')->default(30); // Timeout antrian dalam menit
            $table->boolean('auto_cancel_timeout')->default(true); // Auto cancel jika timeout
            // Display settings
            $table->string('theme_color')->default('#026e4aff'); // Primary color
            $table->string('secondary_color')->default('#2c3e50'); // Secondary color
            $table->integer('display_refresh_seconds')->default(5); // Refresh rate display
            // Notification settings
            $table->boolean('email_notification_enabled')->default(false);
            $table->string('email_notification_recipient')->nullable();
            $table->boolean('sms_notification_enabled')->default(false);
            $table->string('sms_notification_number')->nullable();
            // Working hours
            $table->time('working_hours_start')->default('08:00');
            $table->time('working_hours_end')->default('17:00');
            $table->json('closed_days')->nullable(); // ['Monday', 'Sunday']
            // Advanced features
            $table->boolean('auto_assign_loket')->default(true);
            $table->integer('max_queue_per_loket')->default(100);
            $table->boolean('enable_customer_feedback')->default(true);
            $table->integer('maintenance_mode')->default(0); // 0=off, 1=on
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advanced_settings');
    }
};
