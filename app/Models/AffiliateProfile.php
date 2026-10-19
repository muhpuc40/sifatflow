<?php

namespace App\Models;

use App\Enums\AffiliateStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The affiliate PROGRAM data of one affiliate account: created automatically (status = pending)
 * the moment the affiliate registers; approve/reject happens here afterwards, in the admin panel.
 */
class AffiliateProfile extends Model
{
    protected $table = 'affiliate_profiles';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => AffiliateStatus::class,
            'ref_code_changed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', AffiliateStatus::Approved);
    }

    public function isApproved(): bool
    {
        return $this->status === AffiliateStatus::Approved;
    }
}
