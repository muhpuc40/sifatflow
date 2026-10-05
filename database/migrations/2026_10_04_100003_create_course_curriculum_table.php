<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * One row = one stage of a course, e.g. "Beginning", "Intermediate".
     * A stage groups modules: Beginning = HTML, CSS, JS.
     */
    public function up(): void
    {
        Schema::create('course_curriculum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('milestone_value')->default(0);
            $table->unsignedSmallInteger('duration')->nullable();   // in days
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_list_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_curriculum');
    }
};
