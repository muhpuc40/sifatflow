<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(CourseCurriculum::class, 'course_curriculum_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseModuleItem::class, 'course_modules_id')->orderBy('sort_order');
    }

    /** Example: $module->itemCounts() => ['class' => 8, 'exam' => 1, 'assignment' => 2] */
    public function itemCounts(): array
    {
        return $this->items()->selectRaw('item_type, count(*) as total')
            ->groupBy('item_type')->pluck('total', 'item_type')->all();
    }
}
