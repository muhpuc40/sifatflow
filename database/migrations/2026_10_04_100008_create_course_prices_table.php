<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Price of the whole course. Keep old rows (is_active = false) as price history. */
    public function up(): void
    {
        Schema::create('course_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->cascadeOnDelete();
            $table->decimal('actual_price', 12, 2);
            $table->enum('discount_type', ['percent', 'flat'])->nullable();
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_list_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_prices');
    }
};
