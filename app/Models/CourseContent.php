<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseContent extends Model
{
    protected $table = 'course_content';
    protected $guarded = ['id'];

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->thumbnail));
    }

    /** Link of the video or file (uploaded file or a link to another site). */
    protected function fileUrl(): Attribute
    {
        return Attribute::get(fn () => Media::url($this->url));
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseModuleItem::class, 'course_content_id');
    }
}
