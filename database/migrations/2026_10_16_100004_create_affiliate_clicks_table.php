<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // One row per visit through an affiliate link. Never edited.
        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->cascadeOnDelete();
            $table->foreignId('course_list_id')->nullable()->constrained('course_list')->nullOnDelete();   // page that was opened
            $table->string('visitor_token', 64)->index();                  // random id kept in the visitor's cookie
            $table->string('ip_hash', 64)->nullable();                     // hash only, not the real IP
            $table->string('user_agent', 255)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('landing_url', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['affiliate_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
