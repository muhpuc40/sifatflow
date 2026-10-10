<?php

namespace App\Models;

use App\Enums\LivePlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The live link of one class. Students do not see meeting_url;
 * they join through a tracked link (attendance is taken from that click).
 * is_started = true means the live class was held.
 */
class BatchLiveClassInfo extends Model
{
    use SoftDeletes;

    protected $table = 'batchwise_live_class_info';
    protected $guarded = ['id'];

    /** Same defaults as the database, so a new model is correct before it is reloaded. */
    protected $attributes = ['platform' => 'meet', 'is_started' => false];

    protected function casts(): array
    {
        return [
            'platform' => LivePlatform::class,
            'is_started' => 'boolean',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_list_id');
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(BatchClassSchedule::class, 'class_schedule_id');
    }

    /** The recording, a row of the content library. */
    public function recording(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class, 'recording_content_id');
    }
}
