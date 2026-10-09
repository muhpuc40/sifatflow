<x-admin.layout title="Course categories">
    <div x-data="{ open: false, mode: 'add', action: '', form: {} }" @keydown.escape.window="open = false">
        <x-admin.page-header title="Course categories"
            description="Group courses, for example Web Development or Design.">
            <x-admin.button :href="route('admin.courses.index')" variant="secondary"><x-admin.icon name="arrow-left"
                    class="size-4" /> Courses</x-admin.button>
            <x-admin.button type="button"
                @click="mode = 'add'; action = '{{ route('admin.categories.store') }}'; form = { name: '', slug: '', icon: '', description: '' }; open = true">
                <x-admin.icon name="plus" class="size-4" /> Add category
            </x-admin.button>
        </x-admin.page-header>

        <!-- <x-admin.flash /> -->

        <x-admin.card>
            @if ($categories->isEmpty())
                <x-admin.empty-state icon="tag" title="No categories yet" message="Add one before creating a course." />
            @else
                <x-admin.table>
                    <x-slot:head>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Courses</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th class="text-right">Actions</th>
                    </x-slot:head>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                @if ($category->description)
                                <p class="max-w-md truncate text-xs text-slate-500">{{ $category->description }}</p> @endif
                            </td>
                            <td class="text-slate-500">{{ $category->slug }}</td>
                            <td>{{ $category->courses_count }}</td>
                            <td>{{ $category->created_at }}</td>
                            <td>{{ $category->updated_at }}</td>

                            <td class="whitespace-nowrap text-right">
                                <button type="button"
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit"
                                    aria-label="Edit category"
                                    @click="mode = 'edit'; action = '{{ route('admin.categories.update', $category) }}'; form = {{ \Illuminate\Support\Js::from(['name' => $category->name, 'slug' => $category->slug, 'icon' => $category->icon ?? '', 'description' => $category->description ?? '']) }}; open = true">
                                    <x-admin.icon name="edit" class="size-4" />
                                </button>
                                <x-admin.delete-form :action="route('admin.categories.destroy', $category)"
                                    confirm="Delete this category?" label="Delete category">
                                    <x-admin.icon name="trash" class="size-4" />
                                </x-admin.delete-form>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.table>
            @endif
        </x-admin.card>

        <x-admin.dialog show="open" close="open = false" title="mode === 'add' ? 'Add category' : 'Edit category'">
            <form method="POST" :action="action" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">
                <input type="hidden" name="_modal" value="1">
                <x-admin.input id="cat_name" name="name" label="Name" x-model="form.name" required />
                <x-admin.input id="cat_slug" name="slug" label="Slug" x-model="form.slug"
                    hint="Empty = made from the name" />
                <x-admin.input id="cat_icon" name="icon" label="Icon (optional)" x-model="form.icon" />
                <x-admin.textarea id="cat_desc" name="description" label="Description" x-model="form.description"
                    rows="3" />
                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <x-admin.button type="button" variant="secondary" @click="open = false">Cancel</x-admin.button>
                    <x-admin.button type="submit">Save</x-admin.button>
                </div>
            </form>
        </x-admin.dialog>
    </div>
</x-admin.layout>