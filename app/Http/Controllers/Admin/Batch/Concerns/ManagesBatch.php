<?php

namespace App\Http\Controllers\Admin\Batch\Concerns;

use App\Models\Batch;
use App\Models\CourseCurriculum;
use App\Models\CourseModule;
use App\Models\CourseModuleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

trait ManagesBatch
{
    /** Back to the batch page, on the right tab, with a message. */
    protected function toBatch(Batch|int $batch, string $tab, string $message, string $type = 'success'): RedirectResponse
    {
        $id = $batch instanceof Batch ? $batch->id : $batch;

        return redirect(route('admin.batches.show', $id).'#'.$tab)->with($type, $message);
    }

    /** Ids of all modules of the batch's course (used to check that an item belongs to this course). */
    protected function moduleIds(Batch $batch)
    {
        return CourseModule::query()
            ->whereIn('course_curriculum_id', CourseCurriculum::where('course_list_id', $batch->course_list_id)->select('id'))
            ->select('id');
    }

    /**
     * Module items of the batch's course for the dropdowns, grouped by type:
     * ['live' => [[id, label], ...], 'exam' => [...], ...]
     */
    protected function itemOptions(Batch $batch): array
    {
        $items = CourseModuleItem::query()
            ->whereIn('course_modules_id', $this->moduleIds($batch))
            ->with(['module.curriculum', 'content:id,name'])
            ->get()
            ->sortBy(fn ($i) => sprintf('%06d-%06d-%06d-%06d', $i->module->curriculum->sort_order, $i->module->sort_order, $i->sort_order, $i->id));

        $options = [];
        foreach ($items as $item) {
            $options[$item->item_type->value][] = [
                'id' => $item->id,
                'label' => $this->itemLabel($item),
            ];
        }

        return $options;
    }

    protected function itemLabel(CourseModuleItem $item): string
    {
        $name = $item->content?->name
            ?? ($item->objective ? Str::limit(trim($item->objective), 40) : null)
            ?? ucfirst($item->item_type->value).' #'.$item->id;

        return $item->module->title.' · '.$name;
    }
}
