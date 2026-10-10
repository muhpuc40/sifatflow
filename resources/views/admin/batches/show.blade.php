<x-admin.layout :title="$batch->name">
    <div x-data="{
            tab: 'overview',
            tabs: ['overview', 'classes', 'exams', 'assignments'],
            init() {
                let t = location.hash.slice(1);
                if (! this.tabs.includes(t)) { try { t = sessionStorage.getItem('batchTab{{ $batch->id }}'); } catch (e) { t = null; } }
                this.tab = this.tabs.includes(t) ? t : 'overview';
                window.addEventListener('hashchange', () => { const h = location.hash.slice(1); if (this.tabs.includes(h)) this.tab = h; });
            },
            go(t) { this.tab = t; history.replaceState(null, '', '#' + t); try { sessionStorage.setItem('batchTab{{ $batch->id }}', t); } catch (e) {} },
         }">

        <a href="{{ route('admin.batches.index') }}"
            class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-800">
            <x-admin.icon name="arrow-left" class="size-4" /> All batches
        </a>

        {{-- header --}}
        <div
            class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:flex-row sm:items-center sm:p-5">
            <div class="aspect-video w-full shrink-0 overflow-hidden rounded-lg bg-slate-100 sm:w-48">
                @if ($batch->course?->thumbnail_url)
                    <img src="{{ $batch->course->thumbnail_url }}" alt="" class="size-full object-cover">
                @else
                    <div
                        class="flex size-full items-center justify-center bg-gradient-to-br from-sky-500 to-indigo-600 text-white/90">
                        <x-admin.icon name="layers" class="size-10" />
                    </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <x-admin.state-badge :status="$batch->status" />
                    <x-admin.badge color="indigo">{{ $batch->code }}</x-admin.badge>
                    @unless ($batch->is_active) <x-admin.badge color="gray">Inactive</x-admin.badge> @endunless
                </div>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $batch->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">
                    @if ($batch->course)
                        <a href="{{ route('admin.courses.show', $batch->course) }}"
                            class="hover:text-indigo-600 hover:underline">{{ $batch->course->title }}</a>
                    @else
                        Course deleted
                    @endif
                    · Starts {{ $batch->start_date->format('d M Y') }}
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <x-admin.button :href="route('admin.batches.edit', $batch)" variant="secondary">
                    <x-admin.icon name="edit" class="size-4" /> Edit
                </x-admin.button>
                <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}"
                    data-confirm="Delete this batch?" onsubmit="return confirm(this.dataset.confirm)">
                    @csrf @method('DELETE')
                    <x-admin.button type="submit" variant="danger"><x-admin.icon name="trash" class="size-4" />
                        Delete</x-admin.button>
                </form>
            </div>
        </div>

        {{-- tabs --}}
        @php
            $tabList = [
                'overview' => ['Overview', null],
                'classes' => ['Class schedule', $batch->classes->count()],
                'exams' => ['Exams', $batch->exams->count()],
                'assignments' => ['Assignments', $batch->assignments->count()],
            ];
        @endphp
        <div class="mb-6  border-b border-slate-200">
            <nav class="-mb-px overflow-x-auto flex gap-6" aria-label="Batch sections">
                @foreach ($tabList as $key => [$label, $count])
                    <button type="button" @click="go('{{ $key }}')"
                        :class="tab === '{{ $key }}' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'"
                        class="flex items-center gap-2 whitespace-nowrap border-b-2 px-1 pb-3 text-sm font-medium transition">
                        {{ $label }}
                        @if ($count !== null)
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $count }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>

        <div x-show="tab === 'overview'" x-cloak>@include('admin.batches.partials.overview')</div>
        <div x-show="tab === 'classes'" x-cloak>@include('admin.batches.partials.classes')</div>
        <div x-show="tab === 'exams'" x-cloak>@include('admin.batches.partials.exams')</div>
        <div x-show="tab === 'assignments'" x-cloak>@include('admin.batches.partials.assignments')</div>
    </div>
</x-admin.layout>