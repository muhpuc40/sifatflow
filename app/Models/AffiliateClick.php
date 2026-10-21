<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One row per visit through an affiliate link. Never edited. */
class AffiliateClick extends Model
{
    const UPDATED_AT = null;

    protected $table = 'affiliate_clicks';
    protected $guarded = ['id'];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_list_id');
    }
}
