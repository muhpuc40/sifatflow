<?php

namespace App\Models;

use App\Enums\ItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseModuleItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'item_type' => ItemType::class,
            'is_active' => 'boolean',
            'is_preview' => 'boolean',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_modules_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(CourseResource::class, 'course_resource_id');
    }
}
