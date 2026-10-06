<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseResource;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GET /courses/upload-resource
 * The resource library: files and links students can use (slides, PDFs, zips ...).
 * One resource can be used by many module items.
 */
class CourseResourceController extends Controller
{
    public function index(): View
    {
        return view('admin.library.resources', [
            'resources' => CourseResource::withCount('items')->latest('id')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        if ($request->hasFile('file')) {
            $data['url'] = Media::store($request->file('file'), 'course-resources');
        }

        CourseResource::create($data);

        return back()->with('success', 'Resource saved.');
    }

    public function update(Request $request, CourseResource $resource): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('file')) {
            Media::delete($resource->url);
            $data['url'] = Media::store($request->file('file'), 'course-resources');
        } elseif (! empty($data['url']) && $data['url'] !== $resource->url) {
            Media::delete($resource->url);
        } else {
            unset($data['url']);
        }

        $resource->update($data);

        return back()->with('success', 'Resource updated.');
    }

    public function destroy(CourseResource $resource): RedirectResponse
    {
        if ($resource->items()->exists()) {
            return back()->with('error', "This resource is used by {$resource->items()->count()} item(s). Remove it from them first.");
        }

        Media::delete($resource->url);
        $resource->delete();

        return back()->with('success', 'Resource deleted.');
    }

    private function validated(Request $request, bool $creating): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => [$creating ? 'required_without:file' : 'nullable', 'nullable', 'string', 'max:2048'],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:pdf,zip,rar,doc,docx,ppt,pptx,xls,xlsx,txt,png,jpg,jpeg,mp4,webm'],
        ], [
            'url.required_without' => 'Paste a link or choose a file.',
        ]) + ['url' => null];
    }
}
