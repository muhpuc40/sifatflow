<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_list', function (Blueprint $table) {
            $table->id();
            // A category with courses cannot be deleted.
            $table->foreignId('course_categories_id')->constrained('course_categories')->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->string('level', 20)->nullable();            // beginner | intermediate | advanced
            $table->string('status', 20)->default('draft');     // draft | published | archived
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_list');
    }
};
