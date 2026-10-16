<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // What the student owes for each course, as dues. Full payment = one due. Installment = one due per rule.
        // A due of 0 taka (free course) is marked paid when the order is created.
        Schema::create('order_item_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('course_payment_rule_id')->nullable()->constrained('course_payment_rule')->nullOnDelete();   // null for full payment
            $table->unsignedTinyInteger('sequence')->default(1);
            $table->string('title');                                       // Admission / Second installment / Full payment
            $table->decimal('amount', 12, 2);                              // all dues of one course add up to its net_price
            $table->foreignId('due_before_module_id')->nullable()->constrained('course_modules')->nullOnDelete();   // must be paid before this module opens. null = at enrollment
            $table->string('status', 20)->default('unpaid');               // unpaid | pending | paid | cancelled
            $table->foreignId('payment_id')->nullable()->constrained('payment_list')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['order_item_id', 'sequence']);
            $table->index('payment_id');
            $table->index(['status', 'due_before_module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_installments');
    }
};
