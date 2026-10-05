<?php

namespace App\Models;

use App\Enums\AmountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One installment of a course price. Must be paid before the student opens "paidBeforeModule". */
class CoursePaymentRule extends Model
{
    protected $table = 'course_payment_rule';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount_type' => AmountType::class,
            'amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_list_id');
    }

    /** The price row this rule belongs to. */
    public function price(): BelongsTo
    {
        return $this->belongsTo(CoursePrice::class, 'course_prices_id');
    }

    /** The "paid before" module. NULL means the installment is due at enrollment. */
    public function paidBeforeModule(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_modules_id');
    }

    /** Taka to pay for this installment. A percent is taken from the price AFTER discount. */
    public function amountFor(?float $price = null): float
    {
        $price ??= $this->price->finalPrice();
        $value = (float) $this->amount;

        return round(
            $this->amount_type === AmountType::Percent ? $price * $value / 100 : $value,
            2
        );
    }
}
