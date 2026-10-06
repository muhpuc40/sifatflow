<?php

namespace App\Http\Controllers\Admin\Course;

use App\Enums\AmountType;
use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\CourseCurriculum;
use App\Models\CoursePaymentRule;
use App\Models\CoursePrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Installments of a price. Only the ACTIVE price can be changed; old prices are history. */
class CoursePaymentRuleController extends Controller
{
    use ManagesCourse;

    public function store(Request $request, CoursePrice $price): RedirectResponse
    {
        if (! $price->is_active) {
            return $this->locked($price->course_list_id);
        }

        $data = $this->validated($request, $price->course_list_id);
        $data['course_list_id'] = $price->course_list_id;
        $data['sort_order'] = $data['sort_order'] ?? $this->nextOrder($price->paymentRules()->getQuery()->reorder());

        $price->paymentRules()->create($data);

        return $this->toCourse($price->course_list_id, 'pricing', 'Installment added.');
    }

    public function update(Request $request, CoursePaymentRule $rule): RedirectResponse
    {
        if (! $rule->price->is_active) {
            return $this->locked($rule->course_list_id);
        }

        $data = $this->validated($request, $rule->course_list_id);
        $data['sort_order'] = $data['sort_order'] ?? $rule->sort_order;

        $rule->update($data);

        return $this->toCourse($rule->course_list_id, 'pricing', 'Installment updated.');
    }

    public function destroy(CoursePaymentRule $rule): RedirectResponse
    {
        if (! $rule->price->is_active) {
            return $this->locked($rule->course_list_id);
        }

        $rule->delete();

        return $this->toCourse($rule->course_list_id, 'pricing', 'Installment deleted.');
    }

    private function locked(int $courseId): RedirectResponse
    {
        return $this->toCourse($courseId, 'pricing', 'Installments of an old price cannot be changed.', 'error');
    }

    private function validated(Request $request, int $courseId): array
    {
        $data = $request->validate([
            'rule_name' => ['required', 'string', 'max:255'],
            // "Paid before" module: must belong to THIS course. Empty = due at enrollment.
            'course_modules_id' => ['nullable', 'integer', Rule::exists('course_modules', 'id')->where(
                fn ($q) => $q->whereIn('course_curriculum_id', CourseCurriculum::where('course_list_id', $courseId)->select('id'))
            )],
            'amount_type' => ['required', Rule::enum(AmountType::class)],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999', Rule::when($request->input('amount_type') === 'percent', 'max:100')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
