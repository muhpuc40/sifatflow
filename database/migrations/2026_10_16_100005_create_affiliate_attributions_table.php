<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // "This student belongs to that affiliate". Made when a visitor with an affiliate cookie registers.
        Schema::create('affiliate_attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->restrictOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();   // filled when the visitor becomes a student
            $table->foreignId('affiliate_click_id')->nullable()->constrained('affiliate_clicks')->nullOnDelete();
            $table->string('visitor_token', 64)->nullable()->index();
            $table->string('status', 20)->default('open');                 // open | converted | expired
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_attributions');
    }
};
