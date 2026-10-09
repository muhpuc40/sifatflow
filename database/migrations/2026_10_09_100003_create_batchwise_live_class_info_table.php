<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batchwise_live_class_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_list_id')->constrained('batch_list')->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->unique()->constrained('batchwise_class_schedule')->cascadeOnDelete(); // one per class
            $table->string('platform', 20)->default('meet');        // meet | zoom | other
            $table->string('meeting_url', 2048)->nullable();        // students never see it; they join through a tracked link
            $table->boolean('is_started')->default(false);          // true = the live class was held
            $table->foreignId('recording_content_id')->nullable()->constrained('course_content')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('batch_list_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batchwise_live_class_info');
    }
};
