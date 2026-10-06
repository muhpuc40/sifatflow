<?php

namespace App\Http\Controllers\Admin\Course\Concerns;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;

trait ManagesCourse
{
    /** Back to the course page, on the right tab, with a message. */
    protected function toCourse(Course|int $course, string $tab, string $message, string $type = 'success'): RedirectResponse
    {
        $id = $course instanceof Course ? $course->id : $course;

        return redirect(route('admin.courses.show', $id).'#'.$tab)->with($type, $message);
    }

    /** Next free sort_order in a list (last + 1). */
    protected function nextOrder($query, string $column = 'sort_order'): int
    {
        // reorder(): a relation's own ORDER BY is not allowed together with max() on MySQL
        return (int) $query->reorder()->max($column) + 1;
    }
}
