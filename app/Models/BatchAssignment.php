<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'batchwise_assignment';
    protected $guarded = ['id'];

    /** Same defaults as the database, so a new model is correct before it is reloaded. */
    protected $attributes = ['status' => 'draft', 'allow_late' => false];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'due_at' => 'datetime',
            'total_marks' => 'integer',
            'allow_late' => 'boolean',
            'status' => AssignmentStatus::class,
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

    /** Can a student still submit now? */
    public function acceptsSubmission(): bool
    {
        if ($this->status !== AssignmentStatus::Published) {
            return false;
        }

        return now()->lte($this->due_at) || $this->allow_late;
    }
}
