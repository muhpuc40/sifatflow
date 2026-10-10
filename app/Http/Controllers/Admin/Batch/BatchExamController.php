<?php

namespace App\Http\Controllers\Admin\Batch;

use App\Enums\ExamStatus;
use App\Enums\ItemType;
use App\Http\Controllers\Admin\Batch\Concerns\ManagesBatch;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchExam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BatchExamController extends Controller
{
    use ManagesBatch;

    public function store(Request $request, Batch $batch): RedirectResponse
    {
        $batch->exams()->create($this->validated($request, $batch));

        return $this->toBatch($batch, 'exams', 'Exam added.');
    }

    public function update(Request $request, BatchExam $exam): RedirectResponse
    {
        $exam->update($this->validated($request, $exam->batch, $exam));

        return $this->toBatch($exam->batch_list_id, 'exams', 'Exam updated.');
    }

    /** Show or hide the result to students. */
    public function result(BatchExam $exam): RedirectResponse
    {
        $published = $exam->result_published_at === null;

        $exam->update(['result_published_at' => $published ? now() : null]);

        return $this->toBatch($exam->batch_list_id, 'exams', $published ? 'Result published.' : 'Result hidden again.');
    }

    public function destroy(BatchExam $exam): RedirectResponse
    {
        $batchId = $exam->batch_list_id;
        $exam->delete();

        return $this->toBatch($batchId, 'exams', 'Exam deleted.');
    }

    private function validated(Request $request, Batch $batch, ?BatchExam $exam = null): array
    {
        $data = $request->validate([
            'course_module_items_id' => ['required', 'integer',
                Rule::exists('course_module_items', 'id')->where(
                    fn ($q) => $q->where('item_type', ItemType::Exam->value)->whereIn('course_modules_id', $this->moduleIds($batch))
                ),
                Rule::unique('batchwise_exam', 'course_module_items_id')
                    ->where('batch_list_id', $batch->id)->whereNull('deleted_at')->ignore($exam?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:65535'],
            'pass_marks' => ['nullable', 'integer', 'min:0', 'lte:total_marks'],
            'instruction' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::enum(ExamStatus::class)],
        ], [
            'course_module_items_id.exists' => 'Choose an exam item of this batch\'s course.',
            'course_module_items_id.unique' => 'This exam is already scheduled in this batch.',
            'pass_marks.lte' => 'Pass marks cannot be more than the total marks.',
        ]);

        $data['pass_marks'] ??= 0;

        return $data;
    }
}
