<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');               // 1 to 5 (validated in the request)
            $table->text('comment')->nullable();
            $table->string('status', 20)->default('pending');    // pending | approved | rejected
            $table->timestamps();

            $table->unique(['course_list_id', 'student_id']);    // one review per student per course
            $table->index(['course_list_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }
};
