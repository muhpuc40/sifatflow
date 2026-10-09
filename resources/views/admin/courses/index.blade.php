<x-admin.layout title="Courses">
    <x-admin.page-header title="Courses" description="All courses. Open one to manage its stages, price, installments and more.">
        <!-- <x-admin.button :href="route('admin.library.content.index')" variant="secondary">
            <x-admin.icon name="video" class="size-4" /> Upload content
        </x-admin.button>
        <x-admin.button :href="route('admin.library.resources.index')" variant="secondary">
            <x-admin.icon name="folder" class="size-4" /> Upload resource
        </x-admin.button> -->
        <x-admin.button :href="route('admin.courses.create')">
            <x-admin.icon name="plus" class="size-4" /> New course
        </x-admin.button>
    </x-admin.page-header>

    {{-- filters --}}
    <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_10rem_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by title..."
               class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600">

        <select name="category" class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="status" class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
            <option value="">Any status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ ucfirst($status->value) }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <x-admin.button type="submit">Filter</x-admin.button>
            @if (request()->hasAny(['q', 'category', 'status']))
                <x-admin.button :href="route('admin.courses.index')" variant="secondary">Reset</x-admin.button>
            @endif
        </div>
    </form>

    @if ($courses->isEmpty())
        <x-admin.card>
            <x-admin.empty-state icon="book"
                :title="request()->hasAny(['q', 'category', 'status']) ? 'No course matches your filter' : 'No courses yet'"
                message="Create new course, then add its stages, modules and price.">
                <x-admin.button :href="route('admin.courses.create')"><x-admin.icon name="plus" class="size-4" /> New course</x-admin.button>
            </x-admin.empty-state>
        </x-admin.card>
    @else
        {{-- 3 cards in a row on a wide screen --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                @php($price = $course->currentPrice)
                <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:shadow-md">
                    <a href="{{ route('admin.courses.show', $course) }}" class="relative block aspect-video overflow-hidden bg-slate-100">
                        @if ($course->thumbnail_url)
                            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy"
                                 class="size-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <div class="flex size-full items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600 text-white/90">
                                <x-admin.icon name="book" class="size-12" />
                            </div>
                        @endif

                        <span class="absolute left-3 top-3"><x-admin.status-badge :status="$course->status" /></span>
                        @unless ($course->is_active)
                            <span class="absolute right-3 top-3"><x-admin.badge color="gray">Inactive</x-admin.badge></span>
                        @endunless
                    </a>

                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">{{ $course->category?->name ?? 'No category' }}</p>

                        <h3 class="mt-1 line-clamp-2 text-base font-semibold text-slate-900">
                            <a href="{{ route('admin.courses.show', $course) }}" class="hover:text-indigo-600">{{ $course->title }}</a>
                        </h3>

                        <p class="mt-1 line-clamp-2 min-h-10 text-sm text-slate-500">{{ Str::limit(strip_tags((string) $course->description), 120) ?: 'No description yet.' }}</p>

                        <dl class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Stages</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $course->curricula_count }}</dd>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Modules</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $course->modules_count }}</dd>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Level</dt>
                                <dd class="mt-0.5 text-sm font-semibold capitalize text-slate-900">{{ $course->level ?? '—' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-auto flex items-end justify-between gap-3 border-t border-slate-100 pt-4">
                            <div>
                                @if ($price)
                                    <div class="flex items-baseline gap-2">
                                    <p class="text-lg font-semibold text-slate-900">{{ number_format($price->finalPrice()) }}</p>
                                    @if ($price->finalPrice() < (float) $price->actual_price)
                                        <p class="text-xs text-slate-400 line-through">{{ number_format((float) $price->actual_price) }}</p>
                                    @endif
                                    </div>
                                @else
                                    <p class="text-sm font-medium text-amber-600">No price set</p>
                                @endif
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-500">
                                    <x-admin.icon name="star" class="size-3.5 text-amber-400" />
                                    @if ($course->approved_reviews_count)
                                        {{ number_format($course->avg_rating, 1) }} ({{ $course->approved_reviews_count }})
                                    @else
                                        No reviews
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-1">
                                <x-admin.button :href="route('admin.courses.edit', $course)" variant="ghost" size="sm" aria-label="Edit" title="Edit">
                                    <x-admin.icon name="edit" class="size-4" />
                                </x-admin.button>
                                <x-admin.button :href="route('admin.courses.show', $course)" variant="secondary" size="sm">Details</x-admin.button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $courses->links() }}</div>
    @endif
</x-admin.layout>
