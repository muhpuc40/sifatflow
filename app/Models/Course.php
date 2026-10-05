<?php

namespace App\Models;

use App\Enums\AmountType;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use SoftDeletes;

    protected $table = 'course_list';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'is_active' => 'boolean',
        ];
    }

    /** Courses visible on the public website. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published)->where('is_active', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_categories_id');
    }

    /** Stages of this course (Beginning, Intermediate ...), in order. */
    public function curricula(): HasMany
    {
        return $this->hasMany(CourseCurriculum::class, 'course_list_id')->orderBy('sort_order');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(CoursePrice::class, 'course_list_id');
    }

    /** The price used for new sales. */
    public function currentPrice(): HasOne
    {
        return $this->hasOne(CoursePrice::class, 'course_list_id')->where('is_active', true)->latestOfMany();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'course_list_id');
    }

    public function learningOutcomes(): HasMany
    {
        return $this->hasMany(CourseLearningOutcome::class, 'course_list_id')->orderBy('sort_order');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(CourseFaq::class, 'course_list_id')->orderBy('sort_order');
    }

    /** Installments of ALL prices, old and new. For the live plan use currentPrice->paymentRules. */
    public function paymentRules(): HasMany
    {
        return $this->hasMany(CoursePaymentRule::class, 'course_list_id')->orderBy('sort_order');
    }

    /**
     * Change the price: the old row is kept (for students who already enrolled)
     * and a new active row is created. Installment rules are copied to the new
     * price unless $copyRules is false.
     */
    public function changePrice(
        float $actualPrice,
        ?AmountType $discountType = null,
        float $discountValue = 0,
        bool $copyRules = true,
    ): CoursePrice {
        return DB::transaction(function () use ($actualPrice, $discountType, $discountValue, $copyRules) {
            $old = $this->prices()->where('is_active', true)->latest('id')->first();

            $this->prices()->where('is_active', true)->update(['is_active' => false]);

            $new = $this->prices()->create([
                'actual_price' => $actualPrice,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'is_active' => true,
            ]);

            if ($copyRules && $old) {
                foreach ($old->paymentRules()->where('is_active', true)->get() as $rule) {
                    $new->paymentRules()->create([
                        'course_list_id' => $this->id,
                        'course_modules_id' => $rule->course_modules_id,
                        'rule_name' => $rule->rule_name,
                        'amount_type' => $rule->amount_type,
                        'amount' => $rule->amount,
                        'sort_order' => $rule->sort_order,
                        'is_active' => true,
                    ]);
                }
            }

            return $new;
        });
    }
}
