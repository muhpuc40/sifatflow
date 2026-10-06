{{-- Shared by create and edit. Needs: $course, $categories --}}
<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-admin.input name="title" label="Title" :value="$course->title" required />
    </div>

    <x-admin.select name="course_categories_id" label="Category" :value="$course->course_categories_id" required>
        <option value="">Choose a category...</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </x-admin.select>

    <x-admin.input name="slug" label="Slug (link name)" :value="$course->slug"
        hint="Leave empty to make it from the title." />

    <div class="sm:col-span-2">
        <x-admin.textarea name="description" label="Description" :value="$course->description" rows="5" />
    </div>

    <div>
        <x-admin.input name="thumbnail" type="file" label="Thumbnail image" accept="image/*"
            hint="JPG, PNG or WebP, up to 2 MB." />
        @if ($course->thumbnail_url)
            <div class="mt-3 flex items-center gap-3">
                <img src="{{ $course->thumbnail_url }}" alt=""
                    class="h-16 w-28 rounded-lg object-cover ring-1 ring-slate-900/10">
                <x-admin.checkbox name="remove_thumbnail" label="Remove this image" />
            </div>
        @endif
    </div>

    <x-admin.input name="preview_video" type="url" label="Preview video link" :value="$course->preview_video"
        placeholder="https://..." hint="A free intro video shown on the public page." />

    <x-admin.select name="level" label="Level" :value="$course->level">
        <option value="">Not set</option>
        <option value="Online">Online</option>
        <option value="Offline">Offline</option>

    </x-admin.select>

    <x-admin.select name="status" label="Status" :value="$course->status?->value" required>
        @foreach (\App\Enums\CourseStatus::cases() as $status)
            <option value="{{ $status->value }}">{{ ucfirst($status->value) }}</option>
        @endforeach
    </x-admin.select>

    <div class="sm:col-span-2">
        <x-admin.checkbox name="is_active" label="Active (an inactive course is hidden from sales)"
            :checked="$course->is_active" />
    </div>
</div>