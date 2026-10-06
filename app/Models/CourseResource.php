<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseResource extends Model
{
    protected $guarded = ['id'];

    protected function fileUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->url));
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseModuleItem::class, 'course_resource_id');
    }
}
