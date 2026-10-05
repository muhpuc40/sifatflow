<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('course_payment_rule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->cascadeOnDelete();
            $table->foreignId('course_prices_id')->constrained('course_prices')->cascadeOnDelete();
            $table->foreignId('course_modules_id')->nullable()->constrained('course_modules')->restrictOnDelete();
            $table->string('rule_name');
            $table->enum('amount_type', ['percent', 'flat']);
            $table->decimal('amount', 12, 2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_prices_id', 'sort_order']);
            $table->index('course_list_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_payment_rule');
    }
};
