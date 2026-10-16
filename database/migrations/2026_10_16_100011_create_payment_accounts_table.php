<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Where a student can pay: our bKash / Nagad number, our bank account, or the cash instructions.
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('method', 20);                                  // mfs | cash | bank
            $table->string('provider', 60)->nullable();                    // bkash / nagad / rocket, or the bank name
            $table->string('account_no', 60)->nullable();
            $table->string('account_name')->nullable();
            $table->string('branch')->nullable();
            $table->text('instructions')->nullable();                      // "Send money, then write the TrxID" / "Pay at the office"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
