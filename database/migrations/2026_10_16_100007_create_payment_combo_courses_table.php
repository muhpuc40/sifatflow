<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The courses inside a combo.
        Schema::create('payment_combo_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_combo_id')->constrained('payment_combos')->cascadeOnDelete();
            $table->foreignId('course_list_id')->constrained('course_list')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['payment_combo_id', 'course_list_id']);
            $table->index('course_list_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_combo_courses');
    }
};
