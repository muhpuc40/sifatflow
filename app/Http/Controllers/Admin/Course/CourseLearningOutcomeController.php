<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLearningOutcome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseLearningOutcomeController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($course->learningOutcomes()->getQuery()->reorder());

        $course->learningOutcomes()->create($data);

        return $this->toCourse($course, 'extras', 'Learning outcome added.');
    }

    public function update(Request $request, CourseLearningOutcome $outcome): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $outcome->sort_order;

        $outcome->update($data);

        return $this->toCourse($outcome->course_list_id, 'extras', 'Learning outcome updated.');
    }

    public function destroy(CourseLearningOutcome $outcome): RedirectResponse
    {
        $outcome->delete();

        return $this->toCourse($outcome->course_list_id, 'extras', 'Learning outcome deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
