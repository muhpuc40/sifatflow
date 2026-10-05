<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseContent extends Model
{
    protected $table = 'course_content';
    protected $guarded = ['id'];

    public function items(): HasMany
    {
        return $this->hasMany(CourseModuleItem::class, 'course_content_id');
    }
}
