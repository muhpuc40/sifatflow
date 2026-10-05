<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_curriculum_id')->constrained('course_curriculum')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedSmallInteger('week')->nullable();
            $table->text('objective')->nullable();
            $table->unsignedInteger('milestone_value')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_curriculum_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_modules');
    }
};
