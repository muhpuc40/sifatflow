<?php

namespace App\Http\Controllers\Admin\Batch;

use App\Enums\ClassStatus;
use App\Enums\ItemType;
use App\Enums\LivePlatform;
use App\Http\Controllers\Admin\Batch\Concerns\ManagesBatch;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchClassSchedule;
use App\Models\BatchLiveClassInfo;
use App\Models\CourseModuleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Classes of a batch. A LIVE class also has a row in batchwise_live_class_info
 * (platform, link, was it held, recording). A recorded class has none.
 */
class BatchClassController extends Controller
{
    use ManagesBatch;

    public function store(Request $request, Batch $batch): RedirectResponse
    {
        $data = $this->validated($request, $batch);

        $schedule = $batch->classes()->create($this->classData($data, $batch));
        $this->syncLiveInfo($schedule, $data);

        return $this->toBatch($batch, 'classes', 'Class added.');
    }

    public function update(Request $request, BatchClassSchedule $schedule): RedirectResponse
    {
        $batch = $schedule->batch;
        $data = $this->validated($request, $batch);

        $schedule->update($this->classData($data, $batch));
        $this->syncLiveInfo($schedule, $data);

        return $this->toBatch($batch, 'classes', 'Class updated.');
    }

    /** One click: mark the class as held (or undo it). */
    public function held(BatchClassSchedule $schedule): RedirectResponse
    {
        $held = $schedule->status !== ClassStatus::Held;

        $schedule->update(['status' => $held ? ClassStatus::Held : ClassStatus::Scheduled]);
        $schedule->liveInfo()->update(['is_started' => $held]);

        return $this->toBatch($schedule->batch_list_id, 'classes', $held ? 'Class marked as held.' : 'Class set back to scheduled.');
    }

    public function destroy(BatchClassSchedule $schedule): RedirectResponse
    {
        $batchId = $schedule->batch_list_id;

        $schedule->liveInfo()->delete();   // soft delete (the database cascade does not run for soft deletes)
        $schedule->delete();

        return $this->toBatch($batchId, 'classes', 'Class deleted.');
    }

    private function validated(Request $request, Batch $batch): array
    {
        return $request->validate([
            'course_module_items_id' => ['required', 'integer', Rule::exists('course_module_items', 'id')->where(
                fn ($q) => $q->whereIn('item_type', [ItemType::Live->value, ItemType::Recorded->value])
                    ->whereIn('course_modules_id', $this->moduleIds($batch))
            )],
            'class_start_time' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'instructor_id' => ['nullable', 'integer', Rule::exists('instructors', 'id')->whereNull('deleted_at')],
            'status' => ['required', Rule::enum(ClassStatus::class)],
            'platform' => ['nullable', Rule::enum(LivePlatform::class)],
            'meeting_url' => ['nullable', 'url', 'max:2048'],
            'recording_content_id' => ['nullable', 'integer', 'exists:course_content,id'],
        ], [
            'course_module_items_id.exists' => 'Choose a live or recorded class of this batch\'s course.',
        ]);
    }

    private function classData(array $data, Batch $batch): array
    {
        return [
            'course_module_items_id' => $data['course_module_items_id'],
            'class_start_time' => $data['class_start_time'],
            'duration_minutes' => $data['duration_minutes'],
            'instructor_id' => $data['instructor_id'] ?? $batch->instructor_id,
            'status' => $data['status'],
        ];
    }

    private function syncLiveInfo(BatchClassSchedule $schedule, array $data): void
    {
        $isLive = CourseModuleItem::find($schedule->course_module_items_id)?->item_type === ItemType::Live;

        if (! $isLive) {
            $schedule->liveInfo()->delete();   // a recorded class has no live link

            return;
        }

        $info = BatchLiveClassInfo::withTrashed()->firstOrNew(['class_schedule_id' => $schedule->id]);

        if ($info->trashed()) {
            $info->restore();
        }

        $info->fill([
            'batch_list_id' => $schedule->batch_list_id,
            'platform' => $data['platform'] ?? LivePlatform::Meet->value,
            'meeting_url' => $data['meeting_url'] ?? null,
            'recording_content_id' => $data['recording_content_id'] ?? null,
            'is_started' => $data['status'] === ClassStatus::Held->value,
        ])->save();
    }
}
