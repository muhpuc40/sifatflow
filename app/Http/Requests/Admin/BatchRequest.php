<?php

namespace App\Http\Requests\Admin;

use App\Enums\BatchStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Create and update of a batch (same rules; "code" ignores the batch being edited). */
class BatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $batch = $this->route('batch');

        return [
            'course_list_id' => ['required', 'integer', Rule::exists('course_list', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'alpha_dash:ascii', 'max:50', Rule::unique('batch_list', 'code')->ignore($batch?->id)],
            'instructor_id' => ['nullable', 'integer', Rule::exists('instructors', 'id')->whereNull('deleted_at')],
            'start_date' => ['required', 'date'],
            'approx_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'enroll_deadline' => ['nullable', 'date'],
            'extended_enroll_deadline' => ['nullable', 'date', 'after_or_equal:enroll_deadline'],
            'seat_limit' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'status' => ['required', Rule::enum(BatchStatus::class)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'approx_end_date.after_or_equal' => 'The end date cannot be before the start date.',
            'extended_enroll_deadline.after_or_equal' => 'The extended deadline cannot be before the enroll deadline.',
        ];
    }
}
