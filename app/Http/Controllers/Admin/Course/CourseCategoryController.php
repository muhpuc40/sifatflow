<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => CourseCategory::withCount('courses')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $data['slug'] = $this->slugFor($data['name'], $data['slug'] ?? null);

        CourseCategory::create($data);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, CourseCategory $category): RedirectResponse
    {
        $data = $request->validate($this->rules($category));
        $data['slug'] = ($data['slug'] ?? null) ?: $category->slug;

        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(CourseCategory $category): RedirectResponse
    {
        if ($category->courses()->withTrashed()->exists()) {
            return back()->with('error', 'This category has courses. Move or delete them first.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    private function rules(?CourseCategory $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'alpha_dash:ascii', 'max:255', Rule::unique('course_categories', 'slug')->ignore($category?->id)],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function slugFor(string $name, ?string $given): string
    {
        if ($given) {
            return $given;
        }

        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $i = 2;

        while (CourseCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
