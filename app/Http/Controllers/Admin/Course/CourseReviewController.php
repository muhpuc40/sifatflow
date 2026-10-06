<?php

namespace App\Http\Controllers\Admin\Course;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Admin\Course\Concerns\ManagesCourse;
use App\Http\Controllers\Controller;
use App\Models\CourseReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Students write reviews through the API. Here the admin approves, rejects or deletes them. */
class CourseReviewController extends Controller
{
    use ManagesCourse;

    public function update(Request $request, CourseReview $review): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::enum(ReviewStatus::class)]]);

        $review->update($data);

        return $this->toCourse($review->course_list_id, 'reviews', 'Review '.$data['status'].'.');
    }

    public function destroy(CourseReview $review): RedirectResponse
    {
        $review->delete();

        return $this->toCourse($review->course_list_id, 'reviews', 'Review deleted.');
    }
}
