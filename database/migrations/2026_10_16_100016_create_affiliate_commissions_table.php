<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Commission earned from one conversion. The rate is COPIED from affiliate_settings at the time of the sale.
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->restrictOnDelete();
            $table->foreignId('affiliate_conversion_id')->unique()->constrained('affiliate_conversions')->restrictOnDelete();
            $table->foreignId('affiliate_payout_id')->nullable()->constrained('affiliate_payouts')->nullOnDelete();
            $table->string('rate_type', 10);                               // percent | flat
            $table->decimal('rate_value', 10, 2);
            $table->decimal('base_amount', 12, 2);                         // the amount the commission is calculated on (item net_price)
            $table->decimal('amount', 12, 2);                              // commission in taka
            $table->string('status', 20)->default('pending');              // pending | available | requested | paid | reversed
            $table->timestamp('available_at')->nullable();                 // student fully paid (+ hold_days)
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
            $table->index(['status', 'available_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
