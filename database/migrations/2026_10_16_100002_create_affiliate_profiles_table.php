<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // The affiliate program data of one affiliate account.
        Schema::create('affiliate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->unique()->constrained('affiliates')->cascadeOnDelete();
            $table->string('ref_code', 30)->unique();                      // link = /ref/{ref_code}. The affiliate can change it.
            $table->timestamp('ref_code_changed_at')->nullable();
            $table->string('status', 20)->default('pending')->index();     // pending | approved | rejected | suspended
            $table->string('payout_method', 20)->nullable();               // mfs | bank | cash
            $table->string('payout_provider', 60)->nullable();             // bkash / nagad / rocket, or the bank name
            $table->string('payout_account', 60)->nullable();              // MFS number or bank account number
            $table->string('payout_account_name')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_profiles');
    }
};
