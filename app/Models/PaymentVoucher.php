<?php

namespace App\Models;

use App\Enums\AmountType;
use App\Enums\VoucherStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A one-time discount code made by admin for ONE student. The student is emailed the code.
 * Never edited after creation (financial row): cancel it and make a new one instead.
 */
class PaymentVoucher extends Model
{
    protected $table = 'payment_vouchers';
    protected $guarded = ['id'];

    protected $attributes = ['status' => 'active'];

    protected function casts(): array
    {
        return [
            'amount_type' => AmountType::class,
            'amount' => 'decimal:2',
            'status' => VoucherStatus::class,
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'emailed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', VoucherStatus::Active)->where('expires_at', '>=', now());
    }

    public function isExpired(): bool
    {
        return $this->status === VoucherStatus::Active && $this->expires_at && $this->expires_at->isPast();
    }

    /** active | expired | used | cancelled - for the badge. Expired is not a stored status. */
    public function displayStatus(): string
    {
        return $this->isExpired() ? 'expired' : $this->status->value;
    }

    public function canBeUsed(): bool
    {
        return $this->status === VoucherStatus::Active && ! $this->isExpired();
    }

    /** Discount in taka for an order of this total. Never more than the total. */
    public function amountFor(float $total): float
    {
        $value = (float) $this->amount;
        $discount = $this->amount_type === AmountType::Percent ? $total * $value / 100 : $value;

        return round(min(max($discount, 0), $total), 2);
    }
}
