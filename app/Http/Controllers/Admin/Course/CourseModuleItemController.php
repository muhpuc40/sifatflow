<?php

namespace App\Http\Controllers\Admin\Course;

use App\Enums\ItemType;
use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use App\Models\CourseModuleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** A class / exam / assignment inside a module. Content and resource come from the libraries. */
class CourseModuleItemController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, CourseModule $module): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($module->items()->getQuery()->reorder());

        $module->items()->create($data);

        return $this->toCourse($module->curriculum->course_list_id, 'curriculum', 'Item added.');
    }

    public function update(Request $request, CourseModuleItem $item): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = $data['sort_order'] ?? $item->sort_order;

        $item->update($data);

        return $this->toCourse($item->module->curriculum->course_list_id, 'curriculum', 'Item updated.');
    }

    public function destroy(CourseModuleItem $item): RedirectResponse
    {
        $courseId = $item->module->curriculum->course_list_id;
        $item->delete();

        return $this->toCourse($courseId, 'curriculum', 'Item deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'item_type' => ['required', Rule::enum(ItemType::class)],
            'objective' => ['nullable', 'string', 'max:5000'],
            'course_content_id' => ['nullable', 'integer', 'exists:course_content,id'],
            'course_resource_id' => ['nullable', 'integer', 'exists:course_resources,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_preview'] = $request->boolean('is_preview');

        return $data;
    }
}
