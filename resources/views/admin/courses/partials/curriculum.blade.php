{{-- Stages > modules > items. One popup (dialog) per kind; the buttons fill it for "add" or "edit". --}}
@php
    $tpl = [
        'moduleStore' => route('admin.modules.store', '__ID__'),
        'itemStore' => route('admin.items.store', '__ID__'),
    ];
    $moduleNo = 0;
@endphp

<div x-data="{
        dlg: null, mode: 'add', action: '', form: {},
        tpl: {{ \Illuminate\Support\Js::from($tpl) }},
        show(kind, mode, action, form) { this.dlg = kind; this.mode = mode; this.action = action; this.form = form; },
     }">

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">A stage (for example "Beginning") groups modules. A module holds classes, exams and assignments.</p>
        <x-admin.button type="button"
            @click="show('stage', 'add', '{{ route('admin.curricula.store', $course) }}', { title: '', description: '', milestone_value: 0, duration: '', sort_order: '', is_active: true })">
            <x-admin.icon name="plus" class="size-4" /> Add stage
        </x-admin.button>
    </div>

    <div class="space-y-6">
        @forelse ($course->curricula as $stage)
            <section class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-900/5">
                <header class="flex flex-wrap items-center gap-3 border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-sm font-semibold text-white">{{ $loop->iteration }}</span>
                    <div class="min-w-0 flex-1">
                        <h3 class="flex flex-wrap items-center gap-2 font-semibold text-slate-900">
                            {{ $stage->title }}
                            @unless ($stage->is_active) <x-admin.badge color="gray">Inactive</x-admin.badge> @endunless
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $stage->modules->count() }} {{ Str::plural('module', $stage->modules->count()) }}
                            @if ($stage->duration) · {{ $stage->duration }} days @endif
                            @if ($stage->milestone_value) · milestone {{ $stage->milestone_value }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-admin.button type="button" variant="secondary" size="sm"
                            @click="show('module', 'add', tpl.moduleStore.replace('__ID__', {{ $stage->id }}), { title: '', week: '', objective: '', milestone_value: 0, sort_order: '', is_active: true })">
                            <x-admin.icon name="plus" class="size-4" /> Module
                        </x-admin.button>
                        <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit stage" aria-label="Edit stage"
                            @click="show('stage', 'edit', '{{ route('admin.curricula.update', $stage) }}', {{ \Illuminate\Support\Js::from([
                                'title' => $stage->title, 'description' => $stage->description ?? '', 'milestone_value' => $stage->milestone_value,
                                'duration' => $stage->duration ?? '', 'sort_order' => $stage->sort_order, 'is_active' => (bool) $stage->is_active,
                            ]) }})">
                            <x-admin.icon name="edit" class="size-4" />
                        </button>
                        <x-admin.delete-form :action="route('admin.curricula.destroy', $stage)" confirm="Delete this stage and hide all its modules?" label="Delete stage">
                            <x-admin.icon name="trash" class="size-4" />
                        </x-admin.delete-form>
                    </div>
                </header>

                @if ($stage->description)
                    <p class="border-b border-slate-100 px-5 py-3 text-sm text-slate-600">{{ $stage->description }}</p>
                @endif

                <div class="divide-y divide-slate-100">
                    @forelse ($stage->modules as $module)
                        @php
                            $moduleNo++;
                            $counts = $module->items->groupBy(fn ($i) => $i->item_type->value)->map->count();
                        @endphp
                        <div class="px-5 py-4">
                            <div class="flex flex-wrap items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="flex flex-wrap items-center gap-2 font-medium text-slate-900">
                                        <span class="text-slate-400">Module {{ $moduleNo }}</span> {{ $module->title }}
                                        @if ($module->week) <x-admin.badge color="blue">Week {{ $module->week }}</x-admin.badge> @endif
                                        @unless ($module->is_active) <x-admin.badge color="gray">Inactive</x-admin.badge> @endunless
                                    </h4>
                                    @if ($module->objective) <p class="mt-1 text-sm text-slate-500">{{ $module->objective }}</p> @endif
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @foreach ($itemTypes as $type)
                                            @if (($counts[$type->value] ?? 0) > 0)
                                                <x-admin.badge color="indigo">{{ $counts[$type->value] }} {{ $type->value }}</x-admin.badge>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-admin.button type="button" variant="secondary" size="sm"
                                        @click="show('item', 'add', tpl.itemStore.replace('__ID__', {{ $module->id }}), { item_type: 'recorded', objective: '', course_content_id: '', course_resource_id: '', sort_order: '', is_active: true, is_preview: false })">
                                        <x-admin.icon name="plus" class="size-4" /> Item
                                    </x-admin.button>
                                    <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit module" aria-label="Edit module"
                                        @click="show('module', 'edit', '{{ route('admin.modules.update', $module) }}', {{ \Illuminate\Support\Js::from([
                                            'title' => $module->title, 'week' => $module->week ?? '', 'objective' => $module->objective ?? '',
                                            'milestone_value' => $module->milestone_value, 'sort_order' => $module->sort_order, 'is_active' => (bool) $module->is_active,
                                        ]) }})">
                                        <x-admin.icon name="edit" class="size-4" />
                                    </button>
                                    <x-admin.delete-form :action="route('admin.modules.destroy', $module)" confirm="Delete this module and all its items?" label="Delete module">
                                        <x-admin.icon name="trash" class="size-4" />
                                    </x-admin.delete-form>
                                </div>
                            </div>

                            @if ($module->items->isNotEmpty())
                                <div class="mt-3 overflow-hidden rounded-lg ring-1 ring-slate-200">
                                    <x-admin.table>
                                        <x-slot:head>
                                            <th>#</th><th>Type</th><th>Content</th><th>Resource</th><th>Flags</th><th class="text-right">Actions</th>
                                        </x-slot:head>
                                        @foreach ($module->items as $item)
                                            <tr>
                                                <td class="text-slate-400">{{ $loop->iteration }}</td>
                                                <td>
                                                    <x-admin.badge :color="match ($item->item_type->value) { 'exam' => 'red', 'assignment' => 'amber', 'live' => 'green','resources'=>'black', default => 'indigo' }" class="capitalize">{{ $item->item_type->value }}</x-admin.badge>
                                                    @if ($item->objective) <p class="mt-1 max-w-xs truncate text-xs text-slate-500" title="{{ $item->objective }}">{{ $item->objective }}</p> @endif
                                                </td>
                                                <td>{{ $item->content?->name ?? '—' }}</td>
                                                <td>{{ $item->resource?->name ?? '—' }}</td>
                                                <td class="space-x-1">
                                                    @if ($item->is_preview) <x-admin.badge color="green">Free preview</x-admin.badge> @endif
                                                    @unless ($item->is_active) <x-admin.badge color="gray">Inactive</x-admin.badge> @endunless
                                                </td>
                                                <td class="whitespace-nowrap text-right">
                                                    <button type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit item" aria-label="Edit item"
                                                        @click="show('item', 'edit', '{{ route('admin.items.update', $item) }}', {{ \Illuminate\Support\Js::from([
                                                            'item_type' => $item->item_type->value, 'objective' => $item->objective ?? '',
                                                            'course_content_id' => $item->course_content_id ?? '', 'course_resource_id' => $item->course_resource_id ?? '',
                                                            'sort_order' => $item->sort_order, 'is_active' => (bool) $item->is_active, 'is_preview' => (bool) $item->is_preview,
                                                        ]) }})">
                                                        <x-admin.icon name="edit" class="size-4" />
                                                    </button>
                                                    <x-admin.delete-form :action="route('admin.items.destroy', $item)" confirm="Delete this item?" label="Delete item">
                                                        <x-admin.icon name="trash" class="size-4" />
                                                    </x-admin.delete-form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </x-admin.table>
                                </div>
                            @else
                                <p class="mt-3 text-sm text-slate-400">No items yet. Add a recorded class, a live class, an exam or an assignment.</p>
                            @endif
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-sm text-slate-400">No modules in this stage yet.</p>
                    @endforelse
                </div>
            </section>
        @empty
            <x-admin.card>
                <x-admin.empty-state icon="layers" title="No stages yet" message="Start with a stage such as Beginning, then add its modules." />
            </x-admin.card>
        @endforelse
    </div>

    {{-- ---------------------------------------------------------- stage dialog --}}
    <x-admin.dialog show="dlg === 'stage'" close="dlg = null" title="mode === 'add' ? 'Add stage' : 'Edit stage'">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">

            <x-admin.input id="stage_title" name="title" label="Stage name" x-model="form.title" placeholder="Beginning" required />
            <x-admin.textarea id="stage_description" name="description" label="Description" x-model="form.description" rows="3" />
            <div class="grid grid-cols-3 gap-3">
                <x-admin.input id="stage_duration" name="duration" type="number" min="0" label="Days" x-model="form.duration" />
                <x-admin.input id="stage_milestone" name="milestone_value" type="number" min="0" label="Milestone" x-model="form.milestone_value" />
                <x-admin.input id="stage_order" name="sort_order" type="number" min="0" label="Order" x-model="form.sort_order" hint="Empty = last" />
            </div>
            <x-admin.checkbox id="stage_active" name="is_active" label="Active" x-model="form.is_active" />

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="dlg = null">Cancel</x-admin.button>
                <x-admin.button type="submit">Save stage</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>

    {{-- ---------------------------------------------------------- module dialog --}}
    <x-admin.dialog show="dlg === 'module'" close="dlg = null" title="mode === 'add' ? 'Add module' : 'Edit module'">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">

            <x-admin.input id="module_title" name="title" label="Module name" x-model="form.title" placeholder="HTML" required />
            <x-admin.textarea id="module_objective" name="objective" label="Objective" x-model="form.objective" rows="3" />
            <div class="grid grid-cols-3 gap-3">
                <x-admin.input id="module_week" name="week" type="number" min="0" label="Week" x-model="form.week" />
                <x-admin.input id="module_milestone" name="milestone_value" type="number" min="0" label="Milestone" x-model="form.milestone_value" />
                <x-admin.input id="module_order" name="sort_order" type="number" min="0" label="Order" x-model="form.sort_order" hint="Empty = last" />
            </div>
            <x-admin.checkbox id="module_active" name="is_active" label="Active" x-model="form.is_active" />

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="dlg = null">Cancel</x-admin.button>
                <x-admin.button type="submit">Save module</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>

    {{-- ---------------------------------------------------------- item dialog --}}
    <x-admin.dialog show="dlg === 'item'" close="dlg = null" title="mode === 'add' ? 'Add item' : 'Edit item'">
        <form method="POST" :action="action" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="1">
            <input type="hidden" name="_method" value="PUT" :disabled="mode === 'add'">

            <x-admin.select id="item_type" name="item_type" label="Type" x-model="form.item_type" required>
                @foreach ($itemTypes as $type)
                    <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                @endforeach
            </x-admin.select>
            <x-admin.textarea id="item_objective" name="objective" label="Objective / short note" x-model="form.objective" rows="2" />

            <x-admin.select id="item_content" name="course_content_id" label="Content (video, exam file ...)" x-model="form.course_content_id">
                <option value="">None</option>
                @foreach ($contents as $content)
                    <option value="{{ $content->id }}">{{ $content->name }}</option>
                @endforeach
            </x-admin.select>
            <x-admin.select id="item_resource" name="course_resource_id" label="Resource (slides, PDF ...)" x-model="form.course_resource_id">
                <option value="">None</option>
                @foreach ($resources as $resource)
                    <option value="{{ $resource->id }}">{{ $resource->name }}</option>
                @endforeach
            </x-admin.select>
            <p class="-mt-2 text-xs text-slate-500">
                Not in the list? <a href="{{ route('admin.library.content.index') }}" class="text-indigo-600 underline">Upload content</a> or
                <a href="{{ route('admin.library.resources.index') }}" class="text-indigo-600 underline">upload a resource</a> first.
            </p>

            <x-admin.input id="item_order" name="sort_order" type="number" min="0" label="Order" x-model="form.sort_order" hint="Empty = last" />
            <div class="flex flex-wrap gap-x-6 gap-y-2">
                <x-admin.checkbox id="item_active" name="is_active" label="Active" x-model="form.is_active" />
                <x-admin.checkbox id="item_preview" name="is_preview" label="Free preview" x-model="form.is_preview" />
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-admin.button type="button" variant="secondary" @click="dlg = null">Cancel</x-admin.button>
                <x-admin.button type="submit">Save item</x-admin.button>
            </div>
        </form>
    </x-admin.dialog>
</div>
