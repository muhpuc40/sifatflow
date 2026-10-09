<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batchwise_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_list_id')->constrained('batch_list')->cascadeOnDelete();
            $table->foreignId('course_module_items_id')->constrained('course_module_items')->restrictOnDelete();
            $table->string('title');
            $table->date('exam_date');
            $table->time('start_time');
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedSmallInteger('total_marks');
            $table->unsignedSmallInteger('pass_marks')->default(0);
            $table->dateTime('result_published_at')->nullable();
            $table->text('instruction')->nullable();
            $table->string('status', 20)->default('scheduled');    // scheduled | running | finished | cancelled
            $table->timestamps();
            $table->softDeletes();

            // Not unique in the database, because soft deleted rows would block a new one.
            // The admin form checks "one exam per item per batch".
            $table->index(['batch_list_id', 'course_module_items_id']);
            $table->index(['batch_list_id', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batchwise_exam');
    }
};
