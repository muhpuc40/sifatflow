<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Money sent to an affiliate. One payout pays many commissions.
        Schema::create('affiliate_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained('affiliates')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 20);                                  // mfs | bank | cash  (copied from the profile)
            $table->string('provider', 60)->nullable();
            $table->string('account', 60)->nullable();                     // copy of the account used
            $table->string('status', 20)->default('requested');            // requested | approved | paid | rejected
            $table->timestamp('requested_at')->useCurrent();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->string('trx_id', 60)->nullable();                      // reference of the money sent
            $table->string('reject_reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'status']);
            $table->index(['status', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payouts');
    }
};
