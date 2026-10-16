<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Rules for ALL affiliates. Always exactly one row (id = 1).
        // A commission copies the rate when the sale happens, so changing this never changes old commissions.
        Schema::create('affiliate_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);                   // program on / off
            $table->boolean('auto_approve')->default(false);               // approve new affiliates without admin
            $table->string('commission_type', 10)->default('percent');     // percent | flat
            $table->decimal('commission_value', 10, 2)->default(10);
            $table->unsignedSmallInteger('cookie_days')->default(30);      // how long a click is remembered
            $table->unsignedSmallInteger('hold_days')->default(0);         // wait after the student fully paid, before the commission is available
            $table->decimal('min_payout_amount', 10, 2)->default(500);
            $table->unsignedTinyInteger('payout_day')->default(10);        // day of the month admin pays (1 to 28)
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });

        // the one row
        DB::table('affiliate_settings')->insert([
            'id' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_settings');
    }
};
