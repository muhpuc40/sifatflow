<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\CourseContent;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * GET /courses/upload-content
 * The content library: what a class / exam / assignment shows (video, file ...).
 * One content can be used by many module items.
 */
class CourseContentController extends Controller
{
    public function index(): View
    {
        return view('admin.library.content', [
            'contents' => CourseContent::withCount('items')->latest('id')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = Media::store($request->file('thumbnail'), 'content-thumbnails');
        }

        if ($request->hasFile('file')) {
            $data['url'] = Media::store($request->file('file'), 'course-content');
        }

        CourseContent::create($data);

        return back()->with('success', 'Content saved.');
    }

    public function update(Request $request, CourseContent $content): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('thumbnail')) {
            Media::delete($content->thumbnail);
            $data['thumbnail'] = Media::store($request->file('thumbnail'), 'content-thumbnails');
        }

        if ($request->hasFile('file')) {
            Media::delete($content->url);
            $data['url'] = Media::store($request->file('file'), 'course-content');
        } elseif (! empty($data['url']) && $data['url'] !== $content->url) {
            Media::delete($content->url);       // replaced by a link
        } else {
            unset($data['url']);                // keep what is there
        }

        $content->update($data);

        return back()->with('success', 'Content updated.');
    }

    public function destroy(CourseContent $content): RedirectResponse
    {
        if ($content->items()->exists()) {
            return back()->with('error', "This content is used by {$content->items()->count()} item(s). Remove it from them first.");
        }

        Media::delete($content->thumbnail);
        Media::delete($content->url);
        $content->delete();

        return back()->with('success', 'Content deleted.');
    }

    private function validated(Request $request, bool $creating): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            // a link OR an uploaded file
            'url' => [$creating ? 'required_without:file' : 'nullable', 'nullable', 'string', 'max:2048'],
            'file' => ['nullable', 'file', 'max:51200', 'mimes:mp4,webm,mov,mkv,pdf'],
        ], [
            'url.required_without' => 'Paste a link or choose a file.',
        ]) + ['url' => null];
    }
}
