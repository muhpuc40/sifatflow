<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\CourseCurriculum;
use App\Models\CourseModule;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, CourseCurriculum $curriculum): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($curriculum->modules()->getQuery()->reorder());
        $data['is_active'] = $request->boolean('is_active');

        $curriculum->modules()->create($data);

        return $this->toCourse($curriculum->course_list_id, 'curriculum', 'Module added.');
    }

    public function update(Request $request, CourseModule $module): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $module->sort_order;
        $data['is_active'] = $request->boolean('is_active');

        $module->update($data);

        return $this->toCourse($module->curriculum->course_list_id, 'curriculum', 'Module updated.');
    }

    public function destroy(CourseModule $module): RedirectResponse
    {
        $courseId = $module->curriculum->course_list_id;

        try {
            $module->delete();   // its items are removed with it
        } catch (QueryException) {
            return $this->toCourse($courseId, 'curriculum',
                'This module is used by a payment rule. Change that rule first.', 'error');
        }

        return $this->toCourse($courseId, 'curriculum', 'Module deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'week' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'objective' => ['nullable', 'string', 'max:5000'],
            'milestone_value' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['milestone_value'] ??= 0;

        return $data;
    }
}
