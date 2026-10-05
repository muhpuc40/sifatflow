<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_module_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_modules_id')->constrained('course_modules')->cascadeOnDelete();
            $table->string('item_type', 20);                 // recorded | live | exam | assignment
            $table->text('objective')->nullable();
            $table->foreignId('course_content_id')->nullable()->constrained('course_content')->restrictOnDelete();
            $table->foreignId('course_resource_id')->nullable()->constrained('course_resources')->restrictOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_preview')->default(false);   // free preview
            $table->timestamps();

            $table->index(['course_modules_id', 'sort_order']);
            $table->index(['course_modules_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_module_items');
    }
};
