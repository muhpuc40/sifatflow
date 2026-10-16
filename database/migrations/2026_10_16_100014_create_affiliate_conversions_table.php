<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // A course sold through an affiliate: one row per course. Made when the enrollment is created.
        Schema::create('affiliate_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->restrictOnDelete();
            $table->foreignId('affiliate_attribution_id')->constrained('affiliate_attributions')->restrictOnDelete();
            $table->foreignId('order_list_id')->constrained('order_list')->restrictOnDelete();
            $table->foreignId('order_item_id')->unique()->constrained('order_items')->restrictOnDelete();   // a course sale is credited once
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->decimal('sale_amount', 12, 2);                         // net_price of the item
            $table->string('status', 20)->default('confirmed');            // confirmed | reversed (refund / cancelled)
            $table->timestamp('converted_at')->useCurrent();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_conversions');
    }
};
