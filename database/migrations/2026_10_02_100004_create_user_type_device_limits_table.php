<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_type_device_limits', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 20)->unique();             // admin, student, instructor
            $table->unsignedTinyInteger('max_devices')->default(2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_type_device_limits');
    }
};
