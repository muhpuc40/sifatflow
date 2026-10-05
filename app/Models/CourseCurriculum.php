<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** One stage of a course (Beginning, Intermediate ...). It groups modules. */
class CourseCurriculum extends Model
{
    use SoftDeletes;

    protected $table = 'course_curriculum';
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_list_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class, 'course_curriculum_id')->orderBy('sort_order');
    }
}
