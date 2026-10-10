<?php

namespace App\Models;

use App\Enums\ClassStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/** One class of a batch: which module item is taught, when, and by whom. */
class BatchClassSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'batchwise_class_schedule';
    protected $guarded = ['id'];

    /** Same defaults as the database, so a new model is correct before it is reloaded. */
    protected $attributes = ['status' => 'scheduled', 'duration_minutes' => 90];

    protected function casts(): array
    {
        return [
            'class_start_time' => 'datetime',
            'duration_minutes' => 'integer',
            'status' => ClassStatus::class,
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'batch_list_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CourseModuleItem::class, 'course_module_items_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id')->withTrashed();
    }

    /** The live link of this class (only for live classes). */
    public function liveInfo(): HasOne
    {
        return $this->hasOne(BatchLiveClassInfo::class, 'class_schedule_id');
    }

    /** When the class ends (start + duration). */
    public function endsAt(): \Illuminate\Support\Carbon
    {
        return $this->class_start_time->copy()->addMinutes($this->duration_minutes);
    }
}
