<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batchwise_class_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_list_id')->constrained('batch_list')->cascadeOnDelete();
            $table->foreignId('course_module_items_id')->constrained('course_module_items')->restrictOnDelete();
            $table->dateTime('class_start_time');
            $table->unsignedSmallInteger('duration_minutes')->default(90);
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->string('status', 20)->default('scheduled');   // scheduled | held | cancelled | rescheduled
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_list_id', 'class_start_time']);
            $table->index('course_module_items_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batchwise_class_schedule');
    }
};
