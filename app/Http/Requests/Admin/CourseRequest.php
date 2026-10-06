<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Create and update of a course (the same rules; "slug" ignores the course being edited). */
class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'course_categories_id' => ['required', 'integer', 'exists:course_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash:ascii', 'max:255', Rule::unique('course_list', 'slug')->ignore($course?->id)],
            'description' => ['nullable', 'string', 'max:20000'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'remove_thumbnail' => ['nullable', 'boolean'],
            'preview_video' => ['nullable', 'url', 'max:2048'],
            'level' => ['nullable', Rule::in(['Online', 'Offline'])],
            'status' => ['required', Rule::enum(CourseStatus::class)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
