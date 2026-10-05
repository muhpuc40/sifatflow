<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseCategory extends Model
{
    protected $guarded = ['id'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'course_categories_id');
    }
}
