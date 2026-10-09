<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_list_id')->constrained('course_list')->restrictOnDelete();
            $table->string('name');
            $table->string('code')->unique();                       // example: FSW-12
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->date('start_date');
            $table->date('approx_end_date')->nullable();
            $table->date('enroll_deadline')->nullable();
            $table->date('extended_enroll_deadline')->nullable();  // admin can extend the deadline
            $table->unsignedInteger('seat_limit')->nullable();     // null = unlimited
            $table->string('status', 20)->default('upcoming');     // upcoming | running | completed | cancelled
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_list_id', 'status']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_list');
    }
};
