<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_faq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->cascadeOnDelete();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_list_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_faq');
    }
};
