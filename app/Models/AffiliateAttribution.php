<?php

namespace App\Models;

use App\Enums\AttributionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** "This student belongs to that affiliate." Made when a visitor with an affiliate cookie registers. */
class AffiliateAttribution extends Model
{
    protected $table = 'affiliate_attributions';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => AttributionStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(AffiliateClick::class, 'affiliate_click_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', AttributionStatus::Open)->where('expires_at', '>', now());
    }

    public function isOpen(): bool
    {
        return $this->status === AttributionStatus::Open && $this->expires_at?->isFuture();
    }
}
