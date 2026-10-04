<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_id', 64)->unique();
            $table->string('user_type', 20);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('password_fingerprint', 64)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('channel', 20)->nullable();
            $table->timestamp('challenge_expires_at');
            $table->string('code_hash', 64)->nullable();
            $table->timestamp('code_sent_at')->nullable();
            $table->timestamp('code_expires_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('status', 20)->default('pending');
            $table->string('reset_token_hash', 64)->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['user_type', 'user_id']);
            $table->index('challenge_expires_at');
        });
    }
    public function down(): void { Schema::dropIfExists('password_resets'); }
};
