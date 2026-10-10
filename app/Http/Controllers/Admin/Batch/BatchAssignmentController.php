<?php

namespace App\Http\Controllers\Admin\Batch;

use App\Enums\AssignmentStatus;
use App\Enums\ItemType;
use App\Http\Controllers\Admin\Batch\Concerns\ManagesBatch;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BatchAssignmentController extends Controller
{
    use ManagesBatch;

    public function store(Request $request, Batch $batch): RedirectResponse
    {
        $batch->assignments()->create($this->validated($request, $batch));

        return $this->toBatch($batch, 'assignments', 'Assignment added.');
    }

    public function update(Request $request, BatchAssignment $assignment): RedirectResponse
    {
        $assignment->update($this->validated($request, $assignment->batch, $assignment));

        return $this->toBatch($assignment->batch_list_id, 'assignments', 'Assignment updated.');
    }

    public function destroy(BatchAssignment $assignment): RedirectResponse
    {
        $batchId = $assignment->batch_list_id;
        $assignment->delete();

        return $this->toBatch($batchId, 'assignments', 'Assignment deleted.');
    }

    private function validated(Request $request, Batch $batch, ?BatchAssignment $assignment = null): array
    {
        $data = $request->validate([
            'course_module_items_id' => ['required', 'integer',
                Rule::exists('course_module_items', 'id')->where(
                    fn ($q) => $q->where('item_type', ItemType::Assignment->value)->whereIn('course_modules_id', $this->moduleIds($batch))
                ),
                Rule::unique('batchwise_assignment', 'course_module_items_id')
                    ->where('batch_list_id', $batch->id)->whereNull('deleted_at')->ignore($assignment?->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'instruction' => ['nullable', 'string', 'max:10000'],
            'assigned_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after:assigned_at'],
            'total_marks' => ['required', 'integer', 'min:1', 'max:65535'],
            'status' => ['required', Rule::enum(AssignmentStatus::class)],
        ], [
            'course_module_items_id.exists' => 'Choose an assignment item of this batch\'s course.',
            'course_module_items_id.unique' => 'This assignment is already given in this batch.',
            'due_at.after' => 'The due date must be after the assigned date.',
        ]);

        $data['allow_late'] = $request->boolean('allow_late');

        return $data;
    }
}
