<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batchwise_assignment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_list_id')->constrained('batch_list')->cascadeOnDelete();
            $table->foreignId('course_module_items_id')->constrained('course_module_items')->restrictOnDelete();
            $table->string('title');
            $table->text('instruction')->nullable();
            $table->dateTime('assigned_at');
            $table->dateTime('due_at');
            $table->unsignedSmallInteger('total_marks');
            $table->boolean('allow_late')->default(false);
            $table->string('status', 20)->default('draft');        // draft | published | closed
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_list_id', 'course_module_items_id']);
            $table->index(['batch_list_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batchwise_assignment');
    }
};
