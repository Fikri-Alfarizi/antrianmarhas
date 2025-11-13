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
        Schema::create('antrian_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->constrained('antrians')->onDelete('cascade');
            $table->foreignId('loket_id')->nullable()->constrained('lokets')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // 'called', 'served', 'finished', 'cancelled'
            $table->string('admin_name')->nullable(); // Nama admin yang melakukan aksi
            $table->timestamp('timestamp');
            $table->timestamps();
            
            $table->index(['antrian_id', 'timestamp']);
            $table->index(['loket_id', 'timestamp']);
        });

        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->string('message_type')->default('info'); // 'info', 'warning', 'error', 'success'
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['to_user_id', 'read']);
            $table->index(['from_user_id', 'created_at']);
        });

        Schema::create('staff_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('activity'); // 'login', 'logout', 'opened_loket', 'closed_loket', 'called_antrian', 'served_antrian', etc
            $table->string('status')->default('active'); // 'active', 'idle', 'offline'
            $table->timestamp('last_activity_at');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian_trackings');
        Schema::dropIfExists('admin_messages');
        Schema::dropIfExists('staff_activity_logs');
    }
};
