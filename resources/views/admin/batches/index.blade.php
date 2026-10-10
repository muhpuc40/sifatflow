<x-admin.layout title="Batches">
    <x-admin.page-header title="Batches" description="Every run of a course. Open one to manage its classes, exams and assignments.">
        <x-admin.button :href="route('admin.batches.create')">
            <x-admin.icon name="plus" class="size-4" /> New batch
        </x-admin.button>
    </x-admin.page-header>

    {{-- filters --}}
    <form method="GET" action="{{ route('admin.batches.index') }}" class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_16rem_10rem_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name or code..."
               class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600">

        <select name="course" class="block w-full rounded-lg border-0 bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600">
            <option value="">All courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" @selected((string) request('course') === (string) $course->id)>{{ $course->title }}</option>
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
            @if (request()->hasAny(['q', 'course', 'status']))
                <x-admin.button :href="route('admin.batches.index')" variant="secondary">Reset</x-admin.button>
            @endif
        </div>
    </form>

    @if ($batches->isEmpty())
        <x-admin.card>
            <x-admin.empty-state icon="layers"
                :title="request()->hasAny(['q', 'course', 'status']) ? 'No batch matches your filter' : 'No batches yet'"
                message="Create a batch for a course, then add its class schedule, exams and assignments.">
                <x-admin.button :href="route('admin.batches.create')"><x-admin.icon name="plus" class="size-4" /> New batch</x-admin.button>
            </x-admin.empty-state>
        </x-admin.card>
    @else
        {{-- 3 cards in a row on a wide screen --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($batches as $batch)
                <article class="group flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-slate-900/5 transition hover:shadow-md">
                    <a href="{{ route('admin.batches.show', $batch) }}" class="relative block aspect-[16/7] overflow-hidden bg-slate-100">
                        @if ($batch->course?->thumbnail_url)
                            <img src="{{ $batch->course->thumbnail_url }}" alt="" loading="lazy"
                                 class="size-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <div class="flex size-full items-center justify-center bg-gradient-to-br from-sky-500 to-indigo-600 text-white/90">
                                <x-admin.icon name="layers" class="size-10" />
                            </div>
                        @endif
                        <span class="absolute left-3 top-3"><x-admin.state-badge :status="$batch->status" class="bg-white/90" /></span>
                        @unless ($batch->is_active)
                            <span class="absolute right-3 top-3"><x-admin.badge color="gray">Inactive</x-admin.badge></span>
                        @endunless
                    </a>

                    <div class="flex flex-1 flex-col p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-indigo-600">{{ $batch->code }}</p>
                        <h3 class="mt-1 line-clamp-2 text-base font-semibold text-slate-900">
                            <a href="{{ route('admin.batches.show', $batch) }}" class="hover:text-indigo-600">{{ $batch->name }}</a>
                        </h3>
                        <p class="mt-0.5 line-clamp-1 text-sm text-slate-500">{{ $batch->course?->title ?? 'Course deleted' }}</p>

                        <dl class="mt-4 space-y-1.5 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Starts</dt>
                                <dd class="font-medium text-slate-900">{{ $batch->start_date->format('d M Y') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Enroll until</dt>
                                <dd class="font-medium text-slate-900">
                                    {{ $batch->lastEnrollDate()?->format('d M Y') ?? 'Open' }}
                                    @if ($batch->isEnrollmentOpen())
                                        <span class="ml-1 text-xs font-normal text-emerald-600">open</span>
                                    @else
                                        <span class="ml-1 text-xs font-normal text-slate-400">closed</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Instructor</dt>
                                <dd class="truncate font-medium text-slate-900">{{ $batch->instructor?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Seats</dt>
                                <dd class="font-medium text-slate-900">{{ $batch->seat_limit ?? 'Unlimited' }}</dd>
                            </div>
                        </dl>

                        <dl class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Classes</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $batch->classes_count }}</dd>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Exams</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $batch->exams_count }}</dd>
                            </div>
                            <div class="rounded-lg bg-slate-50 px-2 py-2">
                                <dt class="text-slate-500">Assignments</dt>
                                <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $batch->assignments_count }}</dd>
                            </div>
                        </dl>

                        <div class="mt-auto flex items-center justify-end gap-1 border-t border-slate-100 pt-4 mt-4">
                            <x-admin.button :href="route('admin.batches.edit', $batch)" variant="ghost" size="sm" aria-label="Edit" title="Edit">
                                <x-admin.icon name="edit" class="size-4" />
                            </x-admin.button>
                            <x-admin.button :href="route('admin.batches.show', $batch)" variant="secondary" size="sm">Details</x-admin.button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $batches->links() }}</div>
    @endif
</x-admin.layout>
