<?php

namespace App\Models;

use App\Enums\AmountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

/**
 * A price row is history: never edit its price. To change a price call
 * Course::changePrice(), which adds a new row and keeps this one.
 */
class CoursePrice extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::updating(function (CoursePrice $price) {
            if ($price->isDirty(['actual_price', 'discount_type', 'discount_value'])) {
                throw new LogicException('A price cannot be edited. Use Course::changePrice() to create a new price.');
            }
        });
    }

    protected function casts(): array
    {
        return [
            'actual_price' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_type' => AmountType::class,
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_list_id');
    }

    /** Installments made for this price. */
    public function paymentRules(): HasMany
    {
        return $this->hasMany(CoursePaymentRule::class, 'course_prices_id')->orderBy('sort_order');
    }

    /** Price after discount (never below 0). Orders must copy this value at purchase time. */
    public function finalPrice(): float
    {
        $price = (float) $this->actual_price;
        $value = (float) $this->discount_value;

        $final = match ($this->discount_type) {
            AmountType::Percent => $price - ($price * $value / 100),
            AmountType::Flat => $price - $value,
            default => $price,
        };

        return round(max($final, 0), 2);
    }

    /** True when the active installments add up to the final price (use before publishing). */
    public function hasValidPaymentPlan(): bool
    {
        $final = $this->finalPrice();
        $rules = $this->paymentRules()->where('is_active', true)->get();

        if ($rules->isEmpty()) {
            return true; // no installments = full payment
        }

        return abs($rules->sum(fn (CoursePaymentRule $rule) => $rule->amountFor($final)) - $final) < 0.01;
    }
}
