<x-admin.layout :title="$course->title">
    <div x-data="{
            tab: 'overview',
            tabs: ['overview', 'curriculum', 'pricing', 'extras', 'reviews'],
            init() {
                let t = location.hash.slice(1);
                if (! this.tabs.includes(t)) { try { t = sessionStorage.getItem('courseTab{{ $course->id }}'); } catch (e) { t = null; } }
                this.tab = this.tabs.includes(t) ? t : 'overview';
                window.addEventListener('hashchange', () => { const h = location.hash.slice(1); if (this.tabs.includes(h)) this.tab = h; });
            },
            go(t) { this.tab = t; history.replaceState(null, '', '#' + t); try { sessionStorage.setItem('courseTab{{ $course->id }}', t); } catch (e) {} },
         }">

        <a href="{{ route('admin.courses.index') }}"
            class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-800">
            <x-admin.icon name="arrow-left" class="size-4" /> All courses
        </a>

        {{-- header --}}
        <div
            class="mb-6 flex flex-col gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5 sm:flex-row sm:items-center sm:p-5">
            <div class="aspect-video w-full shrink-0 overflow-hidden rounded-lg bg-slate-100 sm:w-48">
                @if ($course->thumbnail_url)
                    <img src="{{ $course->thumbnail_url }}" alt="" class="size-full object-cover">
                @else
                    <div
                        class="flex size-full items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600 text-white/90">
                        <x-admin.icon name="book" class="size-10" />
                    </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <x-admin.status-badge :status="$course->status" />
                    @unless ($course->is_active) <x-admin.badge color="gray">Inactive</x-admin.badge> @endunless
                    @if ($course->level) <x-admin.badge color="indigo"
                    class="capitalize">{{ $course->level }}</x-admin.badge> @endif
                </div>
                <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $course->title }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $course->category?->name ?? 'No category' }} ·
                    /{{ $course->slug }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <x-admin.button :href="route('admin.courses.edit', $course)" variant="secondary">
                    <x-admin.icon name="edit" class="size-4" /> Edit
                </x-admin.button>
                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                    data-confirm="Delete this course? Students who already bought it keep their access data."
                    onsubmit="return confirm(this.dataset.confirm)">
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
                'curriculum' => ['Curriculum', $course->curricula->count()],
                'pricing' => ['Price & installments', null],
                'extras' => ['Outcomes & FAQ', $course->learningOutcomes->count() + $course->faqs->count()],
                'reviews' => ['Reviews', $course->reviews->count()],
            ];
        @endphp
        <div class="mb-6  border-b border-slate-200">
            <nav class="-mb-px flex gap-6" overflow-x-auto aria-label="Course sections">
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

        <div x-show="tab === 'overview'" x-cloak>@include('admin.courses.partials.overview')</div>
        <div x-show="tab === 'curriculum'" x-cloak>@include('admin.courses.partials.curriculum')</div>
        <div x-show="tab === 'pricing'" x-cloak>@include('admin.courses.partials.pricing')</div>
        <div x-show="tab === 'extras'" x-cloak>@include('admin.courses.partials.extras')</div>
        <div x-show="tab === 'reviews'" x-cloak>@include('admin.courses.partials.reviews')</div>
    </div>
</x-admin.layout>