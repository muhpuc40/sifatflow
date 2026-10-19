<?php

namespace App\Models;

use App\Enums\AmountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rules for ALL affiliates. Always exactly one row (id = 1, seeded by its migration).
 * A commission COPIES the rate at the time of the sale (see AffiliateCommission), so changing
 * this never changes commissions already earned - only sales made after the change.
 */
class AffiliateSetting extends Model
{
    protected $table = 'affiliate_settings';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'auto_approve' => 'boolean',
            'commission_type' => AmountType::class,
            'commission_value' => 'decimal:2',
            'min_payout_amount' => 'decimal:2',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /** The one row. Created on first use if the seeding migration was ever rolled back. */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
