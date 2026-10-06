<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCurriculum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** A stage of a course (Beginning, Intermediate ...). It groups modules. */
class CourseCurriculumController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($course->curricula()->withTrashed());
        $data['is_active'] = $request->boolean('is_active');

        $course->curricula()->create($data);

        return $this->toCourse($course, 'curriculum', 'Stage added.');
    }

    public function update(Request $request, CourseCurriculum $curriculum): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $curriculum->sort_order;
        $data['is_active'] = $request->boolean('is_active');

        $curriculum->update($data);

        return $this->toCourse($curriculum->course_list_id, 'curriculum', 'Stage updated.');
    }

    public function destroy(CourseCurriculum $curriculum): RedirectResponse
    {
        // Soft delete. Modules stay in the database, but are hidden with their stage.
        $curriculum->delete();

        return $this->toCourse($curriculum->course_list_id, 'curriculum', 'Stage deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'milestone_value' => ['nullable', 'integer', 'min:0'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['milestone_value'] ??= 0;

        return $data;
    }
}
