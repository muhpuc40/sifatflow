<?php

namespace App\Http\Controllers\Admin\Course;

use App\Enums\CourseStatus;
use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use App\Models\CourseResource;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    /** GET /courses  - course cards, 3 in a row */
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->with(['category:id,name', 'currentPrice'])
            ->withCount(['curricula', 'modules'])
            ->withCount(['reviews as approved_reviews_count' => fn($q) => $q->approved()])
            ->withAvg(['reviews as avg_rating' => fn($q) => $q->approved()], 'rating')
            ->when($request->filled('q'), fn($q) => $q->where('title', 'like', '%' . $request->input('q') . '%'))
            ->when($request->filled('category'), fn($q) => $q->where('course_categories_id', $request->input('category')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => CourseCategory::orderBy('name')->get(['id', 'name']),
            'statuses' => CourseStatus::cases(),
        ]);
    }

    /** GET /courses/create */
    public function create(): View
    {
        return view('admin.courses.create', [
            'course' => new Course(['status' => CourseStatus::Draft, 'is_active' => true]),
            'categories' => CourseCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** POST /courses */
    public function store(CourseRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'remove_thumbnail']);
        $data['slug'] = ($data['slug'] ?? null) ?: $this->uniqueSlug($data['title']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = Media::store($request->file('thumbnail'), 'course-thumbnails');
        }

        $course = Course::create($data);

        return redirect()->route('admin.courses.show', $course)
            ->with('success', 'Course created. Now add its stages, price and the rest.');
    }

    /** GET /courses/{course} - everything about one course */
    public function show(Course $course): View
    {
        $course->load([
            'category',
            'curricula.modules.items.content',
            'curricula.modules.items.resource',
            'learningOutcomes',
            'faqs',
            'reviews.student',
        ]);
        /** @var \App\Models\CoursePrice|null $currentPrice */
        $currentPrice = $course->prices()->where('is_active', true)->latest('id')->first();
        $rules = $currentPrice ? $currentPrice->paymentRules()->with('paidBeforeModule')->get() : collect();

        return view('admin.courses.show', [
            'course' => $course,
            'currentPrice' => $currentPrice,
            'priceHistory' => $course->prices()->withCount('paymentRules')->latest('id')->get(),
            'rules' => $rules,
            'modules' => $course->curricula->flatMap->modules,
            'itemTypes' => ItemType::cases(),
            'contents' => CourseContent::orderBy('name')->get(['id', 'name']),
            'resources' => CourseResource::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** GET /courses/{course}/edit */
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => CourseCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** PUT /courses/{course} */
    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'remove_thumbnail']);
        $data['slug'] = ($data['slug'] ?? null) ?: $course->slug;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('thumbnail')) {
            Media::delete($course->thumbnail);
            $data['thumbnail'] = Media::store($request->file('thumbnail'), 'course-thumbnails');
        } elseif ($request->boolean('remove_thumbnail')) {
            Media::delete($course->thumbnail);
            $data['thumbnail'] = null;
        }

        $course->update($data);

        return redirect()->route('admin.courses.show', $course)->with('success', 'Course updated.');
    }

    /** DELETE /courses/{course} - soft delete (students who bought it keep their data) */
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', "Course \"{$course->title}\" deleted.");
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'course';
        $slug = $base;
        $i = 2;

        while (Course::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
