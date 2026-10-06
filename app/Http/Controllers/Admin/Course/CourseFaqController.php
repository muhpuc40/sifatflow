<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseFaq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseFaqController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($course->faqs()->getQuery()->reorder());

        $course->faqs()->create($data);

        return $this->toCourse($course, 'extras', 'Question added.');
    }

    public function update(Request $request, CourseFaq $faq): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $faq->sort_order;

        $faq->update($data);

        return $this->toCourse($faq->course_list_id, 'extras', 'Question updated.');
    }

    public function destroy(CourseFaq $faq): RedirectResponse
    {
        $faq->delete();

        return $this->toCourse($faq->course_list_id, 'extras', 'Question deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
