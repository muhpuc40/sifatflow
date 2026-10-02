<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One table for all user types: (user_type, user_id) points to
        // admins, students or instructors. No foreign key on purpose.
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 20);                               // admin, student, instructor
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('token_id')->nullable()->index();   // personal_access_tokens.id
            $table->string('device_id', 100);                              // created by the frontend
            $table->string('device_name')->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_type', 'user_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
